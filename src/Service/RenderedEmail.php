<?php
/**
 * Created by PhpStorm.
 * User: gredicv
 * Date: 11/2/19
 * Time: 7:13 PM
 */

namespace App\Service;


final class RenderedEmail
{
    /**
     * @var string
     */
    private $subject;
    /**
     * @var string
     */
    private $body;
    public function __construct(string $subject, string $body)
    {
        $this->subject = $subject;
        $this->body = $body;
    }
    public function subject(): string
    {
        return $this->subject;
    }
    public function body(): string
    {
        return $this->body;
    }
}