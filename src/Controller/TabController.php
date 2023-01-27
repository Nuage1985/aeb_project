<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TabController extends AbstractController
{
    // "?" donner une valeur par défaut
    #[Route('/tab/{nb<\d+>?5}', name: 'app_tab')]
    public function index($nb): Response
    {
        $notes = [];
        for ($i = 0; $i<$nb ; $i++){
            $notes[] = rand(0,20);
        }
        return $this->render('tab/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/tab/users', name: 'app_tab.users')]
    public function users(): Response
    {
        $users =
            [
                ['firstname' => 'Jean', 'name' => 'Dupont', 'age' => '35'],
                ['firstname' => 'Pierre', 'name' => 'Pauljaques', 'age' => '38'],
                ['firstname' => 'Shifu', 'name' => 'Mi', 'age' => '75'],
            ];

        return  $this->render(view: 'tab/users.html.twig', parameters: [
            'users' => $users
        ]);
    }
}
