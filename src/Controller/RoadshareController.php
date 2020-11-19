<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Entity\Trajet;
use App\Entity\AdressePostale;
use App\Entity\Avis;
use App\Entity\Voiture;
use App\Entity\Entreprise;
use App\Entity\Description;
use App\Entity\InformationTravail;
use App\Entity\Reservation;
use App\Form\AvisType;
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
use App\Repository\AvisRepository;
use App\Repository\DescriptionRepository;
use App\Repository\InformationTravailRepository;
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
        $user = $this->getUser();
        return $this->render('roadshare/connexion.html.twig', [
            'user' => $user
        ]);
    }
    /**
     * @Route("/deconnexion", name="roadshare_deconnexion")
     */
    public function Deconnexion(){}
    /**
     * @Route("/inscription", name="roadshare_inscription")
     */
    public function Inscription(AdressePostaleRepository $adresseRepo, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
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

            $adressePostale->setRue(strtolower($adressePostale->getRue()));
            $adressePostale->setVille(strtolower($adressePostale->getVille()));
            $adresseExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adressePostale->getNumeroRue(),'rue'=>$adressePostale->getRue(), 'ville'=> $adressePostale->getVille()));
            if(isset($adresseExistante)){
                $utilisateur->setAdressePostale($adresseExistante);
            }else{
                $manager->persist($adressePostale);
                $utilisateur->setAdressePostale($adressePostale);
            }
            $manager->persist($description);
            $utilisateur->setDescription($description);

            $manager->persist($utilisateur);
            $manager->flush();
            return $this->redirectToRoute('roadshare_connexion');
        }
        return $this->render('roadshare/inscription.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
    /**
     * @Route("/proposition", name="roadshare_proposition")
     */
    public function Proposition(AdressePostaleRepository $adresseRepo, Request $request, ObjectManager $manager,UtilisateurRepository $repo): Response
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
            
            $adresseDepart->setRue(strtolower($adresseDepart->getRue()));
            $adresseDepart->setVille(strtolower($adresseDepart->getVille()));

            $adresseArrivee->setRue(strtolower($adresseArrivee->getRue()));
            $adresseArrivee->setVille(strtolower($adresseArrivee->getVille()));

            $adresseDepartExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseDepart->getNumeroRue(),'rue'=>$adresseDepart->getRue(), 'ville'=> $adresseDepart->getVille()));
            $adresseArriveeExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseArrivee->getNumeroRue(),'rue'=>$adresseArrivee->getRue(), 'ville'=> $adresseArrivee->getVille()));
            if(isset($adresseDepartExistante)){
                $trajet->setAdresseDepart($adresseDepartExistante);
            }else{
                $manager->persist($adresseDepart);
                $trajet->setAdresseDepart($adresseDepart);
            }
            if(isset($adresseArriveeExistante)){
                $trajet->setAdresseArrivee($adresseArriveeExistante);
            }else{
                $manager->persist($adresseArrivee);
                $trajet->setAdresseArrivee($adresseArrivee);
            }   
            $manager->persist($trajet);
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
    public function Recherche(Request $request, TrajetRepository $trajetRepo, UtilisateurRepository $utilisateurRepo, InformationTravailRepository $informationTravailRepo ): Response
    {   
        $recherche = $request->request;
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte"=>$user->getId()));
        if( $utilisateur->getInformationTravail() != null){
            $adresseEntreprise= $utilisateur->getInformationTravail()->getEntreprise()->getAdressePostale();
        }else{
            $adresseEntreprise = new AdressePostale();
        }
        $adresseDomicile = $utilisateur->getAdressePostale();

        $trajetsEntreprise = $this->getTrajetsEntreprise($utilisateur,$utilisateurRepo,$trajetRepo,$informationTravailRepo);

        if($recherche->count()>0){ 
            
            $infosEntrees = Array(); // [adresseDepart, adresseArrivee, dateDepart, heureDepart]
            $infosEntrees[0] = new AdressePostale();
            $infosEntrees[0]->setRue($recherche->get('rueDepart'))
                            ->setVille($recherche->get('villeDepart'));
            if($recherche->get('numeroRueDepart')!="")
            {
                $infosEntrees[0]->setNumeroRue($recherche->get('numeroRueDepart')); 
            }
                            
            $infosEntrees[1] = new AdressePostale();
            $infosEntrees[1]->setRue($recherche->get('rueArrivee'))
                            ->setVille($recherche->get('villeArrivee'));      
            if($recherche->get('numeroRueArrivee')!="")
            {
                $infosEntrees[1]->setNumeroRue($recherche->get('numeroRueArrivee')); 
            }
            $infosEntrees[2] = $recherche->get('dateDepart');
            $infosEntrees[3]= $recherche->get('heureDepart');

            $infosEntrees[4] = Array($recherche->get('fumeur')=='on', $recherche->get('animaux')=='on', $recherche->get('musique')=='on');
            $date = new DateTime($infosEntrees[2]);
            $trajetsExistants = $trajetRepo->findBy(array('date'=>$date,'etat'=>self::EN_COURS), array('heureDepart' => 'ASC'));
            $trajets = $this->Comparaison($infosEntrees, $trajetsExistants);
            return $this->render('roadshare/recherche.html.twig', [
                'user' => $user,
                'adresseDomicile' =>$adresseDomicile,
                'adresseEntreprise' => $adresseEntreprise,
                'trajetsEntreprise' => $trajetsEntreprise,
                'informationTravail'=>$utilisateur->getInformationTravail(),
                'recherche' => ($recherche->count()>0),
                'trajets' => $trajets,
                'infosEntrees' => $infosEntrees
            ]);
        }
        return $this->render('roadshare/recherche.html.twig', [
            'user' => $user,
            'adresseDomicile' =>$adresseDomicile,
            'adresseEntreprise' => $adresseEntreprise,
            'trajetsEntreprise' => $trajetsEntreprise,
            'informationTravail'=>$utilisateur->getInformationTravail(),
            'recherche' => ($recherche->count()>0)
        ]);
    }
    public function Comparaison($infosEntrees , $trajetsExistants){
        $adresseDepart = $infosEntrees[0];
        $adresseArrivee = $infosEntrees[1];
        $heureDepart = $infosEntrees[3];
        
        $trajetsAvecNiv= Array(); 
        // trajetsAvecNiv[cleNiv => (ObjectTrajet) ]  ex: [ 1 => ObjetTrajet,  2 => ObjetTrajet, ...] 
        // les clés (1,2,3) permettant de connaitre le niveau de correspondance
        // niveau 1 : correspondance faible
        // niveau 2 : correspondance moyenne
        // niveau 3 : correspondance fort

        $i=0;
        foreach ($trajetsExistants as $trajet) {
            
            if($trajet->getHeureDepart()->format('H:i')>=$heureDepart
            && strtolower($trajet->getAdresseDepart()->getVille())==strtolower($adresseDepart->getVille() )
            && strtolower($trajet->getAdresseArrivee()->getVille())==strtolower($adresseArrivee->getVille())
            && $this->Criteres($trajet->getConducteur()->getDescription(),$infosEntrees[4])
            ){// niveau 1
                if(strtolower($trajet->getAdresseDepart()->getRue())==strtolower($adresseDepart->getRue()) 
                && strtolower($trajet->getAdresseArrivee()->getRue())==strtolower($adresseArrivee->getRue() )
                ){// niveau 2
                    if($trajet->getAdresseDepart()->getNumeroRue()==$adresseDepart->getNumeroRue()
                    && $trajet->getAdresseArrivee()->getNumeroRue()==$adresseArrivee->getNumeroRue()
                    ){// niveau 3
                        $trajetsAvecNiv[$i]=array( '3' => $trajet );
                    }else{
                        $trajetsAvecNiv[$i]=array( '2' => $trajet );
                    }
                }
                else{
                    $trajetsAvecNiv[$i]=array( '1' => $trajet );
                }
            }
            $i++;
        }
        
        return $trajetsAvecNiv;
    }
    public function Criteres($description,$criteres){

        if($criteres[0] && !$description->getVoyagerAvecFumeur() 
        || $criteres[1] && !$description->getVoyagerAvecAnimaux() 
        || $criteres[2] && !$description->getVoyagerAvecMusique()){ 
            return False;
        }
        return true;
    }

    public function getTrajetsEntreprise($utilisateur, $utilisateurRepo, $trajetRepo, $informationTravailRepo){
        
        $informationTravail= $utilisateur->getInformationTravail();
        $trajetsEntreprise = array();
      
        if(isset($informationTravail)){ 
            
            $allInfoTravail= $informationTravailRepo->findBy(array('horaireDebut'=> $informationTravail->getHoraireDebut(),'horaireFin'=>$informationTravail->getHoraireFin()));
            $i=0;
            foreach($allInfoTravail as $info){

                if($informationTravail->getEntreprise()->getNom()==$info->getEntreprise()->getNom())
                {
                    $conducteur = $utilisateurRepo->findOneBy(array("informationTravail" => $info->getId()));
                    $trajets = $trajetRepo->findBy(array("heureArrivee" => $info->getHoraireDebut(), 'conducteur' => $conducteur->getId()));
                    foreach($trajets as $trajet){
                        $adresseDepart = $trajet->getAdresseDepart();
                        if($adresseDepart->getVille()==$informationTravail->getEntreprise()->getAdressePostale()->getVille()){
                            $trajetsEntreprise[$i]= $trajet;
                            $i++;
                        }
                    }
                }
            }
        }
        return $trajetsEntreprise;

       
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
     * @Route("/annulation/{id}", name="roadshare_annulation")
     */
    public function Annulation($id,ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo, ObjectManager $manager): Response
    {
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $trajet = $trajetRepo->findOneBy(array("id" => $id));

        if($utilisateur->getId()==$trajet->getConducteur()->getId()){
            $trajet->setEtat(self::ANNULER);
        }
        $manager->persist($trajet);
        $manager->flush();

        return $this->redirectToRoute('roadshare_vos_trajets');
    }

    /**
     * @Route("/trajetEffectue/{id}", name="roadshare_trajet_effectue")
     */
    public function TrajetEffectue($id,ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo, ObjectManager $manager): Response
    {
        $trajet = $trajetRepo->findOneBy(array("id" => $id));
        $trajet->setEtat(self::EFFECTUE);
        $manager->persist($trajet);
        $manager->flush();

        return $this->redirectToRoute('roadshare_vos_trajets');
    }
    /**
     * @Route("/annulReservation/{id}", name="roadshare_annulation_reservation")
     */
    public function AnnulationReservation($id,ReservationRepository $reservationRepo, ObjectManager $manager): Response
    {   
        $reservation = $reservationRepo->findOneBy(array("id" => $id));
        $manager->remove($reservation);
        $manager->flush();

        return $this->redirectToRoute('roadshare_vos_trajets');
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
    public function ModifierTrajet($id,AdressePostaleRepository $adresseRepo, TrajetRepository $repo, Request $request, ObjectManager $manager){
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
            

            $adresseDepart->setRue(strtolower($adresseDepart->getRue()));
            $adresseDepart->setVille(strtolower($adresseDepart->getVille()));

            $adresseArrivee->setRue(strtolower($adresseArrivee->getRue()));
            $adresseArrivee->setVille(strtolower($adresseArrivee->getVille()));

            $adresseDepartExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseDepart->getNumeroRue(),'rue'=>$adresseDepart->getRue(), 'ville'=> $adresseDepart->getVille()));
            $adresseArriveeExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseArrivee->getNumeroRue(),'rue'=>$adresseArrivee->getRue(), 'ville'=> $adresseArrivee->getVille()));
            if(isset($adresseDepartExistante)){
                $trajet->setAdresseDepart($adresseDepartExistante);
            }else{
                $manager->persist($adresseDepart);
                $trajet->setAdresseDepart($adresseDepart);
            }
            if(isset($adresseArriveeExistante)){
                $trajet->setAdresseArrivee($adresseArriveeExistante);
            }else{
                $manager->persist($adresseArrivee);
                $trajet->setAdresseArrivee($adresseArrivee);
            }     
            $manager->persist($trajet);
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
        $adresseDomicile= $utilisateur->getAdressePostale();

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
            'adresseDomicile'=> $utilisateur->getAdressePostale(),
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
            'adresseDomicile'=> $utilisateur->getAdressePostale(),
            'entreprise'=>$entreprise
        ]);
    }
    /**
     * @Route("/vosTrajet", name="roadshare_vos_trajets")
     */
    public function VosTrajet(ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo): Response
    {
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $id = $utilisateur->getId();
        $trajetsEffectuer = $trajetRepo->findBy(array("conducteur" => $id,"etat"=>self::EFFECTUE));
        $trajetsEnCours = $trajetRepo->findBy(array("conducteur" => $id,"etat"=>array(self::EN_COURS,self::COMPLET)));
        $trajetsAnnuler = $trajetRepo->findBy(array("conducteur" => $id,"etat"=>self::ANNULER));
        $reservationsRefuser = $reservationRepo->findBy(array("demandeur" => $id,"etat"=>self::REFUSER));
        $reservationsAcceptee = $reservationRepo->findBy(array("demandeur" => $id,"etat"=>self::ACCEPTEE));
        $reservationsEnAttente = $reservationRepo->findBy(array("demandeur" => $id,"etat"=>self::EN_ATTENTE));

        return $this->render('roadshare/vosTrajets.html.twig', [
            'user' => $user,
            'trajetsEffectuer' =>$trajetsEffectuer,
            'trajetsEnCours' => $trajetsEnCours,
            'trajetsAnnuler' => $trajetsAnnuler,
            'reservationsRefuser' => $reservationsRefuser,
            'reservationsAcceptee' => $reservationsAcceptee,
            'reservationsEnAttente' => $reservationsEnAttente
        ]);
    }
        /**
     * @Route("/avis/{id}", name="roadshare_avis")
     */
    public function Avis($id,ObjectManager $manager, Request $request, AvisRepository $avisRepo,ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo): Response
    {
        $user = $this->getUser();
        $expediteur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $destinataire = $utilisateurRepo->findOneBy(array("id" => $id));

        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $avis->setExpediteur($expediteur);
            $avis->setDestinataire($destinataire);
            $manager->persist($avis);
            $manager->flush();
            return $this->redirectToRoute('roadshare_vos_avis');
        }
        return $this->render('roadshare/redigerAvis.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/vosAvis", name="roadshare_vos_avis")
     */
    public function VosAvis(AvisRepository $avisRepo,ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo): Response
    {// pas fini
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $avisPoster = $avisRepo->findBy(array('expediteur' => $utilisateur->getId()));
        $avisRecu = $avisRepo->findBy(array('destinataire' => $utilisateur->getId()));
        $trajetsProposes = $trajetRepo->findBy(array('conducteur'=>$utilisateur->getId(), 'etat'=>self::EFFECTUE));
        $reservations = $reservationRepo->findBy(array('demandeur'=>$utilisateur->getId(), 'etat'=> self::ACCEPTEE));

        $avisARedigerPassagers = $this->getAvisARedigerPassagers($trajetsProposes, $avisPoster, $reservationRepo);
        $avisARedigerConducteur = $this->getAvisARedigerConducteur($reservations, $avisPoster);

        return $this->render('roadshare/vosAvis.html.twig', [
            'user' => $user,
            'avisPoster' => $avisPoster,
            'avisRecu' => $avisRecu,
            'avisARedigerPassagers' => $avisARedigerPassagers,
            'avisARedigerConducteur' => $avisARedigerConducteur
        ]);
    }

    public function getAvisARedigerPassagers($trajetsProposes, $avisPoster, $reservationRepo){
        $i=0;
        $avisARediger = array();
        if(!empty($trajetsProposes)){
            foreach($trajetsProposes as $trajet){
                $reservations = $reservationRepo->findBy(array('trajet'=>$trajet->getId(),'etat'=>self::ACCEPTEE));
                if(!empty($reservations)){
                    foreach($reservations as $res){
                        $dejaPoster = false;
                        if(!empty($avisPoster)){
                            foreach($avisPoster as $avis){
                                if($res->getDemandeur()->getId()==$avis->getDestinataire()->getId()){
                                    $dejaPoster = true;
                                }
                            }
                        }
                        if(!$dejaPoster){
                            $avisARediger[$i]= $res;
                            $i++;
                        }
    
                    }
                }
            }
        }
        return $avisARediger;
    }
    public function getAvisARedigerConducteur($reservations, $avisPoster){
        $i=0;
        $avisARediger = array();
        if(!empty($reservations)){
            foreach($reservations as $res){
                if($res->getTrajet()->getEtat()==self::EFFECTUE){
                    $dejaPoster = false;
                    if(!empty($avisPoster)){
                        foreach($avisPoster as $avis){
                            if($res->getTrajet()->getConducteur()->getId()!=$avis->getDestinataire()->getId()){
                                $dejaPoster = true;
                            }
                        }
                    }
                    if(!$dejaPoster){
                        $avisARediger[$i]= $res;
                        $i++;
                    }
                }
            }
        }
        return $avisARediger;
    }
    
    /**
     * @Route("/setinformation", name="roadshare_setinformation") 
    */
    public function setInformation(AdressePostaleRepository $adresseRepo, Request $request,ObjectManager $manager,UtilisateurRepository $repo, UserPasswordEncoderInterface $encoder){
    
        
        $user = $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];

        $voiture= $utilisateur->getVoiture();
        $description= $utilisateur->getDescription();
        $informationTravail= $utilisateur->getInformationTravail();
        $compte= $utilisateur->getCompte();
        $adresseDomicile= $utilisateur->getAdressePostale();

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
        $formData['adressepostaleTravail'] = $adressePostaleEntreprise;
        $formData['adressepostale'] = $adresseDomicile;
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

        if($formVoiture->isSubmitted() && $formVoiture->isValid()){
            $utilisateur->setVoiture($voiture);
            $manager->persist($voiture);
            $manager->flush();
        }
        if($formDescription->isSubmitted() && $formDescription->isValid()){
            $utilisateur->setDescription($description);
            $manager->persist($description);
            $manager->flush();
        }
        if(($formTravail['entreprise']->isSubmitted() && $formTravail['entreprise']->isValid()) && 
        ($formTravail['informationTravail']->isSubmitted() && $formTravail['informationTravail']->isValid())&& 
        ($formTravail['adressepostaleTravail']->isSubmitted() && $formTravail['adressepostaleTravail']->isValid()) &&
        ($formTravail['adressepostale']->isSubmitted() && $formTravail['adressepostale']->isValid()) ){

            $adressePostaleEntreprise->setRue(strtolower($adressePostaleEntreprise->getRue()));
            $adressePostaleEntreprise->setVille(strtolower($adressePostaleEntreprise->getVille()));
            $adresseExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adressePostaleEntreprise->getNumeroRue(),'rue'=>$adressePostaleEntreprise->getRue(), 'ville'=> $adressePostaleEntreprise->getVille()));
            if(isset($adresseExistante)){
                $entreprise->setAdressePostale($adresseExistante);
            }else{
                $manager->persist($adressePostaleEntreprise);
                $entreprise->setAdressePostale($adressePostaleEntreprise);
            }

            $entreprise->setAdressePostale($adressePostaleEntreprise);
            $informationTravail->setEntreprise($entreprise);
            $utilisateur->setInformationTravail($informationTravail);
            $utilisateur->setAdressePostale($adresseDomicile);


            $manager->persist($adressePostaleEntreprise);
            $manager->persist($entreprise);
            $manager->persist($informationTravail);
            $manager->persist($adresseDomicile);
            $manager->persist($utilisateur);
            $manager->flush();

        }
        if($formUtilisateur->isSubmitted() && $formUtilisateur->isValid()){

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