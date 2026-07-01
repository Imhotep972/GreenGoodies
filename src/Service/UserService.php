<?php
// src/Service/UserTools.php
namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $hasher)
    {

    }
    public function toogleApiAccess($user): array
    {
        try
        {
            $state = $user->isApiEnabled();
            $newState = !$state;
            $statut = 'success';
            $message = ($newState)? 'Accès API activé' : 'Accès API désactivé';

            $user->setApiEnabled($newState);
            $this->entityManager->flush();  

            return [
                'statut' => $statut,
                'message' => $message,
                'user' => $user,
            ];
        }
        catch (\Throwable $e) 
        {
            $user->setApiEnabled($state);
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de l\'activation/desactivation de l\'acces API',
                'user' => $user,
            ];
        }
    }

    public function deleteAccount($user) : array
    {
        try
        {
            $oldUser = $user;

            // on efface logiquement l'utilisateur archive = true / apienabled = false, deletedAt  = DateTimeimmutable
            $user->setArchive(true);
            $user->setApiEnabled(false);
            $user->setDeletedAt(new DateTimeImmutable());
            $this->entityManager->flush();            
            
            return [
                'statut' => 'success',
                'message' => 'Compte supprimé avec succès',
                'user' => $user,
            ];
        }
        catch (\Throwable $e)
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de la suppression du compte',
                'user' => $oldUser,
            ];        
        }
    }

    public function createAccount($user) : array
    {
        try
        {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt(new DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush(); 

            return [
                'statut' => 'success',
                'message' => 'Vous etes inscrit sur le site, veuillez maintenant vous connecter',
                'user' => $user,
            ];

        }
        catch(\Throwable $e)
        {
            return [
                'statut' => 'danger',
                'message' => 'Une erreur a eu lieu lors de la création de votre compte',
                'user' => $user,
            ];
        }
    }
}