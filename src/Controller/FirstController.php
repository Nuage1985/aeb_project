<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{

    #[Route('/template', name: 'app_template')]
    public function template(): Response
    {
        return $this->render(view: 'template.html.twig');
    }
    #[Route('/order/{maVar}', name: 'app_test.order.route')]
    public function testOrderRoute($maVar): Response
    {
        return new Response(content: "<html lang='fr'><body>$maVar</body></html>");
    }

    #[Route('/first', name: 'app_first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig', [
            'name' => 'Fontaine',
            'firstname' => 'Renaud',
        ]);
    }

    //#[Route('/sayHello/{name}/{firstname}', name: 'app_say.hello')]
    public function sayHello( $name, $firstname): Response
    {

        return $this->render('first/hello.html.twig', [
            'name'=> $name,
            'firstname'=> $firstname,
            'path' => '     '
        ]);
    }

    #[Route(
        'multi/{entier1<\d+>}/{entier2<\d+>}',
        name: 'app_multiplication',
        /*requirements: [
            'entier1' => '/d+',
            'entier2' => '/d+',
            ]*/
    )]
    public function multiplication($entier1, $entier2) :Response
    {
        $resultat = $entier1 * $entier2;
        return new Response(content: "<h1>$resultat</h1>");

    }

}
