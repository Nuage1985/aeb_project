<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        return $this->render(view: 'personne/index.html.twig', parameters: ['personnes' => $personnes]);
    }

    /*#[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(ManagerRegistry $doctrine, $id): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $personne = $repository->find($id);

        if(!$personne){
            $this->addFlash(type: 'error', message: "La personne d'id $id n'existe pas.");
            return $this->redirectToRoute(route: 'personne.list');
        }

        return $this->render(view: 'personne/detail.html.twig', parameters: ['personne' => $personne]);
    }*/

    // Avec Param converters
    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response {
        if(!$personne){
            $this->addFlash(type: 'error', message: "La personne n'existe pas.");
            return $this->redirectToRoute(route: 'personne.list');
        }

        return $this->render(view: 'personne/detail.html.twig', parameters: ['personne' => $personne]);
    }
    #[Route('/add', name: 'app_personne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        //$this->getDoctrine() : Version Sf < 5
        $entityManager = $doctrine->getManager();

        $personne = new Personne();
        $personne->setFirstname(firstname: 'Renaud');
        $personne->setName(name: 'Fontaine');
        $personne->setAge(age: 37);

        //$personne2 = new Personne();
        //$personne2->setFirstname(firstname: 'Jessica');
        //$personne2->setName(name: 'Peretti');
        //$personne2->setAge(age: 35);



        // Ajouter l'opération d'insertion de la personne dans ma transaction
        $entityManager->persist($personne);
        //$entityManager->persist($personne2);

        // Exécute la transaction Todo
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/alls/{page?1}/{nbre?12}', name: 'app_personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response {

        $repository = $doctrine->getRepository(persistentObject: Personne::class);

        $nbPersonne = $repository->count([]);
        $nbPage = ceil(num: $nbPersonne / $nbre);
        $personnes = $repository->findBy([], [], $nbre, offset: ($page-1) * $nbre);

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrePage' => $nbPage,
            'page' => $page,
            'nbre' => $nbre,
        ]);


    }
}
