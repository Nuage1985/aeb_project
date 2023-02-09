<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'app_personne.list')]
    public function index(ManagerRegistry $doctrine): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        return $this->render(view: 'personne/index.html.twig', parameters: ['personnes' => $personnes]);
    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'app_personne.list.alls.age')]
    public function PersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render(view: 'personne/index.html.twig', parameters: ['personnes' => $personnes]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'app_personne.list.stats.age')]
    public function statsPersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonneByAgeInterval($ageMin, $ageMax);

        return $this->render(view: 'personne/stats.html.twig', parameters: [
            'stats' => $stats[0],
            'ageMin'=> $ageMin,
            'ageMax' => $ageMax
        ]);
    }

    //Pagination
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

    // Avec Param converters
    #[Route('/{id<\d+>}', name: 'app_personne.detail')]
    public function detail(Personne $personne = null): Response {
        if(!$personne){
            $this->addFlash(type: 'error', message: "La personne n'existe pas.");
            return $this->redirectToRoute(route: 'app_personne.list');
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

    #[Route('/delete/{id}', name: 'app_personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {

        //Récupérer la personne
        if ($personne) {
            // Si elle existe => suppression et retourner un flashMessage de succès
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Exécution de la transaction
            $manager->flush();
            $this->addFlash(type: 'success', message: "La personne a été supprimé avec succès");
        } else {
            // Sinon FlashMessage d'erreur
            $this->addFlash(type: 'error', message: "Personne inexistante");
        }

        return $this->redirectToRoute(route: 'app_personne.list.alls');
    }

    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'app_personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age): RedirectResponse
    {
        // Vérifier que la personne existe
        if ($personne){
            // Si elle existe => mise à jour + message de succès
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash(type: 'success', message: "La personne a été mise à jour avec succès");
        } else {
            // Sinon message d'erreur
            $this->addFlash(type: 'error', message: "Personne inexistante");
        }
        return $this->redirectToRoute(route: 'app_personne.list.alls');
    }

}
