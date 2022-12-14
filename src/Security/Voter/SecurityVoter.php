<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Consumer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityVoter extends Voter
{
    public const MANAGE = 'manage';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $consumer): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::MANAGE])
            && $consumer instanceof \App\Entity\Consumer;
    }

    protected function voteOnAttribute(string $attribute, mixed $consumer, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if($this->security->isGranted('ROLE_ADMIN')) return true;
        if(null === $consumer->getClient()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::MANAGE:
                return $this->canManage($user, $consumer);
                // logic to determine if the user can MANAGE
                // return true or false
                break;
        }

        return false;
    }

    private function canManage(Client $user, Consumer $consumer): bool
    {
        if($this->security->isGranted('ROLE_ADMIN')|| $user === $consumer->getClient()) return true;
        return false;
    }
}
