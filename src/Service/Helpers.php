<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class Helpers
{
    public function __construct(private LoggerInterface $logger, CoreSecurity $security) {
    }

    public function SayCc()
    {
        $this->logger->info('je dis coucou');
        return 'cc';
    }

    public function getUser(): User
    {
        return $this->security->getUser();
    }
}
