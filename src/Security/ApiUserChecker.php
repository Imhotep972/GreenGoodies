<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user) : void
    {
        /** @var User $user */
        if ($user->isArchive()) { // erreur 403
            throw new CustomUserMessageAuthenticationException('Compte supprimé, accès non autorisé.',[],403);
        }

        if (!$user->isApiEnabled()) { // erreur 403
            throw new CustomUserMessageAuthenticationException('Accès API non autorisé',[],403);
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        // rien ici
    }
}