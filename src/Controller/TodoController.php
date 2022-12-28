<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/todo')]
class TodoController extends AbstractController
{
    #[Route('/', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        //Afficher notre tableau de todo
        //sinon je l'initialise puis je l'affiche
        if (!$session->has('todos')) {
            $todos = [
                'achat' => 'acheter clé usb',
                'cours' => 'finir les cours',
                'correction' => 'corriger mes examens'
            ];

            $session->set('todos', $todos);
            $this->addFlash('info', 'la liste des todos vient d\'être initialisée');//addFlash(type, message)
        }

        //sinon si j'ai mon tableau de todo dans ma session je ne fait que l'afficher
        return $this->render('todo/index.html.twig');
    }

    #[Route(
        '/add/{name?test}/{content?test}', // valeur par défault ?test
        name: 'todo.add'
        // ,defaults: ['name' => 'sf6', //valeur par défault
        //     'content' =>'content par défault'] 
        )]
    public function addTodo(Request $request, $name, $content): RedirectResponse 
    {
        $session = $request->getSession();

        // Vérifier si j'ai mon tableau de TODO dans la sesssion
        if ($session->has(name:'todos')) {
        //si oui
            // Vérifier si on a déja un todo avec le même name
            $todos = $session->get('todos');

            if(isset($todos[$name])) {
                //si oui afficher erreur
                $this->addFlash('error', 'Le todo d\'id ' . $name . ' existe déjà dans la liste');//addFlash(type, message)
            }else{
                //si non on l'ajoute et on affiche un message de succès
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', 'Le todo d\'id '. $name . ' a été ajouté avec succès');
                
            }
               
             
        }else{
            
            //si non
                //afficher une erreur et on va rediriger vers le controller index
                
            $this->addFlash('error', 'la liste des todos n\'a pas encore été initialisée');//addFlash(type, message)

        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo(Request $request, $name): RedirectResponse 
    {
        $session = $request->getSession();

        // Vérifier si j'ai mon tableau de TODO dans la sesssion
        if ($session->has(name:'todos')) {
        //si oui
            // Vérifier si on a déja un todo avec le même name
            $todos = $session->get('todos');

            if(!isset($todos[$name])) {
                //si oui afficher erreur
                $this->addFlash('error', 'Le todo d\'id ' . $name . ' n\'existe pas dans la liste');//addFlash(type, message)
            }else{
                //si non on l'ajoute et on affiche un message de succès
                unset($todos[$name]);
                $session->set('todos', $todos);
                $this->addFlash('success', 'Le todo d\'id '. $name . ' a été supprimé avec succès');
                
            }
               
             
        }else{
            
            //si non
                //afficher une erreur et on va rediriger vers le controller index
                
            $this->addFlash('error', 'la liste des todos n\'a pas encore été initialisée');//addFlash(type, message)

        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request, $name, $content): RedirectResponse 
    {
        $session = $request->getSession();

        // Vérifier si j'ai mon tableau de TODO dans la sesssion
        if ($session->has(name:'todos')) {
        //si oui
            // Vérifier si on a déja un todo avec le même name
            $todos = $session->get('todos');

            if(!isset($todos[$name])) {
                //si oui afficher erreur
                $this->addFlash('error', 'Le todo d\'id ' . $name . ' n\'existe pas dans la liste');//addFlash(type, message)
            }else{
                //si non on l'ajoute et on affiche un message de succès
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', 'Le todo d\'id '. $name . ' a été modifié avec succès');
                
            }
               
             
        }else{
            
            //si non
                //afficher une erreur et on va rediriger vers le controller index
                
            $this->addFlash('error', 'la liste des todos n\'a pas encore été initialisée');//addFlash(type, message)

        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo(Request $request): RedirectResponse 
    {
        $session = $request->getSession();

        $session->remove('todos');

        return $this->redirectToRoute('todo');
    }
}
