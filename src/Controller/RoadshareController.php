<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Entity\Trajet;
use App\Entity\AdressePostale;
use App\Form\PropositionType;
use App\Form\CompteType;
use App\Form\InscriptionFormType;
use App\Form\UtilisateurType;
use App\Form\AdressepostaleType;
use App\Form\TrajetType;
use App\Repository\UtilisateurRepository;
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
     * @Route("/", name="roadshare_home")
     */
    public function home(): Response
    {
        $user = $this->getUser();

        return $this->render('roadshare/home.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/connexion", name="roadshare_connexion")
     */
    public function Connexion(): Response
    {
        return $this->render('roadshare/connexion.html.twig');
    }
    /**
     * @Route("/deconnexion", name="roadshare_deconnexion")
     */
    public function Deconnexion(){}

    /**
     * @Route("/inscription", name="roadshare_inscription")
     */
    public function Inscription(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
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
            return $this->redirectToRoute('roadshare_connexion');
        }
        return $this->render('roadshare/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }
  
    /**
     * @Route("/recherche", name="roadshare_recherche")
     */
    public function Recherche(): Response
    {
        $user = $this->getUser();
        return $this->render('roadshare/recherche.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/profil", name="roadshare_profil")
     */
    public function Profil(UtilisateurRepository $repo){
        $user = $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()));
        dump($utilisateur[0]);
        return $this->render('roadshare/profil.html.twig', [
            'user' => $user,
            'utilisateur' => $utilisateur[0]
        ]);
    }

    /**
     * @Route("/proposition", name="roadshare_proposition")
     */
    public function PropositionTrajet(Request $request, ObjectManager $manager): Response
    {
        $trajet = new Trajet();
        $adressepostale = new AdressePostale();
        
        $formData['AdressePostale']  = $adressepostale;
        $formData['trajet'] = $trajet;
      
        $form = $this->createForm(PropositionType::class, $formData);
        $form->handleRequest($request);
        if(($form['trajet']->isSubmitted() && $form['trajet']->isValid()) && 
            ($form['AdressePostale']->isSubmitted() && $form['AdressePostale']->isValid())){

            $manager->persist($trajet);
            $manager->persist($adressepostale);
            $manager->flush();
            return $this->redirectToRoute('roadshare_home');
        }
        $user = $this->getUser();
        return $this->render('roadshare/proposition.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
 
}
