<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @Route("/roadshare/inscription", name="roadshare_inscription")
     */
    public function inscription(Request $request, ObjectManager $manager): Response
    {
        $utilisateur = new Utilisateur;
        $compte = new Compte;

        $formUtilisateur = $this->createForm(UtilisateurType::class, $utilisateur);
        $formUtilisateur->handleRequest($request);

        $formCompte = $this->createForm(UtilisateurType::class, $compte);
        $formCompte->handleRequest($request);

        if(($formUtilisateur->isSubmitted() && $formUtilisateur->isValid()) && ($formCompte->isSubmitted() && $formCompte->isValid())){
            $utilisateur->setCompte($compte);
            $manager->persist($compte);
            $manager->persist($utilisateur);
            $manager->flush();
            return $this->redirectToRoute('roadshare_profil', [
                'id' => $compte->getId()
            ]);
        }
        return $this->render('roadshare/inscription.html.twig', [
            'formUtilisateur' => $formUtilisateur->createView(),
            'formCompte' => $formCompte->createView()
        ]);
    }

     /**
     * @Route("/redirection", name="redirection")
     */
    public function Redirection(): Response
    {
        return $this->render('roadshare/redirection.html.twig');
    }

}
