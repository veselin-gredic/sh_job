<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use PhpParser\Node\Expr\Cast\Bool_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;

/**
 * @Route("/job")
 */
class JobController extends AbstractController
{
    private const MODERATE = 1;
    private const PUBLISHED = 2;
    private const SPAM = 3;
    /**
     * @Route("/", name="job_index", methods={"GET"})
     */
    public function index(JobRepository $jobRepository): Response
    {
        return $this->render('job/index.html.twig', [
            'jobs' => $jobRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="job_new", methods={"GET","POST"})
     */
    public function new(Request $request, JobRepository $jobRepository): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        // TODO without persitance for COS 1 new - from form event

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->published($jobRepository,$job->getEmail())) {
                $emailToClinet = $this->renderClinetMail($job->getEmail());
                $this->send($emailToClinet);
            }
            $this->save($job);

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
        if ($jobRepository->findOneByEmailPublished($email)) return true;
        return false;
    }

    /**
     * @param $email
     * @return Email
     */
    public function renderClinetMail($email):Email
    {
        // TODO make class that will redner mail from template (Twig)
        // TODO make sender const in mailer service
        $emailToNewClinet = (new Email())
            ->from($email)
            ->to($email)
            ->subject('Job submition')
            ->text('You offer is under moderation!')
            ->html('<p><h3>Welcome to SH JOB</h3></p><p>You job offer is uner moderation</p><br><p>Best requard<br>SH JOB</p>>');
        return $emailToNewClinet;
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
     * @param Email $mail
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send(Email $mail) {
        $transport = new GmailTransport('01solutions.bg@gmail.com', 'Sp@@cesky7');
        $mailer = new Mailer($transport);
        $mailer->send($mail);
        $this->addFlash('success', 'Message was send');
    }
}
