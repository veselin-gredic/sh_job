<?php
/**
 * Created by PhpStorm.
 * User: gredicv
 * Date: 11/2/19
 * Time: 6:10 PM
 */

namespace App\Service;


interface EmailRendererInterface
{
    public function render(string $template, array $data): RenderedEmail;

}