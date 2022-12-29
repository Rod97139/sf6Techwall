<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    #[Route('/personne/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
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

        $entityManager->persist($personne);
        $entityManager->persist($personne2);
        
        //Execute la transaction Todo

        $entityManager->flush();
             return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }
}
