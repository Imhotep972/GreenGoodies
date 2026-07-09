<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/******************************************* 
    Utilisation des composants suivants :
        EntityManagerInterface $entityManager
        UserPasswordHasherInterface $hasher 
        
    Méthodes de Gestion de Compte
        createAccount(?User $user): array
            Prend en charge l'enregistrement initial d'un nouvel utilisateur dans le système.
            Hachage du mot de passe : Récupère le mot de passe en clair présent temporairement dans l'entité $user, le chiffre via le hasher, puis réaffecte le mot de passe sécurisé à l'entité.
            Horodatage : Initialise la date de création du compte à la date actuelle via DateTimeImmutable.
            Enregistre la nouvelle entité en base de données (persist + flush).
            Traite les éventuelles erreurs
            
        deleteAccount(?User $user): array
            Exécute une suppression logique (soft delete) de l'utilisateur afin de préserver l'intégrité référentielle des données (par exemple, pour les statistiques ou l'historique légal).
            Données utilisateur : Passe le flag archive à true, désactive les accès API (apiEnabled = false) et ajoute la date de suppression dans deletedAt.
            Données liées (Commandes) : Parcourt l'ensemble des commandes rattachées à l'utilisateur ($user->getOrders()) pour basculer également leur état archive à true.
            Traite les éventuelles erreurs

        toggleApiAccess(?User $user): array
            Agit comme un interrupteur (toggle) pour inverser l'état actuel des droits d'accès à l'API d'un utilisateur.
            Si l'accès était actif, il est désactivé (et inversement).
            En cas d'erreur ou d'exception durant le processus de sauvegarde, l'état initial de l'accès API est restauré sur l'entité avant de retourner le tableau d'erreur.
*******************************************/

class UserService
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $hasher)
    {

    }

    public function toggleApiAccess(?User $user): array
    {
        $state = false;
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

    public function deleteAccount(?User $user) : array
    {
        $oldUser = $user;
        try
        {
            $oldUser = $user;

            // on supprime logiquement l'utilisateur archive = true / apienabled = false, deletedAt  = DateTimeimmutable
            $user->setArchive(true);
            $user->setApiEnabled(false);
            $user->setDeletedAt(new DateTimeImmutable());
            $this->entityManager->flush();            

            // on supprime logiquement les commandes de l'utilisateur supprimé
            $orders = $user->getOrders();

            foreach($orders as $order)
            {
                $order->setArchive(true);
            }

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

    public function createAccount(?User $user) : array
    {
        try
        {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt(new DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush(); 

            return [
                'statut' => 'success',
                'message' => 'Vous etes inscrit sur le site',
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