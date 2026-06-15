<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/compte',name: 'app_account_')]
final class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager, )
    {
    }

    #[Route('/inscription', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        // nouvel utilisateur
        $user = new User();
        // role ROLE_USER par defaut
        $user->setRoles(['ROLE_USER']); 

        // compte :  Archive / Acces API sont initialisé dans le constructeur
      
        // on cree le formulaire
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt(new DateTimeImmutable());
            $this->entityManager->persist($user);
            $this->entityManager->flush(); 
            $this->addFlash('home','Vous etes inscrit sur le site, veuillez maintenant vous connecter');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('User/inscription.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        /** @var User $user */
/*        $user = $this->getUser();
        if (!empty($user)) { // on n'arrive pas ici, login est gere par Symfony
            $this->entityManager->persist($user);
            $this->entityManager->flush(); 

            $this->addFlash('account','Vous etes maintenant connecté');

            return $this->redirectToRoute('app_account_index');   
        }*/

        return $this->render('User/Login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/deconnexion', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'index')]
    public function index(OrderRepository $orderRepository): Response
    {
        // recuperation des commandes
        /** @var User $user */
        $user = $this->getUser();
        $orders = $orderRepository->findBy(['user'=> $user->getId()]);


        return $this->render('User/Compte.html.twig', [
            'orders' => $orders,
            'user' =>  $user,
        ]);         

        /*   
        $totalCommandes = [];
        $orderLines = [];        
        // Pour chaque commande on recupere le montant de la commande et les lignes de la commande
        foreach ($orders as $i => $order) {
            $totalCommandes[$i] = $order->getAmount();
            $orderLines[$i] = $order->getOrderLines();
        }}


        return $this->render('User/Compte.html.twig', [
            'orders' => $orders,
            'orderlines' => $orderLines,
            'totalorders' => $totalCommandes,
            'user' =>  $user,
        ]);        */
    }
 
    #[IsGranted('ROLE_USER')]
    #[Route(path: '/accessAPI/', name: 'API', methods: ['GET'])] //,requirements: ['id' => '\d+'], methods: ['GET'])])]
    public function accessAPI(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getAPI()) {
            $user->SetAPI(false);
            $this->addFlash('account','Accès API désactivé');
        } else {
            $user->SetAPI(true);
            $this->addFlash('account','Accès API activé');
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_account_index'); 
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/delete/', name: 'delete', )] 
    public function delete(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // on indique que l'utilisateur n'est plus actif
        $user->setArchive(true);
        $user->setAPI(false);
        $user->setDeletedAt(new DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('account','Compte supprimé avec succès') ;

        return $this->redirectToRoute('app_account_index');         
    }

}