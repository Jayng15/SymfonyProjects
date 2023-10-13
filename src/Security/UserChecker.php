<?php

namespace App\Security;

use DateTime;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserChecker implements UserCheckerInterface {

    
    /**
     * @param User $user
     */
    public function checkPreAuth(UserInterface $user): Void
    {
        if($user->getBannedUntil() == null)
        {
            return;
        }        
        $now = new DateTime();

        if($now < $user->getBannedUntil())
        {
            throw new AccessDeniedException('The user is banned');
        }
    }

    /**
     * @param User $user
     */
    public function checkPostAuth(UserInterface $user): Void
    {

    }
}

