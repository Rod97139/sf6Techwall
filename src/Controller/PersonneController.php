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
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        return $this->render('personne/index.html.twig', [
                'personnes' => $personnes,
            ]);
        
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
    {
    //     $repository = $doctrine->getRepository(Personne::class);
    //     $personne = $repository->find($id);

        if (!$personne) {
            $this->addFlash('error', 'La personne d\'id ' . $id . ' n\'existe pas');
            return $this->redirectToRoute('personne.list');
        }

        return $this->render('personne/detail.html.twig', [
                'personne' => $personne,
            ]);
        
    }


    #[Route('/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine)//: Response
    {

        $entityManager = $doctrine-> getManager();
        
        // $personne = new Personne();
        // $personne->setFirstname('Rod')
        //          ->setName('MINGO')
        //          ->setAge(31);

        // $personne2 = new Personne();
        // $personne2->setFirstname('John')
        //          ->setName('Doe')
        //          ->setAge(25);

        //Ajouter l'opération d'insertion de la personne dans ma transaction

        // $entityManager->persist($personne);
        // $entityManager->persist($personne2);
        
        //Execute la transaction Todo

        // $entityManager->flush();
        //      return $this->render('personne/detail.html.twig', [
        //     'personne' => $personne,
        // ]);
    }
}
