<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/first', name: 'app_first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig', [
            'name' => 'Fontaine',
            'firstname' => 'Renaud'
        ]);
    }

    #[Route('/sayHello/{name}/{firstname}', name: 'app_say.hello')]
    public function sayHello( $name, $firstname): Response
    {

        return $this->render('first/hello.html.twig', [
            'name'=> $name,
            'firstname'=> $firstname,
        ]);
    }

}
