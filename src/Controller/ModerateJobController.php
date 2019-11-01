<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Job;
use App\Repository\JobRepository;

class ModerateJobController extends AbstractController
{
    /**
     * @Route("/moderate/job/{slug}", name="moderate_job")
     */
    public function index($slug, JobRepository $jobRepository)
    {
        // TODO decrypt slug

        $job = $jobRepository->findOneBySlug($slug);
        if ($job) {
            // TODO update status
            $job->setStatus(2);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($job);
            $entityManager->flush();
            return $this->render('moderate_job/index.html.twig', [
                'controller_name' => 'ModerateJobController',
            ]);
        }
        return false;
    }
}
