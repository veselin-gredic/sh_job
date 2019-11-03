<?php

namespace App\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Job;
use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Request;

class ModerateJobController extends AbstractController
{
    private $jobRepository;
    private $logger;

    /**
     *
     * ModerateJobController constructor.
     *
     * @param JobRepository $jobRepository
     * @param LoggerInterface $loger
     */
    public function __construct(JobRepository $jobRepository, LoggerInterface $loger)
    {
        $this->jobRepository = $jobRepository;
        $this->logger = $loger;
    }

    /**
     * @Route("/moderate/job/{slug}", name="moderate_job")
     */
    public function index($slug, Request $request)
    {
        // TODO decrypt
        try {
            $job = $this->jobRepository->findOneBySlug($slug);
            $status = $request->query->get('status');
            if ($status==2 || $status==3) {
                $this->updatestatus($job, intval($status));
            }

            return $this->render('moderate_job/index.html.twig', [
                'controller_name' => 'ModerateJobController',
            ]);
        } catch (NonUniqueResultException $e) {
            $this->logger->error($e->getMessage());
        }
        return $this->render('moderate_job/fail.html.twig', [
            'controller_name' => 'ModerateJobController',
        ]);
    }

    /**
     * @param $job
     * @param $status
     */
    public function updatestatus($job, $status) {
        $job->setStatus($status);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($job);
        $entityManager->flush();
        $this->logger->info('Job is updated with status ->'.$status.' Object :'. print_r($job));
    }
}
