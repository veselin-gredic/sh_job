<?php
/**
 * Created by PhpStorm.
 * User: gredicv
 * Date: 11/1/19
 * Time: 8:40 PM
 */

namespace App\Service;
use Symfony\Component\Mime\Email;

interface SenderInterface
{
    //public function send(string $from, array $recipients, string $subject, string $body): void;
    public function send(Email $mail): void;
}