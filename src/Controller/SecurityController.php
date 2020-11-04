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
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function storeUser(Request $request,UserPasswordEncoderInterface $encoder): Response
    {

        //je crée un un utilisateur
        $utilisateur= new Utilisateur();
        $compte= new Compte();


       /* $email= $request->get('email');
        $password = $request->get('password');

        $nom = $request->get('nom');
        $prenom = $request->get('prenom');
        $telephone = $request->get('telephone');
        $datedenaissance = $request->get('datedenaissance');

        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setTelephone($telephone);
        $utilisateur->setDatedenaissance(new \DateTime($datedenaissance));

        $compte->setEmail($email);
        $compte->SetPassword($password);*/

        //je recupère le formulaire new user
        $form = $this->createForm(UtilisateurType::class,$utilisateur);
        $form->handleRequest($request);//symfony va faire le lien entre les données des champs du formulaire et la variable $utilisateur
        $form1 = $this->createForm(CompteType::class,$compte);
        $form1->handleRequest($request);



            //enregistrement du nv utilisateur
            $manager = $this->getDoctrine()->getManager();
            dump($utilisateur);
            $manager->persist($utilisateur);
            $manager->persist($compte);
            $manager->flush();

            return $this->redirectToRoute('login_brouillon');

    }

    /**
     * @Route("/login_brouillon", name="login_brouillin")
     */
    public function login(){
      return $this->render('security/login.html.twig') ;
    }

    // /**
    //  * @Route("/inscription", name="inscription")
    //  */
    // public function inscription(){
    //     return $this->render('security/inscription.html.twig') ;
    // }
}
