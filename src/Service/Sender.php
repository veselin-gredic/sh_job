<?php
/**
 * Created by PhpStorm.
 * User: gredicv
 * Date: 11/1/19
 * Time: 8:42 PM
 */

namespace App\Service;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;


final class Sender implements SenderInterface
{
    public function send(Email $mail): void
    {
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
        $mailer = new Mailer($transport);
        //try
        $mailer->send($mail);
    }
}