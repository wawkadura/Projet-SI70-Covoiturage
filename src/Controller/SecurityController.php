<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Compte;
use App\Form\CompteType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;


class SecurityController extends AbstractController
{

    /**
     * @Route("/storeUser", name="storeUser")
     * @param Request $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function storeUser(Request $manager,UserPasswordEncoderInterface $encoder): Response
    {

        //je crée un un utilisateur
        $utilisateur= new Utilisateur();
        $compte= new Compte();


        $email= $manager->get('email');
        $password = $manager->get('password');

        $nom = $manager->get('nom');
        $prenom = $manager->get('prenom');
        $telephone = $manager->get('telephone');
        $datedenaissance = $manager->get('datedenaissance');

        $compte->setEmail($email);
        $compte->setPassword($password);

        //hasher un mot de passe
        $hash =$encoder->encodePassword($compte,$compte->getPassword());
        $compte->getPassword($hash);

        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setTelephone($telephone);
        $utilisateur->setDatedenaissance(new \DateTime($datedenaissance));



        //je recupère le formulaire new user
        $form = $this->createForm(UtilisateurType::class,$utilisateur);
        $form->handleRequest($manager);//symfony va faire le lien entre les données des champs du formulaire et la variable $utilisateur
        $form1 = $this->createForm(CompteType::class,$compte);
        $form1->handleRequest($manager);

            //enregistrement du nv utilisateur
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($utilisateur);
            $manager->persist($compte);
            $manager->flush();

            return $this->redirectToRoute('login');

    }

    /**
     * @Route("/login", name="login")
     */
    public function login(){
      return $this->render('security/login.html.twig') ;
    }

    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(){
        return $this->render('security/connexion.html.twig') ;
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(){
        return $this->render('security/inscription.html.twig') ;
    }
}
