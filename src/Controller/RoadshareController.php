<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Form\CompteType;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UtilisateurRepository;
use Psr\Container\ContainerInterface;


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

    /**
     * @Route("/storeUser", name="storeUser")
     * @param Request $manager
     * @return Response
     */
    public function storeUser(Request $manager): Response
   {
       //je crée un un utilisateur
       $utilisateur= new Utilisateur();
       $compte= new Compte();

        //je recupère le formulaire new user
      $form = $this->createForm(UtilisateurType::class,$utilisateur);
      $form->handleRequest($manager);//symfony va faire le lien entre les données des champs du formulaire et la variable $utilisateur

       $form1=$this->cretaeFrorm(CompteType::class,$compte);
       $form1->handleRequest($manager);

       if($form->isSubmitted() && $form1->isSubmitted())
       {
           //enregistrement du nv utilisateur
           $manager = $this->getDoctrine()->getManager();
           $manager->persist($utilisateur);
           $manager->flush();
           return new Response('compte crée');
       }
       return $this->render('roadshare/index.html.twig', [
           'controller_name' => 'RoadshareController',
       ]);
   }

   /*public function UserConnected(ObjectManager $manager): Response
   {

   }*/

}
