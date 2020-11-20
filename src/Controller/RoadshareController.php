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
use App\Form\PropositionType;
use App\Form\ChangePasswordType;
use App\Form\InscriptionFormType;
use App\Form\UtilisateurType;
use App\Repository\AdressePostaleRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\AvisRepository;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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
    public function Home(): Response
    {   
        // récupération de l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('roadshare/home.html.twig', [
            'user' => $user
        ]);
    }
    /**
     * @Route("/connexion", name="roadshare_connexion")
     */
    public function Connexion(AuthenticationUtils $authenticationUtils): Response
    {   
        // récupération de l'utilisateur connecté
        $user = $this->getUser();
        // gestion me message d'erreur de connection  
        $error = $authenticationUtils->getLastAuthenticationError();
    
        return $this->render('roadshare/connexion.html.twig', [
            'user' => $user,
            'error' => $error
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
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();

        // Création des differents type de formulaire et les variables qui stockent les informations saisies
        $utilisateur = new Utilisateur;
        $compte = new Compte;
        $adressePostale = new AdressePostale;
        $description = new Description;
        $formData['utilisateur'] = $utilisateur;
        $formData['compte']  = $compte;
        $formData['adressePostale']  = $adressePostale;
        $formData['description']  = $description;

        // Création du formulaire global
        $form = $this->createForm(InscriptionFormType::class, $formData);
        $form->handleRequest($request);

        // Vérifier si les formulaires sont soumis et valid
        if(($form['compte']->isSubmitted() && $form['compte']->isValid()) 
            &&($form['utilisateur']->isSubmitted() && $form['utilisateur']->isValid())
            && ($form['adressePostale']->isSubmitted() && $form['adressePostale']->isValid())
            && ($form['description']->isSubmitted() && $form['description']->isValid())){
            
            // Hashage du mot de passe 
            $hash = $encoder->encodePassword($compte,$compte->getMotDePasse());
            $compte->setMotDePasse($hash);

            // Créer la requéte SQL pour la creation du compte
            $manager->persist($compte);
            $utilisateur->setCompte($compte);

            // Transformer la saisie d'adresse postale en minuscule pour eviter la casse
            $adressePostale->setRue(strtolower($adressePostale->getRue()));
            $adressePostale->setVille(strtolower($adressePostale->getVille()));

            // Vérifier dans la base de données si l'adresse saisie existe déjà, si oui l'utiliser sinon la créée 
            $adresseExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adressePostale->getNumeroRue(),'rue'=>$adressePostale->getRue(), 'ville'=> $adressePostale->getVille()));
            if(isset($adresseExistante)){
                $utilisateur->setAdressePostale($adresseExistante);
            }else{
                // Créer la requéte SQL pour la creation de l'adresse postale
                $manager->persist($adressePostale);
                $utilisateur->setAdressePostale($adressePostale);
            }
            
            // Créer la requéte SQL pour la creation de la description
            $manager->persist($description);
            $utilisateur->setDescription($description);

            // Créer la requéte SQL pour la creation de l'utilisateur
            $manager->persist($utilisateur);

            // Exécuter les requêtes dans la base
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
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();

        // Déclarer la variable pour la gestion d'erreur de la date et des heures
        $errors[0] ="";
        $errors[1] ="";

        // Création des differents type de formulaire et les variables qui stockent les informations saisies
        $trajet = new Trajet();
        $adresseDepart = new AdressePostale();
        $adresseArrivee = new AdressePostale();
        $formData['adresseDepart']  = $adresseDepart;
        $formData['adresseArrivee']  =  $adresseArrivee;
        $formData['trajet'] = $trajet;

        // Création du formulaire global
        $form = $this->createForm(PropositionType::class, $formData);
        $form->handleRequest($request);

        // Si le formulaire est saisie, crée les variables pour la prochaine vérification
        if($form['trajet']->isSubmitted() && $form['trajet']->isValid()){
            $dateBeforeToday = new \DateTime("now");
            $dateBeforeToday->modify("- 1 days");
            $date=$trajet->getDate();
            $heureDepart = $trajet->getHeureDepart();
            $heureArrivee = $trajet->getHeureArrivee();
        }

        // Vérifier que les formulaires sont soumis et valid 
        // Vérifier si la date renseignée est supérieur ou égale à la date d'aujourd'hui
        // Vérifier si l'heure de départ est inférieur à l'heure d'arrivée
        if(($form['trajet']->isSubmitted() && $form['trajet']->isValid()) 
        &&($form['adresseDepart']->isSubmitted() && $form['adresseDepart']->isValid()) 
        &&($form['adresseArrivee']->isSubmitted() && $form['adresseArrivee']->isValid()) 
        &&($date>$dateBeforeToday) && ($heureDepart < $heureArrivee)){

            // Prendre l'utilisateur connecté et le relier au trajet créé
            $conducteur = $repo->findOneBy(array("compte" => $user->getId()));
            $trajet->setConducteur($conducteur);
            $trajet->setEtat(self::EN_COURS);
            
            // Transformer la saisie d'adresse postale en minuscule pour eviter la casse
            $adresseDepart->setRue(strtolower($adresseDepart->getRue()));
            $adresseDepart->setVille(strtolower($adresseDepart->getVille()));
            $adresseArrivee->setRue(strtolower($adresseArrivee->getRue()));
            $adresseArrivee->setVille(strtolower($adresseArrivee->getVille()));

            // Vérifier dans la base de données si les adresses saisie existe déjà, si oui les utiliser sinon les créées 
            $adresseDepartExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseDepart->getNumeroRue(),'rue'=>$adresseDepart->getRue(), 'ville'=> $adresseDepart->getVille()));
            $adresseArriveeExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseArrivee->getNumeroRue(),'rue'=>$adresseArrivee->getRue(), 'ville'=> $adresseArrivee->getVille()));
            if(isset($adresseDepartExistante)){
                $trajet->setAdresseDepart($adresseDepartExistante);
            }else{

                // Créer la requéte SQL pour la creation de l'adresse postale de départ
                $manager->persist($adresseDepart);
                $trajet->setAdresseDepart($adresseDepart);
            }
            if(isset($adresseArriveeExistante)){
                $trajet->setAdresseArrivee($adresseArriveeExistante);
            }else{

                // Créer la requéte SQL pour la creation de l'adresse postale d'arrivée
                $manager->persist($adresseArrivee);
                $trajet->setAdresseArrivee($adresseArrivee);
            }

            // Créer la requéte SQL pour la création du trajet
            $manager->persist($trajet);

            // Exécuter les requêtes dans la base
            $manager->flush();

            // Rédiriger vers la page d'accueil
            return $this->redirectToRoute('roadshare_home');
        }

        // Vérifier si le formulaire à été rejeté à cause de la date ou des heures saisies
        if( isset($date) && isset($heureDepart) && isset($heureArrivee) ){

            // Si la date saisie est inférieur à la date d'aujourd'hui afficher le message d'erreur 
            if ($date<=$dateBeforeToday){
                $errors[0] = "la date saisie dois être supérieur ou égale à la date d'aujourd'hui";
            }

            // Si l'heure d'arrivée est inférieur à l'heure de départ afficher le message d'erreur 
            if ($heureDepart >= $heureArrivee){
                $errors[1] = "vérifier que l'heure de départ est inférieur à l'heure d'arrivée";
            }
        }

        return $this->render('roadshare/proposition.html.twig', [
            'form' => $form->createView(),
            'modification' => false,
            'errors' => $errors,
            'user' => $user
        ]);
    }

    /**
     * @Route("/recherche", name="roadshare_recherche")
     */
    public function Recherche(Request $request, TrajetRepository $trajetRepo, UtilisateurRepository $utilisateurRepo, InformationTravailRepository $informationTravailRepo, ReservationRepository $reservationRepo ): Response
    {   
        // Récuperer la requéte 
        $recherche = $request->request;

        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte"=>$user->getId()));
        
        // Récupérer les adresses postale des trajets déjà effectués pour la proposer à l'utilisateur
        $historiqueTrajets = $this->getHistoriqueTrajets($reservationRepo->findBy(array("demandeur" => $utilisateur->getId(),"etat"=>self::ACCEPTEE)),$trajetRepo->findBy(array("conducteur" => $utilisateur->getId(),"etat"=>self::EFFECTUE)));
        
        // Récupérer l'adresse de l'entreprise pour la proposer à l'utilisateur
        if( $utilisateur->getInformationTravail() != null){
            $adresseEntreprise= $utilisateur->getInformationTravail()->getEntreprise()->getAdressePostale();
        }else{
            $adresseEntreprise = new AdressePostale();
        }

        // Récupérer l'adresse de domicile pour la proposer à l'utilisateur
        $adresseDomicile = $utilisateur->getAdressePostale();

        // Récupérer les trajets proposés par d'autres utilisateurs qui travaillent à la même heure et dans la même entreprise que l'utilisateur connecté
        $trajetsEntreprise = $this->getTrajetsEntreprise($utilisateur,$utilisateurRepo,$trajetRepo,$informationTravailRepo);

        // Si le formulaire de recherche à été soumis
        if($recherche->count()>0){ 

            // Variable qui stockera l'ensemble des informations saisies
            $infosEntrees = Array(); // [adresseDepart, adresseArrivee, dateDepart, heureDepart]

            // Récuperer l'adresse postale de départ saisie 
            $infosEntrees[0] = new AdressePostale();
            $infosEntrees[0]->setRue($recherche->get('rueDepart'))
                            ->setVille($recherche->get('villeDepart'));
            if($recherche->get('numeroRueDepart')!="")
            {
                $infosEntrees[0]->setNumeroRue($recherche->get('numeroRueDepart')); 
            }

            // Récuperer l'adresse postale d'arrivée saisie 
            $infosEntrees[1] = new AdressePostale();
            $infosEntrees[1]->setRue($recherche->get('rueArrivee'))
                            ->setVille($recherche->get('villeArrivee'));      
            if($recherche->get('numeroRueArrivee')!="")
            {
                $infosEntrees[1]->setNumeroRue($recherche->get('numeroRueArrivee')); 
            }

            // Récuperer la date saisie
            $infosEntrees[2] = $recherche->get('dateDepart');

            // Récuperer l'heure de départ saisie
            $infosEntrees[3]= $recherche->get('heureDepart');

            // Récuperer les critéres saisis : [ fumeur(true ou false) , animaux(true ou false), musique(true ou false) ]
            $infosEntrees[4] = Array($recherche->get('fumeur')=='on', $recherche->get('animaux')=='on', $recherche->get('musique')=='on');
            
            // Récuperer les trajets en cours(non complét) qui correspondent à la date saisie et avec une heure de départ supérieur ou égale à l'heure saisie
            $trajetsExistants = $trajetRepo->findBy(array('date'=>new DateTime($infosEntrees[2]),'etat'=>self::EN_COURS), array('heureDepart' => 'ASC'));

            // Récupérer la list de trajets à afficher par rapport à les adresses postales et les critéres saisies 
            $trajets = $this->Comparaison($infosEntrees, $trajetsExistants);

            return $this->render('roadshare/recherche.html.twig', [
                'user' => $user,
                'adresseDomicile' =>$adresseDomicile,
                'adresseEntreprise' => $adresseEntreprise,
                'trajetsEntreprise' => $trajetsEntreprise,
                'historiqueTrajets' => $historiqueTrajets,
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
            'historiqueTrajets' => $historiqueTrajets,
            'informationTravail'=>$utilisateur->getInformationTravail(),
            'recherche' => ($recherche->count()>0)
        ]);
    }

    // Fonction permettant de générer une list de trajets par rapport à la recherche saisie
    public function Comparaison($infosEntrees , $trajetsExistants)
    {
        // Déclarer les variables pour la comparaison
        $adresseDepart = $infosEntrees[0];
        $adresseArrivee = $infosEntrees[1];
        $heureDepart = $infosEntrees[3];
        
        //  Déclarer la variable qui contiendra les trajets, cette variable sera une combinaison de clé => valeur, la clé représente le niveau
        $trajetsAvecNiv= Array(); 
        // trajetsAvecNiv[cleNiv => (ObjectTrajet) ]  ex: [ 1 => ObjetTrajet,  2 => ObjetTrajet, ...] 
        // les clés (1,2,3) permettant de connaitre le niveau de correspondance
        // niveau 1 : correspondance faible
        // niveau 2 : correspondance moyenne
        // niveau 3 : correspondance fort

        $i=0;

        // Parcourir la list des trajets trouvés précédemment 
        foreach ($trajetsExistants as $trajet) 
        {
            // Si 
            if($trajet->getHeureDepart()->format('H:i')>=$heureDepart
            && strtolower($trajet->getAdresseDepart()->getVille())==strtolower($adresseDepart->getVille() )
            && strtolower($trajet->getAdresseArrivee()->getVille())==strtolower($adresseArrivee->getVille())
            && $this->Criteres($trajet->getConducteur()->getDescription(),$infosEntrees[4])
            ){// Niveau 1
                if(strtolower($trajet->getAdresseDepart()->getRue())==strtolower($adresseDepart->getRue()) 
                && strtolower($trajet->getAdresseArrivee()->getRue())==strtolower($adresseArrivee->getRue() )
                ){// Niveau 2
                    if($trajet->getAdresseDepart()->getNumeroRue()==$adresseDepart->getNumeroRue()
                    && $trajet->getAdresseArrivee()->getNumeroRue()==$adresseArrivee->getNumeroRue()
                    ){// Niveau 3
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

    // Fonction permettant de retourner une expression(true ou false) selon les criteres renseignés
    public function Criteres($description,$criteres)
    {
        if($criteres[0] && !$description->getVoyagerAvecFumeur() 
        || $criteres[1] && !$description->getVoyagerAvecAnimaux() 
        || $criteres[2] && !$description->getVoyagerAvecMusique()){ 
            return False;
        }
        return true;
    }

    // Fonction permettant de retourner les trajets correspondent au informations de travail de l'utilisateur connecté
    public function getTrajetsEntreprise($utilisateur, $utilisateurRepo, $trajetRepo, $informationTravailRepo)
    {
        // Récupérer les informations de travail de l'utilisateur connecté
        $informationTravail= $utilisateur->getInformationTravail();
        $trajetsEntreprise = array();
      
        // Vérifier si l'utilisateur 
        if(isset($informationTravail)){
            
            // Récupérer tous les informations de travails avec les même horaire de debut et de fin
            $infosTravails= $informationTravailRepo->findBy(array('horaireDebut'=> $informationTravail->getHoraireDebut(),'horaireFin'=>$informationTravail->getHoraireFin()));
            $i=0;

            // Parcourir la list des infos de travails  
            foreach($infosTravails as $info){

                // Vérifier si l'information de travail détient le même nom d'entreprise que l'utilisateur connecté 
                if($informationTravail->getEntreprise()->getNom()==$info->getEntreprise()->getNom())
                {   
                    // Récupérer les conducteur (autre utilisateur) qui détienne les même informations de travail que l'utilisateur connecté
                    $conducteur = $utilisateurRepo->findOneBy(array("informationTravail" => $info->getId()));

                    // Récupérer les trajet proposé par ce conducteur
                    $trajets = $trajetRepo->findBy(array("heureArrivee" => $info->getHoraireDebut(), 'conducteur' => $conducteur->getId()));
                    foreach($trajets as $trajet){

                        // Vérifier si ce trajet va dans la bonne direction
                        $adresseDepart = $trajet->getAdresseDepart();
                        if($adresseDepart->getVille()==$informationTravail->getEntreprise()->getAdressePostale()->getVille()){

                            // Ajouter ce trajet 
                            $trajetsEntreprise[$i]= $trajet;
                            $i++;
                        }
                    }
                }
            }
        }
        return $trajetsEntreprise;
    }

    // Fonction permettant de récupérer l'historiques des adresses postales des trajets effectués
    public function getHistoriqueTrajets($reservationsAcceptee, $trajetsEffectuer)
    {
        // Récupération des adresse postales des trajets effectués via les reservations 
        $historiqueTrajets = array();
        if(count($reservationsAcceptee)>0){
            foreach($reservationsAcceptee as $res){
                if($res->getTrajet()->getEtat() == self::EFFECTUE){
                    $historiqueTrajets[$res->getTrajet()->getAdresseDepart()->getId()]=$res->getTrajet()->getAdresseDepart();
                    $historiqueTrajets[$res->getTrajet()->getAdresseArrivee()->getId()]=$res->getTrajet()->getAdresseArrivee();
                }
            }
        }
        // Récupération des adresse postales des trajets effectués via les propositions 
        if(count($trajetsEffectuer)>0){
            foreach($trajetsEffectuer as $trajet){
                $historiqueTrajets[$trajet->getAdresseDepart()->getId()]=$trajet->getAdresseDepart();
                $historiqueTrajets[$trajet->getAdresseArrivee()->getId()]=$trajet->getAdresseArrivee();
            }
        }
        return $historiqueTrajets;
    }

    /**
     * @Route("/trajet/{id}", name="roadshare_trajet")
     */
    public function Trajet($id,ReservationRepository $reservationRepo, TrajetRepository $trajetRepo, UtilisateurRepository $utilisateurRepo)
    {    
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $dejaReserver = false;

        // Récupérer le trajet qui correspond à l'id renseigné
        $trajet = $trajetRepo->findOneBy(array('id'=>$id ));

        // Récupérer les reservations de ce trajet 
        $reservationsAcceptee = $reservationRepo->findBy(array('trajet'=>$trajet->getId(), 'etat' => self::ACCEPTEE));        
        $reservationsEnAttente = $reservationRepo->findBy(array('trajet'=>$trajet->getId(), 'etat' =>self::EN_ATTENTE));  
        
        // Vérifier si l'utilisateur connecté n'a pas déjà envoyée une demande de réservation
        $reservation = $reservationRepo->findBy(array('trajet'=>$trajet->getId(), 'demandeur'=> $utilisateur->getId()));        
        $dejaReserver =  !empty($reservation);

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
     * @Route("/annulationTrajet/{id}", name="roadshare_annulation_trajet")
     */
    public function AnnulationTrajet($id, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo, ObjectManager $manager): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));

        // Récupérer le trajet qui correspond au id renseigné dans les paramétres
        $trajet = $trajetRepo->findOneBy(array("id" => $id));

        // Vérifier que l'utilisateur qui à proposé ce trajet est bien l'utilisateur connecté
        if($utilisateur->getId()==$trajet->getConducteur()->getId()){
            $trajet->setEtat(self::ANNULER);
        }

        // Créer et exécuter la requete SQL permettant la suppression du trajet 
        $manager->persist($trajet);
        $manager->flush();

        return $this->redirectToRoute('roadshare_vos_trajets');
    }

    /**
     * @Route("/trajetEffectue/{id}", name="roadshare_trajet_effectue")
     */
    public function TrajetEffectue($id, TrajetRepository $trajetRepo, ObjectManager $manager): Response
    {
        // Récupérer le trajet qui correspond au id renseigné dans les paramétres
        $trajet = $trajetRepo->findOneBy(array("id" => $id));

        // Changer l'état du trajet
        $trajet->setEtat(self::EFFECTUE);

        // Créer et exécuter la requete SQL permettant la mise à jour du trajet dans la base 
        $manager->persist($trajet);
        $manager->flush();

        return $this->redirectToRoute('roadshare_vos_trajets');
    }

    /**
     * @Route("/annulReservation/{id}", name="roadshare_annulation_reservation")
     */
    public function AnnulationReservation($id,ReservationRepository $reservationRepo, ObjectManager $manager): Response
    {   
        // Récupérer la reservation qui correspond au id renseigné dans les paramétres
        $reservation = $reservationRepo->findOneBy(array("id" => $id));

        // Créer et exécuter la requete SQL permettant la suppréssion de la réservation dans la base 
        $manager->remove($reservation);
        $manager->flush();

        return $this->redirectToRoute('roadshare_vos_trajets');
    }
    
    /**
     * @Route("/reservation/{id}", name="roadshare_reservation")
     */
    public function Reservation($id,ReservationRepository $reservationRepo, UtilisateurRepository $utilisateurRepo, TrajetRepository $trajetRepo, ObjectManager $manager): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));

        // Récupérer le trajet qui correspond au id renseigné dans les paramétres
        $trajet = $trajetRepo->findOneBy(array("id" => $id));

        // Créer la réservation
        $reservation = new Reservation;
        $reservation->setDemandeur($utilisateur)
                    ->setTrajet($trajet)
                    ->setEtat(self::EN_ATTENTE);

        // Créer et exécuter la requete SQL permettant la création de la réservation dans la base 
        $manager->persist($reservation);
        $manager->flush();

        // Vérifier le nombre de places restant au trajet 
        $totaleReservations = $reservationRepo->findBy(array('trajet'=>$id, 'etat'=>self::ACCEPTEE));
        if(count($totaleReservations)>= $trajet->getNbPlaces()){

            // Si le nombre de places est au maximum changer l'état du trajet de "EN_COURS" à "COMPLET"
            $trajet->setEtat(self::COMPLET);
            $manager->persist($trajet);
            $manager->flush();
        }

        return $this->redirectToRoute('roadshare_trajet',array('id'=>$id ));
    }

    /**
     * @Route("/reponseDemande/{id}/{accepte}", name="roadshare_reponse_demande")
     */
    public function ReponseDemande($id,$accepte,ReservationRepository $reservationRepo, ObjectManager $manager): Response
    {
        // Récupérer la réservation qui correspond au id renseigné dans les paramétres
        $reservation = $reservationRepo->findOneBy(array('id'=>$id));
        $trajet = $reservation->getTrajet();

        // Récupérer toute les réservations 
        $reservations = $reservationRepo->findBy(array('trajet'=>$trajet->getId(), 'etat'=> self::ACCEPTEE));
        
        if(($trajet->getNbPlaces())>(count($reservations))){

            // Si la réservation est accepter changer l'état de la réservation de EN_ATTENTE à ACCEPTER
            // Sinon changer l'état de la réservation de EN_ATTENTE à REFUSER
            if($accepte){
                $reservation->setEtat(self::ACCEPTEE);
            }else{
                $reservation->setEtat(self::REFUSER);
            }

            // Créer et exécuter la requete SQL permettant la mise à jour de la réservation dans la base 
            $manager->persist($reservation);
            $manager->flush();
        }
        return $this->redirectToRoute('roadshare_trajet',array('id'=>$reservation->getTrajet()->getId() ));
    }

    /**
     * @Route("/modifTrajet/{id}", name="roadshare_modifierTrajet")
     */
    public function ModifierTrajet($id,AdressePostaleRepository $adresseRepo, TrajetRepository $repo, Request $request, ObjectManager $manager)
    {    
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $trajet = $repo->findOneBy(array("id" => $id));

        // Déclarer la variable pour la gestion d'erreur de la date et des heures
        $errors[0] ="";
        $errors[1] ="";

        // Création des differents type de formulaire et les variables qui stockent les informations saisies
        $adresseDepart = $trajet->getAdresseDepart();
        $adresseArrivee = $trajet->getAdresseArrivee();
        $formData['adresseDepart']  = $adresseDepart;
        $formData['adresseArrivee']  =  $adresseArrivee;
        $formData['trajet'] = $trajet;

        // Création du formulaire global
        $form = $this->createForm(PropositionType::class, $formData);
        $form->handleRequest($request);

        // Si le formulaire est saisie, crée les variables pour la prochaine vérification
        if($form['trajet']->isSubmitted() && $form['trajet']->isValid()){
            $dateBeforeToday = new \DateTime("now");
            $dateBeforeToday->modify("- 1 days");
            $date=$trajet->getDate();
            $heureDepart = $trajet->getHeureDepart();
            $heureArrivee = $trajet->getHeureArrivee();
        }

        // Vérifier que les formulaires sont soumis et valid 
        // Vérifier si la date renseignée est supérieur ou égale à la date d'aujourd'hui
        // Vérifier si l'heure de départ est inférieur à l'heure d'arrivée
        if(($form['trajet']->isSubmitted() && $form['trajet']->isValid()) && 
            ($form['adresseDepart']->isSubmitted() && $form['adresseDepart']->isValid()) && 
            ($form['adresseArrivee']->isSubmitted() && $form['adresseArrivee']->isValid()) &&
            ($date>$dateBeforeToday) && ($heureDepart < $heureArrivee)){
            
            // Transformer la saisie d'adresse postale en minuscule pour eviter la casse
            $adresseDepart->setRue(strtolower($adresseDepart->getRue()));
            $adresseDepart->setVille(strtolower($adresseDepart->getVille()));
            $adresseArrivee->setRue(strtolower($adresseArrivee->getRue()));
            $adresseArrivee->setVille(strtolower($adresseArrivee->getVille()));

            // Vérifier dans la base de données si les adresses saisie existe déjà, si oui les utiliser sinon les créées 
            $adresseDepartExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseDepart->getNumeroRue(),'rue'=>$adresseDepart->getRue(), 'ville'=> $adresseDepart->getVille()));
            $adresseArriveeExistante = $adresseRepo->findOneBy(array('numeroRue'=>$adresseArrivee->getNumeroRue(),'rue'=>$adresseArrivee->getRue(), 'ville'=> $adresseArrivee->getVille()));
            if(isset($adresseDepartExistante)){
                $trajet->setAdresseDepart($adresseDepartExistante);
            }else{
                // Créer la requéte SQL pour la creation de l'adresse postale de départ
                $manager->persist($adresseDepart);
                $trajet->setAdresseDepart($adresseDepart);
            }
            if(isset($adresseArriveeExistante)){
                $trajet->setAdresseArrivee($adresseArriveeExistante);
            }else{
                // Créer la requéte SQL pour la creation de l'adresse postale d'arrivée
                $manager->persist($adresseArrivee);
                $trajet->setAdresseArrivee($adresseArrivee);
            }     

            // Créer la requéte SQL pour la mise à jour du trajet
            $manager->persist($trajet);

            // Exécuter les requêtes dans la base
            $manager->flush();
            
            return $this->redirectToRoute('roadshare_trajet', array('id' => $id));
        }
        
        // Vérifier si le formulaire à été rejeté à cause de la date ou des heures saisies
        if( isset($date) && isset($heureDepart) && isset($heureArrivee) ){

            // Si la date saisie est inférieur à la date d'aujourd'hui afficher le message d'erreur 
            if ($date<=$dateBeforeToday){
                $errors[0] = "la date saisie dois être supérieur ou égale à la date d'aujourd'hui";
            }

            // Si l'heure d'arrivée est inférieur à l'heure de départ afficher le message d'erreur 
            if ($heureDepart >= $heureArrivee){
                $errors[1] = "vérifier que l'heure de départ est inférieur à l'heure d'arrivée";
            }
        }
        
        return $this->render('roadshare/proposition.html.twig', [
            'form' => $form->createView(),
            'modification' => true,
            'errors' => $errors,
            'user' => $user
        ]);
    }

    /**
     * @Route("/profil/{id}", name="roadshare_profil_public")
     */
    public function ProfilPublic($id,UtilisateurRepository $repo)
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();

        // Récupération de l'utilisateur ciblé
        $utilisateur = $repo->findOneBy(array("id" => $id));

        // Récupérer la description de l'utilisateur ciblé
        $description= $utilisateur->getDescription();

        // Récupérer la voiture de l'utilisateur ciblé
        $voiture= $utilisateur->getVoiture();

        // Récupérer les informations de travail de l'utilisateur ciblé
        $informationTravail= $utilisateur->getInformationTravail();

        // Récupérer le compte de l'utilisateur ciblé
        $compte=$utilisateur->getCompte();

        // Vérifier si l'utilisateur ciblé a bien renseignées ses informations de travail
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
    public function Profil(UtilisateurRepository $repo)
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $repo->findOneBy(array("compte" => $user->getId()));

        // Récupérer la description de l'utilisateur
        $description= $utilisateur->getDescription();
    
        // Récupérer la voiture de l'utilisateur
        $voiture= $utilisateur->getVoiture();

        // Récupérer les informations de travail de l'utilisateur
        $informationTravail= $utilisateur->getInformationTravail();

        // Récupérer le compte de l'utilisateur
        $compte=$utilisateur->getCompte();

        // Vérifier si l'utilisateur a bien renseignées ses informations de travail
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
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        $id = $utilisateur->getId();

        // Récupération des trajets effectués
        $trajetsEffectuer = $trajetRepo->findBy(array("conducteur" => $id,"etat"=>self::EFFECTUE));
       
        // Récupération des trajets en cours
        $trajetsEnCours = $trajetRepo->findBy(array("conducteur" => $id,"etat"=>array(self::EN_COURS,self::COMPLET)));
       
        // Récupération des trajets annuler
        $trajetsAnnuler = $trajetRepo->findBy(array("conducteur" => $id,"etat"=>self::ANNULER));
       
        // Récupération des réservations refuser
        $reservationsRefuser = $reservationRepo->findBy(array("demandeur" => $id,"etat"=>self::REFUSER));
       
        // Récupération des réservations acceptées
        $reservationsAcceptee = $reservationRepo->findBy(array("demandeur" => $id,"etat"=>self::ACCEPTEE));
       
        // Récupération des réservations en attentes
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
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $expediteur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        
        // Récupération de l'utilisateur destinataire
        $destinataire = $utilisateurRepo->findOneBy(array("id" => $id));

        // Création des differents type de formulaire et les variables qui stockent les informations saisies
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        // Vérifier que les formulaires sont soumis et valides
        if($form->isSubmitted() && $form->isValid()){

            $avis->setExpediteur($expediteur);
            $avis->setDestinataire($destinataire);

            // Créer la requête SQL permettant la de créée l'avis
            $manager->persist($avis);

            // Executer la requête SQL dans la base
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
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $utilisateurRepo->findOneBy(array("compte" => $user->getId()));
        
        // Récupération les avis poster par l'utilisateur connecté
        $avisPoster = $avisRepo->findBy(array('expediteur' => $utilisateur->getId()));

        // Récupération les avis recu de l'utilisateur connecté
        $avisRecu = $avisRepo->findBy(array('destinataire' => $utilisateur->getId()));
        
        // Récupération des trajets proposés par l'utilisateur connecté
        $trajetsProposes = $trajetRepo->findBy(array('conducteur'=>$utilisateur->getId(), 'etat'=>self::EFFECTUE));
        
        // Récupération des réservations de l'utilisateur connecté
        $reservations = $reservationRepo->findBy(array('demandeur'=>$utilisateur->getId(), 'etat'=> self::ACCEPTEE));
        
        // Récupération de la liste des passager que l'utilisateur connecté n'a pas encore rédigé d'avis
        $avisARedigerPassagers = $this->getAvisARedigerPassagers($trajetsProposes, $avisPoster, $reservationRepo);
        
        // Récupération de la liste des conducteur que l'utilisateur connecté n'a pas encore rédigé d'avis
        $avisARedigerConducteur = $this->getAvisARedigerConducteur($reservations, $avisPoster);

        return $this->render('roadshare/vosAvis.html.twig', [
            'user' => $user,
            'avisPoster' => $avisPoster,
            'avisRecu' => $avisRecu,
            'avisARedigerPassagers' => $avisARedigerPassagers,
            'avisARedigerConducteur' => $avisARedigerConducteur
        ]);
    }

    // Fonction qui retourne une la liste des passager que l'utilisateur connecté n'a pas encore rédigé d'avis
    public function getAvisARedigerPassagers($trajetsProposes, $avisPoster, $reservationRepo)
    {
        $i=0;
        $avisARediger = array();
        if(!empty($trajetsProposes)){

            // Parcourir les trajets dans la list des trajets proposées
            foreach($trajetsProposes as $trajet){
                $reservations = $reservationRepo->findBy(array('trajet'=>$trajet->getId(),'etat'=>self::ACCEPTEE));
                if(!empty($reservations)){
                    
                    // Parcourir les réservations du trajet
                    foreach($reservations as $res){
                        $dejaPoster = false;
                        if(!empty($avisPoster)){
                            
                            // Vérifier si l'utilisateur connecté n'a pas déjà poster d'avis sur ce utilisateur
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

    // Fonction qui retourne une la liste des passager que l'utilisateur connecté n'a pas encore rédigé d'avis
    public function getAvisARedigerConducteur($reservations, $avisPoster)
    {
        $i=0;
        $avisARediger = array();
        if(!empty($reservations)){

            // Parcourir la réservation dans la list des réservations
            foreach($reservations as $res){
                if($res->getTrajet()->getEtat()==self::EFFECTUE){
                    $dejaPoster = false;
                    if(!empty($avisPoster)){

                        // Vérifier si l'utilisateur connecté n'a pas déjà poster d'avis sur ce utilisateur
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
    public function setInformation(AdressePostaleRepository $adresseRepo, Request $request,ObjectManager $manager,UtilisateurRepository $repo, UserPasswordEncoderInterface $encoder, EntrepriseRepository $entrepriseRepo){
    
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $utilisateur = $repo->findOneBy(array("compte" => $user->getId()));

        // Récupération de tous les entreprises pour les proposés comme choix
        $entreprises = $entrepriseRepo->findAll();

        // Récupération de la description de l'utilisateur connecté
        $description= $utilisateur->getDescription();

        // Récupération du compte de l'utilisateur connecté
        $compte= $utilisateur->getCompte();

        // Récupération de l'adresse postale du domicile de l'utilisateur connecté
        $adresseDomicile= $utilisateur->getAdressePostale();

        // Récupération des informations de travail de l'utilisateur connecté
        $informationTravail= $utilisateur->getInformationTravail();
       
        // Récupération de la voiture de l'utilisateur connecté
        $voiture= $utilisateur->getVoiture();

        // Vérifier si l'utilisateur a bien renseignée une voiture
        if(!isset($voiture)){
            $voiture = new Voiture();
        }

        // Vérifier si l'utilisateur a bien renseignée des informations de travail
        if(!isset($informationTravail )){
            $informationTravail = new InformationTravail();
            $entreprise = new Entreprise();
            $adressePostaleEntreprise= new AdressePostale();
        }
        else {
            $entreprise=$informationTravail->getEntreprise();
            $adressePostaleEntreprise= $entreprise->getAdressePostale();
        }
        ######################### Création des formulaires ###########################
        
        // Formulaire de travail 
        $formData['entreprise'] = $entreprise;
        $formData['adressepostaleTravail'] = $adressePostaleEntreprise;
        $formData['informationTravail']  =  $informationTravail;
        $formTravail = $this->createForm(TravailType::class, $formData);
        $formTravail->handleRequest($request);
        
        // Formulaire adresse postale du domicile 
        $formDomicile = $this->createForm(AdressePostaleType::class, $adresseDomicile);
        $formDomicile->handleRequest($request);
        
        // Formulaire voiture 
        $formVoiture = $this->createForm(VoitureType::class, $voiture);
        $formVoiture->handleRequest($request);

        // Formulaire description 
        $formDescription = $this->createForm(DescriptionType::class, $description);
        $formDescription->handleRequest($request);
        
        // Formulaire des informations personnelles 
        $formUtilisateur=$this->createForm(utilisateurType::class,$utilisateur);
        $formUtilisateur->handleRequest($request);
        
        // Formulaire de nouveau mot de passe
        $formpassword = $this->createForm(ChangePasswordType::class, $compte);
        $formpassword->handleRequest($request);
        ###########################################################################
        
        ///////////////////////// Formulaire Voiture /////////////////////////
        if($formVoiture->isSubmitted() && $formVoiture->isValid()){
            $utilisateur->setVoiture($voiture);
            $manager->persist($voiture);
            $manager->flush();
        }

        ///////////////////////// Formulaire Description /////////////////////////
        if($formDescription->isSubmitted() && $formDescription->isValid()){
            $utilisateur->setDescription($description);
            $manager->persist($description);
            $manager->flush();
        }

        /////////// Formulaire Informations de travail & Entreprise ///////////////
        if(($formTravail['entreprise']->isSubmitted() && $formTravail['entreprise']->isValid())  
        && ($formTravail['informationTravail']->isSubmitted() && $formTravail['informationTravail']->isValid()) 
        && ($formTravail['adressepostaleTravail']->isSubmitted() && $formTravail['adressepostaleTravail']->isValid())){

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
        //////////////////// Formulaire informations personnelles //////////////////
        if($formUtilisateur->isSubmitted() && $formUtilisateur->isValid()){
            $manager->persist($utilisateur);
            $manager->flush();
        }

        ///////////////////////// Formulaire de Mot de passe /////////////////////////
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
            'formDomicile' => $formDomicile->createView(),
            'entreprises' => $entreprises,
            'user' => $user
        ]);
    }


      /**
     * @Route("/suprimeVoiture", name="roadshare_surprimerVoiture") 
     */
    public function SurprimerVoiture(UtilisateurRepository $repo, ObjectManager $manager){
        $user= $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];
        $voiture= $utilisateur->getVoiture();
        dump($voiture);
        $manager->remove($voiture);
        $manager->flush();
        return $this->redirectToRoute('roadshare_profil');
    }

     /**
     * @Route("/surprimeInformationTravail", name="roadshare_surprimeInformationTravail") 
     */
    public function SurprimerInfomationTravail(UtilisateurRepository $repo, ObjectManager $manager){
        $user= $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];
        $informationTravail= $utilisateur->getInformationTravail();

        $manager->remove($informationTravail);
        $manager->flush();
        return $this->redirectToRoute('roadshare_profil');
    }
}