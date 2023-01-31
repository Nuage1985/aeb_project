<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    #[Route('/personne/add', name: 'app_personne')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        //$this->getDoctrine() : Version Sf < 5
        $entityManager = $doctrine->getManager();

        $personne = new Personne();
        $personne->setFirstname(firstname: 'Renaud');
        $personne->setName(name: 'Fontaine');
        $personne->setAge(age: 37);

        $personne2 = new Personne();
        $personne2->setFirstname(firstname: 'Jessica');
        $personne2->setName(name: 'Peretti');
        $personne2->setAge(age: 35);



        // Ajouter l'opération d'insertion de la personne dans ma transaction
        $entityManager->persist($personne);
        $entityManager->persist($personne2);

        // Exécute la transaction Todo
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }
}
