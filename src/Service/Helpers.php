<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class Helpers
{
    public function __construct(private LoggerInterface $logger, private CoreSecurity $security) {
    }

    public function SayCc()
    {
        $this->logger->info('je dis coucou');
        return 'cc';
    }

    public function getUser(): User
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                return $user;
            }
        }
    }
}
