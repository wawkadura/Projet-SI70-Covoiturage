<?php

namespace App\Controller;

use App\Repository\CompteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/comptes", name="admin_comptes")
     */
    public function comptes(CompteRepository $repo): Response
    {
        $comptes = $repo->findAll();
        dump($comptes);
        return $this->render('admin/comptes.html.twig', [
            'comptes' => $comptes,
        ]);
    }
}
