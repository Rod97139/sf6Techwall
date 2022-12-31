<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use Psr\Log\LoggerInterface;

class PersonneListener {
    
    public function __construct(private LoggerInterface $logger) {}

    public function onPersonneAdd(AddPersonneEvent $event){
        $this->logger->debug('coucou je suis en train d\'écouter l\'évennement personne.add et une personne ajoutée et c\'est ' . $event->getPersonne()->getName());
    }

    public function onListAllPersonne(ListAllPersonneEvent $event){
        $this->logger->debug('Le nombre de personnes dans la page est de  ' . $event->getNbPersonne());
    }

    public function onListAllPersonne2(ListAllPersonneEvent $event){
        $this->logger->debug('2eme listener Le nombre de personnes dans la page est de  ' . $event->getNbPersonne());
    }
}