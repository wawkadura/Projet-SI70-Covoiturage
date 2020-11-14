<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Entity\Trajet;
use App\Entity\AdressePostale;
use App\Entity\Voiture;
use App\Entity\Entreprise;
use App\Entity\Description;
use App\Entity\InformationTravail;
use App\Form\TravailType;
use App\Form\VoitureType;
use App\Form\DescriptionType;
use App\Form\EntrepriseType;
use App\Form\PropositionType;
use App\Form\CompteType;
use App\Form\InscriptionFormType;
use App\Form\UtilisateurType;
use App\Form\TrajetType;
use App\Repository\AdressePostaleRepository;
use App\Repository\DescriptionRepository;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use App\Repository\UtilisateurRepository;
use DateTime;
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
        $adressePostale = new AdressePostale;
        $description = new Description;

        $formData['utilisateur'] = $utilisateur;
        $formData['compte']  = $compte;
        $formData['adressePostale']  = $adressePostale;
        $formData['description']  = $description;

        $form = $this->createForm(InscriptionFormType::class, $formData);
        $form->handleRequest($request);
    
        if(($form['compte']->isSubmitted() && $form['compte']->isValid()) 
            &&($form['utilisateur']->isSubmitted() && $form['utilisateur']->isValid())
            && ($form['adressePostale']->isSubmitted() && $form['adressePostale']->isValid())
            && ($form['description']->isSubmitted() && $form['description']->isValid())){

            $hash = $encoder->encodePassword($compte,$compte->getMotDePasse());
            $compte->setMotDePasse($hash);
            $manager->persist($compte);
            $utilisateur->setCompte($compte);

            $manager->persist($adressePostale);
            $utilisateur->setAdressePostale($adressePostale);

            $manager->persist($description);
            $utilisateur->setDescription($description);

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
            $trajet->setEtat('En cours');

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
    public function Recherche(Request $request, TrajetRepository $trajetRepo, UtilisateurRepository $utilisateurRepo, DescriptionRepository $descriptionRepo): Response
    {   
        $recherche = $request->request;
        $user = $this->getUser();
        if($recherche->count()>0){ 
            
            $infosEntrees = Array(); // [adresseDepart, adresseArrivee, dateDepart, heureDepart]
            $trajetsExistants = $trajetRepo->findBy(array('etat'=>'En cours'));
            $infosEntrees[0] = new AdressePostale();
            $infosEntrees[0]->setRue($recherche->get('adresseDepart'))
                            ->setVille($recherche->get('villeDepart'))
                            ->setCodePostale($recherche->get('codePostaleDepart')); 
            $infosEntrees[1] = new AdressePostale();
            $infosEntrees[1]->setRue($recherche->get('adresseArrivee'))
                            ->setVille($recherche->get('villeArrivee'))
                            ->setCodePostale($recherche->get('codePostaleArrivee'));
            $infosEntrees[2] = $recherche->get('dateDepart');
            $infosEntrees[3]= $recherche->get('heureDepart');
            $infosEntrees[4] = Array($recherche->get('fumeur')=='on', $recherche->get('animaux')=='on', $recherche->get('musique')=='on');
            $trajets = $this->Comparaison($infosEntrees,$trajetsExistants, $utilisateurRepo,$descriptionRepo);


            return $this->render('roadshare/recherche.html.twig', [
                'user' => $user,
                'recherche' => ($recherche->count()>0),
                'trajets' => $trajets,
                'infosEntrees' => $infosEntrees
            ]);
        }
        return $this->render('roadshare/recherche.html.twig', [
            'user' => $user,
            'recherche' => ($recherche->count()>0)
        ]);
    }
    public function Comparaison($infosEntrees , $trajetsExistants, $utilisateurRepo, $descriptionRepo ){
        dump($trajetsExistants);
        $adresseDepart = $infosEntrees[0];
        $adresseArrivee = $infosEntrees[1];
        $dateDepart = $infosEntrees[2];
        $heureDepart = $infosEntrees[3];
        
        $trajetNiv1= Array(); //correspondance moyenne
        $trajetNiv2= Array(); //correspondance fort
        $niv1=0;
        $niv2=0;
        foreach ($trajetsExistants as $trajet) {
            
            if($trajet->getDate()->format('Y-m-d')==$dateDepart 
            && $trajet->getHeureDepart()->format('H:i')>=$heureDepart 
            && strtolower($trajet->getAdresseDepart()->getVille())==strtolower($adresseDepart->getVille() )
            && strtolower($trajet->getAdresseArrivee()->getVille())==strtolower($adresseArrivee->getVille())
            && $this->Criteres($trajet->getConducteur()->getDescription(),$infosEntrees[4])
            ){// niveau 1 
                
                if(strtolower($trajet->getAdresseDepart()->getRue())==strtolower($adresseDepart->getRue()) 
                && strtolower($trajet->getAdresseArrivee()->getRue())==strtolower($adresseArrivee->getRue() )
                ){// niveau 2
                    $trajetNiv2[$niv2]=$trajet;
                    $niv2 = $niv2 +1;
                }
                else{
                    $trajetNiv1[$niv1]=$trajet;
                    $niv1 = $niv1 +1;
                }
                
            }
        }
        
        return $this->Combine($trajetNiv1,$trajetNiv2);
    }
    public function Combine($trajetNiv1,$trajetNiv2){ //à revoir
        $trajets = Array();
        $i = 0;
        if(!empty($trajetNiv2)){
            $trajets[$i] = $trajetNiv2;
            $i = $i+1;
        }
        if(!empty($trajetNiv1)){
            $trajets[$i] = $trajetNiv1;
            $i = $i+1;
        }
        return $trajets;
    }

    public function Criteres($description,$criteres){

        if($criteres[0] && !$description->getVoyagerAvecFumeur() // si true et false
        || $criteres[1] && !$description->getVoyagerAvecAnimaux() // si true et false
        || $criteres[2] && !$description->getVoyagerAvecMusique()){ // si true et true
            return False;
        }
        return true;
    }

    /**
     * @Route("/trajet/{id}", name="roadshare_trajet")
     */
    public function Trajet($id,ReservationRepository $reservationRepo, TrajetRepository $trajetRepo, UtilisateurRepository $utilisateurRepo){
        $user = $this->getUser();
        $reservation = false;
        $utilisateur = $utilisateurRepo->findBy(array("compte" => $user->getId()))[0];
        $trajet = $trajetRepo->findOneBy(array('id'=>$id ));
        $reservations = $reservationRepo->findBy(array('trajet'=>$trajet->getId()));
        if(!empty($reservations)){
            foreach ($reservations as  $res) {
                if($res->getDemandeur()->getId()==$utilisateur->getId()){
                    $reservation=true;
                }
            }
        }
        return $this->render('roadshare/trajet.html.twig', [
            'user' => $user,
            'trajet' => $trajet,
            'reservation' => $reservation,
            'owner' => $utilisateur->getId() == $trajet->getConducteur()->getId()
        ]);
    }
    /**
     * @Route("/reservation/{id}", name="roadshare_reservation")
     */
    public function Reservation($id): Response
    {
        // a faire
        return $this->redirectToRoute('roadshare_trajet',array('id'=>$id ));
    }

    /**
     * @Route("/profil", name="roadshare_profil")
     */
    public function Profil(UtilisateurRepository $repo){
        $user = $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];
        $description= $utilisateur->getDescription();

        $voiture= $utilisateur->getVoiture();
        $informationTravail= $utilisateur->getInformationTravail();
        $compte=$utilisateur->getCompte();
        
        if (!isset($entreprise)){
            return $this->render('roadshare/profil.html.twig', [
                'user' => $user,
                'utilisateur' => $utilisateur,
                'description'=>$description,
                'voiture'=>$voiture,
                'informationTravail'=>$informationTravail,
                'compte'=>$compte
            ]);
        }
        else {
            $entreprise= $informationTravail->getEntreprise();
            return $this->render('roadshare/profil.html.twig', [
                'user' => $user,
                'utilisateur' => $utilisateur,
                'description'=>$description,
                'voiture'=>$voiture,
                'informationTravail'=>$informationTravail,
                'compte'=>$compte,
                'entreprise'=>$entreprise
            
            ]);
        }    

   
    }

    /**
     * @Route("/setinformation", name="roadshare_setinformation") 
    */
    public function setInformation(Request $request,ObjectManager $manager,UtilisateurRepository $repo){
    
        
        $user = $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];

        $voiture= $utilisateur->getVoiture();
        $description= $utilisateur->getDescription();
        $informationTravail= $utilisateur->getInformationTravail();
        $compte= $utilisateur->getCompte();
        
        if(!isset($informationTravail )){
            $informationTravail = new InformationTravail();
            $entreprise = new Entreprise();
            $adressePostaleEntreprise= new AdressePostale;
        }
        else   {
            $entreprise=$informationTravail->getEntreprise();
            $adressePostaleEntreprise= $entreprise->getAdressePostale();
        }

        if(!isset($voiture)){
            $voiture = new Voiture();
        }
        else {$voiture= $utilisateur->getVoiture();}


        $formData['informationTravail']  =  $informationTravail;
        $formData['entreprise'] = $entreprise;

        $formVoiture = $this->createForm(VoitureType::class, $voiture);
        $formVoiture->handleRequest($request);

        $formDescription = $this->createForm(DescriptionType::class, $description);
        $formDescription->handleRequest($request);

        $formTravail = $this->createForm(TravailType::class, $formData);
        $formTravail->handleRequest($request);

        $formUtilisateur=$this->createForm(utilisateurType::class,$utilisateur);
        $formUtilisateur->handleRequest($request);

        $formCompte=$this->createForm(CompteType::class,$compte);
        $formCompte->handleRequest($request);

        if(($formVoiture->isSubmitted() && $formVoiture->isValid())){
            $utilisateur->setVoiture($voiture);

            $manager->persist($voiture);
            $manager->flush();
           
        }
        if(($formDescription->isSubmitted() && $formDescription->isValid())){
            $utilisateur->setDescription($description);

            dump($formDescription);
            $manager->persist($description);
            $manager->flush();

        }
        if(($formTravail['entreprise']->isSubmitted() && $formTravail['entreprise']->isValid()) && 
        ($formTravail['informationTravail']->isSubmitted() && $formTravail['informationTravail']->isValid())){

            $informationTravail->setEntreprise($entreprise);
            $entreprise->setAdressePostale($adressePostaleEntreprise);
            $utilisateur->setInformationTravail($informationTravail);

            $manager->persist($entreprise);
            $manager->persist($informationTravail);
            $manager->persist($utilisateur);
            $manager->flush();

        }
        if(($formUtilisateur->isSubmitted() && $formUtilisateur->isValid())){

            $manager->persist($utilisateur);
            $manager->flush();
        }
        if(($formCompte->isSubmitted() && $formCompte->isValid())){
            $utilisateur->setCompte($compte);
            
            $manager->persist($compte);
            $manager->flush();
        }
        return $this->render('roadshare/informations.html.twig', [
            'formVoiture' => $formVoiture->createView(),
            'formDescription' => $formDescription->createView(),
            'formTravail' => $formTravail->createView(),
            'formUtilisateur' => $formUtilisateur->createView(),
            'formCompte' => $formCompte->createView(),
            'user' => $user
        ]);
    }

}
