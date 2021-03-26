<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getEmailVerified()) {
            throw new CustomUserMessageAccountStatusException("Merci de vérifier votre email en cliquant sur le lien que nous vous avons envoyé.");
        }

        if ($user->isBannedNow()) {
            throw new CustomUserMessageAccountStatusException("Vous êtes banni jusqu'au " . $user->getBannedUntil()->format('d/m/Y'));
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }
}
