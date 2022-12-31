<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use Psr\Log\LoggerInterface;

class PersonneListener {
    
    public function __construct(private LoggerInterface $logger) {}

    public function onPersonneAdd(AddPersonneEvent $event){
        $this->logger->debug('coucou je suis en train d\'écouter l\'évennement personne.add et une personne ajoutée et c\'est ' . $event->getPersonne()->getName());
    }
}