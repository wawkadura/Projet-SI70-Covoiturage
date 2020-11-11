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
            $infosEntrees = Array(); // [adresseDepart, adresseArrivee, dateDepart, heureDepart]
            $trajetsExistants = $trajetRepo->findAll();
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
            
            $trajets = $this->Comparaison($infosEntrees,$trajetsExistants);


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
    public function Comparaison($infosEntrees , $trajetsExistants ){
        $adresseDepart = $infosEntrees[0];
        $adresseArrivee = $infosEntrees[1];
        $dateDepart = $infosEntrees[2];
        $heureDepart = $infosEntrees[3];
        
        $trajetNiv1= Array(); //correspondance faible
        $trajetNiv2= Array(); //correspondance moyenne
        $trajetNiv3= Array(); //correspondance fort
        $niv1=0;
        $niv2=0;
        $niv3=0;
        foreach ($trajetsExistants as $trajet) {

            if($trajet->getDate()->format('Y-m-d')==$dateDepart 
            && $trajet->getHeureDepart()->format('H:i')>=$heureDepart 
            && strtolower($trajet->getAdresseDepart()->getVille())==strtolower($adresseDepart->getVille() )
            && strtolower($trajet->getAdresseArrivee()->getVille())==strtolower($adresseArrivee->getVille() )
            ){// niveau 1 
                if($trajet->getAdresseDepart()->getCodePostale()==$adresseDepart->getCodePostale() 
                && $trajet->getAdresseArrivee()->getCodePostale()==$adresseArrivee->getCodePostale() 
                ){// niveau 2
                    if(strtolower($trajet->getAdresseDepart()->getRue())==strtolower($adresseDepart->getRue()) 
                    && strtolower($trajet->getAdresseArrivee()->getRue())==strtolower($adresseArrivee->getRue() )
                    ){ // niveau 3
                        $trajetNiv3[$niv3]=$trajet;
                        $niv3 = $niv3 +1;
                    }
                    else{
                        $trajetNiv2[$niv2]=$trajet;
                        $niv2 = $niv2 +1;
                    }
                }
                else{
                    $trajetNiv1[$niv1]=$trajet;
                    $niv1 = $niv1 +1;
                }
                
            }
        }
        
        return $this->Combine($trajetNiv1,$trajetNiv2,$trajetNiv3);
    }
    public function Combine($trajetNiv1,$trajetNiv2,$trajetNiv3){
        $trajets = Array();
        $i = 0;
        if(!empty($trajetNiv3)){
            $trajets[$i] = $trajetNiv3;
            $i = $i+1;
        }
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

    /**
     * @Route("/trajet/{id}", name="roadshare_trajet")
     */
    public function Trajet($id,ReservationRepository $reservationRepo, TrajetRepository $trajetRepo, UtilisateurRepository $utilisateurRepo){
        $user = $this->getUser();
        $reservation = false;
        $utilisateur = $utilisateurRepo->findBy(array("compte" => $user->getId()))[0];
        $trajet = $trajetRepo->findBy(array('id'=>$id ))[0];
        if(!empty($trajet->getReservations())){
            foreach ($trajet->getReservations() as $res) {
                if($res->getDemadeur()->getId()==$utilisateur->getId()){
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
        $utilisateur = $repo->findBy(array("compte" => $user->getId()));
        $voitures= $this->getDoctrine()->getRepository(Voiture::class)->findAll();
        $criteres= $this->getDoctrine()->getRepository(Criteres::class)->findAll();

        return $this->render('roadshare/profil.html.twig', [
            'user' => $user,
            'utilisateur' => $utilisateur[0],
            'voitures'=>$voitures[0],
            'criteres'=>$criteres
        ]);

   
    }

  
    /**
     * @Route("/setinformation", name="roadshare_setinformation") 
    */
    public function setInformation(Request $request,ObjectManager $manager,UtilisateurRepository $repo){
    
        //
        $user = $this->getUser();
        $utilisateur = $repo->findBy(array("compte" => $user->getId()))[0];

        $voiture= $utilisateur->getVoiture();
        $criteres= $utilisateur->getCriteres();

        if(isset($voture))
        {
            $voiture = new Voiture();
        }
        if(isset($criteres))
        {
            $criteres= new Criteres();
        }
        
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        $formCriteres = $this->createForm(CriteresType::class, $criteres);
        $formCriteres->handleRequest($request);

        if(($form->isSubmitted() && $form->isValid())){
            $utilisateur->setVoiture($voiture);

            $manager->persist($voiture);
            $manager->flush();
           
        }

        if(($formCriteres->isSubmitted() && $formCriteres->isValid())){
            $utilisateur->setCriteres($criteres);

            dump($formCriteres);
            $manager->persist($criteres);
            $manager->flush();

        }
        return $this->render('roadshare/informations.html.twig', [
            'form' => $form->createView(),
            'formCriteres' => $formCriteres->createView(),
            'user' => $user
        ]);
    }


}
