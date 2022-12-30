<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class Helpers
{
    public function __construct(private LoggerInterface $logger) {
    }

    public function SayCc()
    {
        $this->logger->info('je dis coucou');
        return 'cc';
    }
}
