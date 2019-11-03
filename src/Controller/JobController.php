<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\SenderInterface;
use App\Service\TwigEmailEmailRenderer;


/**
 * @Route("/job")
 */
class JobController extends AbstractController
{
    private const MODERATE = 1;
    private const PUBLISHED = 2;
    private const SPAM = 3;
    private const subject = ['Moderate','New'];


    private $jobRepository;
    private $sender;
    private $twigEmail;

    /**
     * JobController constructor.
     * @param JobRepository $jobRepository
     * @param SenderInterface $sender
     * @param TwigEmailEmailRenderer $twigEmail
     */
    public function __construct(JobRepository $jobRepository, SenderInterface $sender, TwigEmailEmailRenderer $twigEmail)
    {
        $this->jobRepository = $jobRepository;
        $this->sender = $sender;
        $this->twigEmail = $twigEmail;
    }

    /**
     * @Route("/", name="job_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('job/index.html.twig', [
            'jobs' => $this->jobRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="job_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->save($job);
            if (!$this->published($this->jobRepository,$job->getEmail())) {
                $this->sendEmail($job->getEmail(),$_ENV['GMAIL_USERNAME'],self::subject[0],$job);
                //$this->sendEmail($job->getEmail(),$_ENV['GMAIL_USERNAME'],self::subject[1],$job);
            } else {
                $job->setStatus(self::PUBLISHED);
            }

           return $this->render('job/show.html.twig', [
                'job' => $job,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_show", methods={"GET"})
     */
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="job_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Job $job): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Job $job): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($job);
            $entityManager->flush();
        }

        return $this->redirectToRoute('job_index');
    }

    /**
     * @param JobRepository $jobRepository
     * @param $email
     * @return bool
     */
    public function published(JobRepository $jobRepository, $email): bool
    {
        try {
            $results = $jobRepository->findOneByEmailPublished($email);
        } catch (NonUniqueResultException $e) {
        }
        try {
            if ($jobRepository->findOneByEmailPublished($email)) return true;
        } catch (NonUniqueResultException $e) {
        }
        return false;
    }

    /**
     * @param $job
     */
    public function save($job) {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($job);
        $entityManager->flush();
        $this->addFlash('success', 'Data was saved');
    }

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $job
     */
    public function sendEmail($from, $to, $subject, $job) {

                $emailToNewClinet = (new Email())
                    ->from($from)
                    ->to($to)
                    ->subject('Job submition')
                    ->text('You offer is under moderation!')
                    ->html('<p><h3>Welcome to SH JOB</h3></p>
                            <p>You job offer is uner moderation</p>
                            <br><p>Best requard<br>SH JOB</p>>');
                $this->sender->send($emailToNewClinet);

                $moderatorRenderedEmail = $this->twigEmail->render('emails/modarator.html.twig', [
                    'title' => $job->getTitle(),
                    'description' => $job->getDescription(),
                    'clientEmail' => $job->getEmail(),
                    'linkyes' => $_SERVER["HTTP_ORIGIN"].'/moderate/job/'.$job->getSlug().'?status=2',
                    'linkno' => $_SERVER["HTTP_ORIGIN"].'/moderate/job/'.$job->getSlug().'?status=3',
                    'expiration_date' => new \DateTime('+1 days'),
                ]);

                $emailToModerator = (new  Email())
                    ->from($from)
                    ->to($to)
                    ->subject('Moderate - New job submition')
                    ->html($moderatorRenderedEmail->body());
                $this->sender->send($emailToModerator);


    }
}
