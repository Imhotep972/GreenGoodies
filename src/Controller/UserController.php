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


final class UserController extends AbstractController
{
    public function __construct(private UserRepository $employeRepository, private EntityManagerInterface $entityManager, )
    {

    }

    #[Route('/compte', name: 'app_gg_account')]
    public function index(): Response
    {
        return $this->render('User/compte.html.twig', [
        ]);
    }


    #[Route('/inscription', name: 'app_gg_register')]
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

    #[Route(path: '/deconnexion', name: 'app_gg_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/connexion', name: 'app_gg_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('User/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


}