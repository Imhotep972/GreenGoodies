<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/compte',name: 'app_gg_account_')]
final class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager, )
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'index')]
    public function index(OrderRepository $orderRepository): Response
    {
        // recuperation des commandes
        $user = $this->getUser();
        $orders = $orderRepository->findBy(['user'=> $user->getId()]);
        $totalCommandes = array();
        $orderLines = array();
        foreach ($orders as $i => $order) {
            $totalCommandes[$i] = $order->getTotal();
            $orderLines[$i] = $order->getOrderLines();
        }
        return $this->render('User/compte.html.twig', [
            'orders' => $orders,
            'orderlines' => $orderLines,
            'totalorders' => $totalCommandes,
            'user' =>  $user,
        ]);
    }


    #[Route('/inscription', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        // nouvel utilisateur
        $user = new User();
        $user->setRoles(array('ROLE_USER'));

        // on cree le formulaire
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush(); 
            return $this->redirectToRoute('app_gg_accueil');
        }
        return $this->render('User/inscription.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/deconnexion', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if (!empty($this->getUser()))
            return $this->redirectToRoute('app_gg_accueil');   

        return $this->render('User/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/accessAPI/', name: 'accesAPI', methods: ['GET'])] //,requirements: ['id' => '\d+'], methods: ['GET'])])]
    public function accessAPI(): Response
    {
        $user = $this->getUser();
        ($user->getAPI()) ?  $user->SetAPI(false) : $user->SetAPI(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_gg_account_index'); 
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/delete/', name: 'delete', )] 
    public function delete(): Response
    {
        $user = $this->getUser();
        $user->setArchive(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_gg_account_index'); 
    }
}