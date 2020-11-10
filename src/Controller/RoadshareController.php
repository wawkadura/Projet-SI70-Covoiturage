<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Entity\Trajet;
use App\Entity\AdressePostale;
use App\Entity\Voiture;
use App\Entity\Criteres;
use App\Form\VoitureType;
use App\Form\CriteresType;
use App\Form\PropositionType;
use App\Form\CompteType;
use App\Form\InscriptionFormType;
use App\Form\UtilisateurType;
use App\Form\TrajetType;
use App\Repository\AdressePostaleRepository;
use App\Repository\TrajetRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


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
     * @Route("/proposition", name="roadshare_proposition")
     */
    public function Proposition(Request $request, ObjectManager $manager,UtilisateurRepository $repo): Response
    {
        $trajet = new Trajet();
        $adresseDepart = new AdressePostale();
        $adresseArrivee = new AdressePostale();
        
        $formData['adresseDepart']  = $adresseDepart;
        $formData['adresseArrivee']  =  $adresseArrivee;
        $formData['trajet'] = $trajet;
      

        $form = $this->createForm(PropositionType::class, $formData);
        $form->handleRequest($request);

        $datenow = new \DateTime("now");
        sleep(2);
        $date=$trajet->getDate();
        if(($form['trajet']->isSubmitted() && $form['trajet']->isValid()) && 
            ($form['adresseDepart']->isSubmitted() && $form['adresseDepart']->isValid()) && 
            ($form['adresseArrivee']->isSubmitted() && $form['adresseArrivee']->isValid()) &&($date>$datenow)){

            $user = $this->getUser();
            $conducteur = $repo->findBy(array("compte" => $user->getId()));
            $trajet->setConducteur($conducteur[0]);
            $trajet->setEtat('en cours');

            $trajet->setAdresseDepart($adresseDepart);
            $trajet->setAdresseArrivee($adresseArrivee);    
            $manager->persist($trajet);
            $manager->persist($adresseDepart);
            $manager->persist($adresseArrivee);
            $manager->flush();
            return $this->redirectToRoute('roadshare_home');
        }
        $user = $this->getUser();
        return $this->render('roadshare/proposition.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
  
    /**
     * @Route("/recherche", name="roadshare_recherche")
     */
    public function Recherche(Request $request, TrajetRepository $trajetRepo, AdressePostaleRepository $adresseRepo): Response
    {   
        $recherche = $request->request;
        $user = $this->getUser();
        if($recherche->count()>0){
            $adresseDepart = new AdressePostale();
            $adresseDepart->setRue($recherche->get('adresseDepart'))
                            ->setVille($recherche->get('villeDepart'))
                            ->setCodePostale($recherche->get('codePostaleDepart')); 
            $adresseArrivee = new AdressePostale();
            $adresseArrivee->setRue($recherche->get('adresseArrivee'))
                            ->setVille($recherche->get('villeArrivee'))
                            ->setCodePostale($recherche->get('codePostaleArrivee'));
            $dateDepart = $recherche->get('dateDepart');
            $heureDepart = $recherche->get('heureDepart');

            $adressesTrouver = $adresseRepo->findBy(array('codePostale'=>$adresseDepart->getCodePostale()));
            
        }
        return $this->render('roadshare/recherche.html.twig', [
            'user' => $user,
            'recherche' => ($recherche->count()>0)
        ]);
    }

    /**
     * @Route("/profil", name="roadshare_profil")
     */
    public function Profil(UtilisateurRepository $repo){
        $user = $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()));
       
        return $this->render('roadshare/profil.html.twig', [
            'user' => $user,
            'utilisateur' => $utilisateur[0],
        ]);
    }

    /**
     * @Route("/modification", name="roadshare_get_modification")
     */
    public function getIformation(){
        $user = $this->getUser();
        $voitures= $this->getDoctrine()->getRepository(Voiture::class)->findBy(array("compte" => $user->getId()))[0];
        $criteres= $this->getDoctrine()->getRepository(Voiture::class)->findBy(array("compte" => $user->getId()))[0];

        return $this->render('roadshare/informations.html.twig', [
            'voitures'=>$voitures,
            'criteres'=>$criteres,
            'user' => $user
        ]);
    }

 
    /**
     * @Route("/edit/{id}", name="roadshare_set_information")
     * @ParamConverter("id", class="Criteres" options={"id":"criteres"}) //il ya un erreur a ce niveau.
    */
    public function setmodification(Criteres $criteres,Voiture $voiture,Request $request,ObjectManager $manager,UtilisateurRepository $repo){
        //$voiture = new voiture();
        //criteres= new Criteres();
        
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        $form1 = $this->createForm(CriteresType::class, $criteres);
        $form1->handleRequest($request);

        if(($form->isSubmitted() && $form->isValid())){
            $user = $this->getUser();
            $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];
            $utilisateur->setVoiture($voiture);

            $manager->persist($voiture);
            $manager->flush();
        }

        if(($form1->isSubmitted() && $form1->isValid())){
            $user = $this->getUser();
            $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];
            $utilisateur->setCritres($criteres);

            $manager->persist($criteres);
            $manager->flush();
        }

        $user = $this->getUser();
        return $this->render('roadshare/informations.html.twig', [
            'form' => $form->createView(),
            'form1' => $form1->createView(),
            'user' => $user
        ]);
    }


}
