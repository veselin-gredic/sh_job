<?php
/**
 * Created by PhpStorm.
 * User: gredicv
 * Date: 11/3/19
 * Time: 7:45 PM
 */

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController  extends AbstractController
{
    /**
     * @Route("/", name="home_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', []);
    }
}