<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoController extends AbstractController
{
    #[Route('/to_do', name: 'app_to_do')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        // Afficher notre tableau de toDo
        // sinon je l'initialise puis l'affiche.
        if ( !$session->has(name: 'toDo') ){
            $toDo = [
                'achat' => 'acheter clé USB',
                'cours' => 'Finaliser mon cours',
                'correction' => 'Corriger mes examens',
            ];
            $session->set('toDo', $toDo);
            $this->addFlash(type: 'info', message: "La liste des to do viens d'être initialisée.");
        }
        // Si j'ai mon tableau en session je ne fait que l'afficher
        return $this->render('to_do/index.html.twig');
    }

    #[Route('/to_do/add/{name}/{content}', name: 'app_to_do.add')]
    public function addToDo(Request $request, $name, $content): Response
    {
        $session = $request->getSession();

        // Vérifier si j'ai mon tableau toDo en session
        if ( $session->has(name: 'toDo') ){
            // Si oui
            // Si toDo avec le même name
            $toDo = $session->get(name: 'toDo');
            if ( isset($toDo[$name]) ){
                // Oui = Afficher erreur
                $this->addFlash(type: 'error', message: "Le to Do d'id $name existe déjà dans la liste");
            } else {
                // Non = On l'ajoute et message de succès
                $toDo[$name] = $content;
                $session->set('toDo', $toDo);
                $this->addFlash(type: 'success', message: "Le to Do d'id $name a été ajouté");
            }


        } else {
            // Si non
            // Afficher une erreur et redirection vers le controller initial 'index'
            $this->addFlash(type: 'error', message: "La liste des to Do n'est pas encore initialisée.");
        }
        return $this->redirectToRoute(route: 'app_to_do');

    }

    #[Route('/to_do/update/{name}/{content}', name: 'app_to_do.update')]
    public function updateToDo(Request $request, $name, $content): RedirectResponse{
        $session = $request->getSession();

        // Vérifier si j'ai mon tableau toDo en session
        if ( $session->has(name: 'toDo') ){
            // Si oui
            // Si toDo avec le même name
            $toDo = $session->get(name: 'toDo');
            if ( !isset($toDo[$name]) ){
                // Oui = Afficher erreur
                $this->addFlash(type: 'error', message: "Le to Do d'id $name n'existe pas");
            } else {
                // Non = On l'ajoute et message de succès
                $toDo[$name] = $content;
                $session->set('toDo', $toDo);
                $this->addFlash(type: 'success', message: "Le to Do d'id $name a été modifié");
            }


        } else {
            // Si non
            // Afficher une erreur et redirection vers le controller initial 'index'
            $this->addFlash(type: 'error', message: "La liste des to Do n'est pas encore initialisée.");
        }
        return $this->redirectToRoute(route: 'app_to_do');

    }

    #[Route('/to_do/delete/{name}', name: 'app_to_do.delete')]
    public function deleteToDo(Request $request, $name): RedirectResponse{
        $session = $request->getSession();

        // Vérifier si j'ai mon tableau toDo en session
        if ( $session->has(name: 'toDo') ){
            // Si oui
            // Si toDo avec le même name
            $toDo = $session->get(name: 'toDo');
            if ( !isset($toDo[$name]) ){
                // Oui = Afficher erreur
                $this->addFlash(type: 'error', message: "Le to Do d'id $name n'existe pas dans la liste");
            } else {
                // Non = On l'ajoute et message de succès
                unset($toDo[$name]);
                $session->set('toDo', $toDo);
                $this->addFlash(type: 'success', message: "Le to Do d'id $name a été supprimé");
            }

        } else {
            // Si non
            // Afficher une erreur et redirection vers le controller initial 'index'
            $this->addFlash(type: 'error', message: "La liste des to Do n'est pas encore initialisée.");
        }
        return $this->redirectToRoute(route: 'app_to_do');

    }

    #[Route('/to_do/reset', name: 'app_to_do.reset')]
    public function resetToDo(Request $request): RedirectResponse{
        $session = $request->getSession();
        $session->remove(name: 'toDo');
        return $this->redirectToRoute(route: 'app_to_do');

    }
}
