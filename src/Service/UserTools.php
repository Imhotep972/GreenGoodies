<?php
// src/Service/UserTools.php
namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTools
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $hasher)
    {

    }
    public function toogleApiAccess($user): array
    {
        try
        {
            $state = $user->getApiEnabled();
            $newState = !$state;
            $statut = 'success';
            $message = ($newState)? 'Accès API Activé' : 'Accès API désactivé';

            $user->setApiEnabled($newState);
            $this->entityManager->flush();  

            return [
                'statut' => $statut,
                'message' => $message,
            ];
        }
        catch (\Throwable $e) 
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de l\'activation/desactivation de l\'acces API.'
            ];
        }
    }

    public function deleteAccount($user) : array
    {
        try
        {
            // on indique que l'utilisateur n'est plus actif
            $user->setArchive(true);
            $user->setApiEnabled(false);
            $user->setDeletedAt(new DateTimeImmutable());
            $this->entityManager->flush();            
            
            return [
                'statut' => 'success',
                'message' => 'Compte supprimé avec succès',
            ];
        }
        catch (\Throwable $e)
        {
            return [
                'statut' => 'danger',
                'message' => 'Un problème est survenu lors de la suppression du compte.'
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
                'message' => 'Vous etes inscrit sur le site, veuillez maintenant vous connecter.',
            ];

        }
        catch(\Throwable $e)
        {
            return [
                'statut' => 'danger',
                'message' => 'Une erreur a eu lieu lors de la création de votre compte.',
            ];
        }
    }
}