<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use App\Services\Helpers;
use App\Form\PersonneType;
use App\Services\MailerService;
use App\Services\PdfService;
use Psr\Log\LoggerInterface;
use App\Services\UploaderService;
use Attribute;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Route('personne'), IsGranted('ROLE_USER')]
class PersonneController extends AbstractController
{
    // Exemple injection de dépendances
    public function __construct(
        private LoggerInterface $logger,
        private Helpers $helpers,
        private EventDispatcherInterface $dispatcher
        )
    {
    }

    #[Route('/', name: 'app_personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        return $this->render(view: 'personne/index.html.twig', parameters: ['personnes' => $personnes]);
    }
    #[Route('/pdf/{id}', name: 'app_personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf)
    {
        $html = $this->render(view: 'personne/detail.html.twig', parameters: ['personne' => $personne]);
        $pdf->showPdfFile($html);
    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'app_personne.list.alls.age')]
    public function PersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render(view: 'personne/index.html.twig', parameters: ['personnes' => $personnes]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'app_personne.list.stats.age')]
    public function statsPersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonneByAgeInterval($ageMin, $ageMax);

        return $this->render(view: 'personne/stats.html.twig', parameters: [
            'stats' => $stats[0],
            'ageMin'=> $ageMin,
            'ageMax' => $ageMax
        ]);
    }

    //Pagination
    #[Route('/alls/{page?1}/{nbre?12}', name: 'app_personne.list.alls'),
    IsGranted("ROLE_USER")
    ]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        // Test services
        //echo $this->helpers->sayCc();

        $repository = $doctrine->getRepository(persistentObject: Personne::class);

        $nbPersonne = $repository->count([]);
        $nbPage = ceil(num: $nbPersonne / $nbre);
        $personnes = $repository->findBy([], [], $nbre, offset: ($page-1) * $nbre);

        //Evenement
        $listAllPersonneEvent = new ListAllPersonnesEvent(count($personnes));
        $this->dispatcher->dispatch($listAllPersonneEvent, ListAllPersonnesEvent::LIST_ALL_PERSONNE_EVENT);

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
    public function detail(Personne $personne = null): Response
    {
        if (!$personne) {
            $this->addFlash(type: 'error', message: "La personne n'existe pas.");
            return $this->redirectToRoute(route: 'app_personne.list');
        }

        return $this->render(view: 'personne/detail.html.twig', parameters: ['personne' => $personne]);
    }

    #[Route('/edit/{id?0}', name: 'app_personne.edit')]
    public function addPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mailer,
    ): Response {
        // Verifier si role Admin
        $this->denyAccessUnlessGranted(attribute: 'ROLE_ADMIN');

        // Déterminer si on creer un profil ou si on l'update
        $new = false;

        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }

        // Ici $personne est l'image de notre formulaire
        // Génération du formulaire
        $form = $this->createForm(PersonneType::class, $personne);

        //Suppression des champs de formulaire non requis ex CreatedAt (Ou supprimer dans Form/PersonneType)
        $form->remove(name: 'createdAt');
        $form->remove(name: 'updatedAt');

        //Traitement
        //dump($request);
        // Mon formulaire va traiter la requête
        $form->handleRequest($request);

        // Le formulaire est-il soumis ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Oui -> ajout de cet nouvel objet Personne dans la BDD

            // Traitement d'une image de profil
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $directory = $this->getParameter(name: 'personne_directory');
                $personne->setImage($uploaderService->uploadImage($photo, $directory));
            }

            //Message de succès + récupération de l'utilisateurt
            if ($new) {
                $message = " a été ajouté avec succès !";
                $personne->setCreatedBy( $this->getUser() );
            } else {
                $message = " a été mis à jour avec succès !";
            }

            //$this->getDoctrine() : Version Sf < 5
            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();

            //Event Dispatcher
            if ($new) {
                // Création de l'évenement
                $addPersonneEvent = new AddPersonneEvent($personne);
                // Dispacth
                $this->dispatcher->dispatch($addPersonneEvent, AddPersonneEvent::ADD_PERSONNE_EVENT);
            }

            // Message
            $this->addFlash(type: 'success', message: "Le profil ".$personne->getName().$message);

            //Redirection vers la liste des personnes
            return $this->redirectToRoute('app_personne.list.alls');
        }

        //Non -> Affiche le formulaire
        else {
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/delete/{id}', name: 'app_personne.delete'), IsGranted('ROLE_ADMIN')]
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
        if ($personne) {
            // Si elle existe => mise à jour + message de succès
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);

            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();

            //Message
            $this->addFlash(type: 'success', message: "La personne a été mise à jour avec succès");
        } else {
            // Sinon message d'erreur
            $this->addFlash(type: 'error', message: "Personne inexistante");
        }
        return $this->redirectToRoute(route: 'app_personne.list.        ');
    }
}
