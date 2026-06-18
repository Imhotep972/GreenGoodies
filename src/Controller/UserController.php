<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Form\UserDeleteType;
use App\Form\UserSetApiType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\UserTools;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/compte',name: 'app_account_')]
final class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager,  )
    {
    }

    #[Route('/inscription', name: 'register')]
    public function register(Request $request, UserTools $userTools): Response
    {
        // nouvel utilisateur
        $user = new User();

        // role ROLE_USER par defaut
        $user->setRoles(['ROLE_USER']); 

        // on cree le formulaire
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $result = $userTools->createAccount($user);
            $this->addFlash($result['statut'],$result['message']);

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

        $setApiForm = $this->createForm(UserSetApiType::class, null,[
            'action' => $this->generateUrl('app_account_api'),
            'method' => 'POST',
            'access_enabled' => $user->getApiEnabled(),
        ]); 
        $deleteForm = $this->createForm(UserDeleteType::class, $user,[
            'action' => $this->generateUrl('app_account_delete'),
            'method' => 'POST',
        ]); 

        return $this->render('User/Compte.html.twig', [
            'orders' => $orders,
            'user' =>  $user,
            'activateApiForm' => $setApiForm,
            'deleteForm' => $deleteForm,

        ]);         
    }
 
    #[IsGranted('ROLE_USER')]
    #[Route(path: '/api/', name: 'api', methods: ['POST'])] 
    public function toogleApiAccess(Request $request, UserTools $userTools): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $setApiForm = $this->createForm(UserSetApiType::class, null,[
            'action' => $this->generateUrl('app_account_api'),
            'method' => 'POST',
            'access_enabled' => $user->getApiEnabled(),
        ]); 
        $setApiForm->handleRequest($request);
        
        if ($setApiForm->isSubmitted() && $setApiForm->isValid())
        {
            $result = $userTools->toogleApiAccess($user);
            $this->addFlash($result['statut'],$result['message']);
        }
        else
            $this->addFlash('danger','Un problème est survenu lors de l\'activation/desactivation de l\'acces API.');

        return $this->redirectToRoute('app_account_index'); 
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/delete/', name: 'delete', methods: ['POST'])] 
    public function delete(Request $request, UserTools $userTools): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $deleteForm = $this->createForm(UserDeleteType::class, $user,[
            'action' => $this->generateUrl('app_account_delete'),
            'method' => 'POST',
        ]); 
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid())
        {
            $result = $userTools->deleteAccount($user);
            $this->addFlash($result['statut'],$result['message']);
        }
        else
            $this->addFlash('danger','Un problème est survenu lors de la suppression du compte') ;

        return $this->redirectToRoute('app_account_index');         
    }
}