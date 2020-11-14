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
use App\Entity\Reservation;
use App\Form\TravailType;
use App\Form\VoitureType;
use App\Form\DescriptionType;
use App\Form\EntrepriseType;
use App\Form\PropositionType;
use App\Form\CompteType;
use App\Form\ChangePasswordType;
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
   // Etats pour les reservations 
   public const ACCEPTEE = 'ACCEPTER';
   public const EN_ATTENTE = 'EN_ATTENTE';
   public const REFUSER = 'REFUSER';

   // Etats pour les trajets 
   public const ANNULER = 'ANNULER';
   public const EN_COURS = 'EN_COURS';
   public const COMPLET = 'COMPLET';
   public const EFFECTUE = 'EFFECTUE';

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
            $trajet->setEtat(self::EN_COURS);

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
            'modification' => false,
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
        // $adresseDepart = new AdressePostale();
        // $adresseArrivee = new AdressePostale();

        if($recherche->count()>0){ 
            
            $infosEntrees = Array(); // [adresseDepart, adresseArrivee, dateDepart, heureDepart]
            $trajetsExistants = $trajetRepo->findBy(array('etat'=>self::EN_COURS));
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
        $dejaReserver = false;
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $trajet = $trajetRepo->findOneBy(array('id'=>$id ));
        $reservationsAcceptee = $reservationRepo->findBy(array('trajet'=>$trajet->getId(), 'etat' => self::ACCEPTEE));        
        $reservationsEnAttente = $reservationRepo->findBy(array('trajet'=>$trajet->getId(), 'etat' =>self::EN_ATTENTE));        
        $reservations = $reservationRepo->findBy(array('trajet'=>$trajet->getId()));        
        if(!empty($reservations)){
            foreach ($reservations as  $res) {
                if($res->getDemandeur()->getId()==$utilisateur->getId()){
                    $dejaReserver=true;
                }
            }
        }
        return $this->render('roadshare/trajet.html.twig', [
            'user' => $user,
            'trajet' => $trajet,
            'dejaReserver' => $dejaReserver,
            'reservationsAcceptee' =>$reservationsAcceptee,
            'reservationsEnAttente' =>$reservationsEnAttente,
            'owner' => $utilisateur->getId() == $trajet->getConducteur()->getId()
        ]);
    }
    /**
     * @Route("/reservation/{id}", name="roadshare_reservation")
     */
    public function Reservation($id,ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo, ObjectManager $manager): Response
    {
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $trajet = $trajetRepo->findOneBy(array("id" => $id));
        $reservation = new Reservation;
        $reservation->setDemandeur($utilisateur)
                    ->setTrajet($trajet)
                    ->setEtat(self::EN_ATTENTE);
        $manager->persist($reservation);
        $manager->flush();
        $totaleReservations = $reservationRepo->findBy(array('trajet'=>$id, 'etat'=>self::ACCEPTEE));
        if(count($totaleReservations)>= $trajet->getNbPlaces()){
            $trajet->setEtat(self::COMPLET);
            $manager->persist($trajet);
            $manager->flush();
        }

        return $this->redirectToRoute('roadshare_trajet',array('id'=>$id ));
    }
    /**
     * @Route("/reponseDemande/{id}/{accepte}", name="roadshare_reponse_demande")
     */
    public function ReponseDemande($id,$accepte,ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo, ObjectManager $manager): Response
    {
        $reservation = $reservationRepo->findOneBy(array('id'=>$id));
        if($accepte){
            $reservation->setEtat(self::ACCEPTEE);
        }else{
            $reservation->setEtat(self::REFUSER);
        }
        $manager->persist($reservation);
        $manager->flush();

        return $this->redirectToRoute('roadshare_trajet',array('id'=>$reservation->getTrajet()->getId() ));
    }

    /**
     * @Route("/modiftrajet/{id}", name="roadshare_modifierTrajet")
     */
    public function ModifierTrajet($id,TrajetRepository $repo, Request $request, ObjectManager $manager){
        $user = $this->getUser();
        $trajet = $repo->findOneBy(array("id" => $id));

        $adresseDepart = $trajet->getAdresseDepart();
        $adresseArrivee = $trajet->getAdresseArrivee();
        
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
            
            $trajet->setAdresseDepart($adresseDepart);
            $trajet->setAdresseArrivee($adresseArrivee);    
            $manager->persist($trajet);
            $manager->persist($adresseDepart);
            $manager->persist($adresseArrivee);
            $manager->flush();
            return $this->redirectToRoute('roadshare_home');
        }
        return $this->render('roadshare/proposition.html.twig', [
            'form' => $form->createView(),
            'modification' => true,
            'user' => $user
        ]);
    }

    /**
     * @Route("/profil/{id}", name="roadshare_profil_public")
     */
    public function ProfilPublic($id,UtilisateurRepository $repo){
        $user = $this->getUser();
        $utilisateur = $repo->findOneBy(array("id" => $id));
        $description= $utilisateur->getDescription();
        $voiture= $utilisateur->getVoiture();
        $informationTravail= $utilisateur->getInformationTravail();
        $compte=$utilisateur->getCompte();

        if(isset($informationTravail)){
            $entreprise= $informationTravail->getEntreprise();
        } else{
            $entreprise= new Entreprise();
        }
        return $this->render('roadshare/profil.html.twig', [
            'user' => $user,
            'owner' => false,
            'utilisateur' => $utilisateur,
            'description'=>$description,
            'voiture'=>$voiture,
            'informationTravail'=>$informationTravail,
            'compte'=>$compte,
            'entreprise'=>$entreprise
        ]);
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
        if(isset($informationTravail)){
            $entreprise= $informationTravail->getEntreprise();
        } else{
            $entreprise= new Entreprise();
        }
        return $this->render('roadshare/profil.html.twig', [
            'user' => $user,
            'owner' => true,
            'utilisateur' => $utilisateur,
            'description'=>$description,
            'voiture'=>$voiture,
            'informationTravail'=>$informationTravail,
            'compte'=>$compte,
            'entreprise'=>$entreprise
        ]);
    }

    /**
     * @Route("/setinformation", name="roadshare_setinformation") 
    */
    public function setInformation(Request $request,ObjectManager $manager,UtilisateurRepository $repo, UserPasswordEncoderInterface $encoder){
    
        
        $user = $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];

        $voiture= $utilisateur->getVoiture();
        $description= $utilisateur->getDescription();
        $informationTravail= $utilisateur->getInformationTravail();
        $compte= $utilisateur->getCompte();

        if(!isset($informationTravail )){
            $informationTravail = new InformationTravail();
            $entreprise = new Entreprise();
            $adressePostaleEntreprise= new AdressePostale();
        }
        else {
            $entreprise=$informationTravail->getEntreprise();
            $adressePostaleEntreprise= $entreprise->getAdressePostale();
        }
        $formData['entreprise'] = $entreprise;
        $formData['adressepostale'] = $adressePostaleEntreprise;
        $formData['informationTravail']  =  $informationTravail;


        if(!isset($voiture)){
            $voiture = new Voiture();
        }
        else {$voiture= $utilisateur->getVoiture();}

        $formVoiture = $this->createForm(VoitureType::class, $voiture);
        $formVoiture->handleRequest($request);

        $formDescription = $this->createForm(DescriptionType::class, $description);
        $formDescription->handleRequest($request);

        $formTravail = $this->createForm(TravailType::class, $formData);
        $formTravail->handleRequest($request);

        $formUtilisateur=$this->createForm(utilisateurType::class,$utilisateur);
        $formUtilisateur->handleRequest($request);

        $formpassword = $this->createForm(ChangePasswordType::class, $compte);
        $formpassword->handleRequest($request);

        if(($formVoiture->isSubmitted() && $formVoiture->isValid())){
            $utilisateur->setVoiture($voiture);
            $manager->persist($voiture);
            $manager->flush();
        }
        if(($formDescription->isSubmitted() && $formDescription->isValid())){
            $utilisateur->setDescription($description);
            $manager->persist($description);
            $manager->flush();
        }
        if(($formTravail['entreprise']->isSubmitted() && $formTravail['entreprise']->isValid()) && 
        ($formTravail['informationTravail']->isSubmitted() && $formTravail['informationTravail']->isValid())&& 
        ($formTravail['adressepostale']->isSubmitted() && $formTravail['adressepostale']->isValid()) ){

            $entreprise->setAdressePostale($adressePostaleEntreprise);
            $informationTravail->setEntreprise($entreprise);
            $utilisateur->setInformationTravail($informationTravail);

            $manager->persist($adressePostaleEntreprise);
            $manager->persist($entreprise);
            $manager->persist($informationTravail);
            $manager->persist($utilisateur);
            $manager->flush();

        }
        if(($formUtilisateur->isSubmitted() && $formUtilisateur->isValid())){

            $manager->persist($utilisateur);
            $manager->flush();
        }

        if ($formpassword->isSubmitted() && $formpassword->isValid()) {

            $newpassword= $formpassword->get('MotDePasse')->getData();
            $hash= $encoder->encodePassword($compte, $newpassword);
            $compte->setMotDePasse($hash);

            $manager->persist($compte);
            $manager->flush();
        }

        return $this->render('roadshare/informations.html.twig', [
            'formVoiture' => $formVoiture->createView(),
            'formDescription' => $formDescription->createView(),
            'formTravail' => $formTravail->createView(),
            'formUtilisateur' => $formUtilisateur->createView(),
            'formpassword' => $formpassword->createView(),
            'user' => $user
        ]);
    }
}
