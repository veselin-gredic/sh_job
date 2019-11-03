<?php
/**
 * Created by PhpStorm.
 * User: gredicv
 * Date: 11/2/19
 * Time: 6:20 PM
 */

namespace App\Service;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


final class TwigEmailEmailRenderer implements EmailRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(string $template, array $data): RenderedEmail
    {
        $data = $this->twig->mergeGlobals($data);
        try {
            $template = $this->twig->loadTemplate($template);
        } catch (LoaderError  | RuntimeError | SyntaxError $e) {

        }
        $subject = $template->renderBlock('subject', $data);
        $body = $template->renderBlock('body', $data);
        return new RenderedEmail($subject, $body);
    }
}