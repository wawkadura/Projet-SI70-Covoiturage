<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Form\CompteType;
use App\Form\InscriptionFormType;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RoadshareController extends AbstractController
{
   
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('roadshare/home.html.twig');
    }
    
    /**
     * @Route("/user/connected", name="user_connected")
     */
    public function Homepage(): Response
    {
        return $this->render('roadshare/pageprincipal.html.twig');
    }

    /**
     * @Route("roadshare/connexion", name="roadshare_connexion")
     */
    public function Connexion(): Response
    {
        return $this->render('security/login.html.twig');
    }
    /**
     * @Route("roadshare/deconnexion", name="roadshare_deconnexion")
     */
    public function Deconnexion(){}

      /**
     * @Route("/redirection", name="redirection")
     */
    public function Redirection(): Response
    {
        return $this->render('roadshare/redirection.html.twig');
    }

    /**
     * @Route("/new/trajet", name="new_tajet")
     */
    public function newtrajet(): Response
    {
        return $this->render('roadshare/proposition.html.twig');
    }

    /**
     * @Route("roadshare/profil/{id}", name="roadshare_profil")
     */
    public function Profil($id){
        return $this->render('roadshare/profil.html.twig');
    }
    /**
     * @Route("/roadshare/inscription", name="roadshare_inscription")
     */
    public function inscription(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $utilisateur = new Utilisateur;
        $compte = new Compte;
        
        $formData['utilisateur'] = $utilisateur;
        $formData['compte']  = $compte;

        $form = $this->createForm(InscriptionFormType::class, $formData);
        $form->handleRequest($request);

        dump($form);

        if(($form['compte']->isSubmitted() && $form['compte']->isValid()) && 
            ($form['utilisateur']->isSubmitted() && $form['utilisateur']->isValid())){

            $hash = $encoder->encodePassword($compte,$compte->getMotDePasse());
            $compte->setMotDePasse($hash);
            $manager->persist($compte);

            $utilisateur->setCompte($compte);
            $manager->persist($utilisateur);

            $manager->flush();
            return $this->redirectToRoute('user_connected');
        }
        return $this->render('roadshare/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }

 
}
