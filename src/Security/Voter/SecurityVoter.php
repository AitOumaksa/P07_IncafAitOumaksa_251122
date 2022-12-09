<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Consumer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityVoter extends Voter
{
    protected function supports(string $attribute, mixed $consumer): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['MANAGE'])
            && $consumer instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $consumer, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'MANAGE':
                return $this->canManage($user, $consumer);
                // logic to determine if the user can MANAGE
                // return true or false
                break;
        }

        return false;
    }

    private function canManage($user, Consumer $consumer): bool
    {
        return $user === $consumer->getClient();
    }
}
