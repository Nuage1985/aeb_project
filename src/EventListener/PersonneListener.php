<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener
{
    public function __construct(private LoggerInterface $logger)
    {
        
    }

    public function onPersonneAdd(AddPersonneEvent $event){
        $this->logger->debug("Coucou je suis en train d'écouter l'événement personne.add et ". $event->getPersonne()->getName());
    }

    public function onListAllPersonnes(ListAllPersonnesEvent $event){
        $this->logger->debug("Le nombre de personne dans la BDD est de ". $event->getNbPersonne());
    }

    public function onListAllPersonnes2(ListAllPersonnesEvent $event){
        $this->logger->debug("Second Listener");
    }

    public function logKernelRequest(KernelEvent $event){
        dd($event->getRequest());
    }
}