<?php

namespace App\Services;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;



class Helpers
{

    public function __construct(private LoggerInterface $logger, private Security $security){}

    public function sayCc(){
        $this->logger->info(message: 'Je dis cc');
        return 'cc';
    }

    public function getUser(): User {
        return $this->security->getUser();
    }
}