<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoadshareController extends AbstractController
{
    /**
     * @Route("/roadshare", name="roadshare")
     */
    public function index(): Response
    {
        return $this->render('roadshare/index.html.twig', [
            'controller_name' => 'RoadshareController',
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('roadshare/home.html.twig');
    }
    /**
     * @Route("/newuser", name="newuser")
     */
    public function newuser(): Response
    {
        return $this->render('roadshare/newuser.html.twig');
    }
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(): Response
    {
        return $this->render('roadshare/connexion.html.twig');
    }

}
