<?php

namespace App\Services;

use Psr\Log\LoggerInterface;

class Helpers
{

    public function __construct(private LoggerInterface $logger){}

    public function sayCc(){
        $this->logger->info(message: 'Je dis cc');
        return 'cc';
    }
}