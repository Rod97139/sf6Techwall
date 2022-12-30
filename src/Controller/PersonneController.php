<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    #[Route('/all/age/{ageMin<\d+>}/{ageMax<\d+>}', name: 'personne.list.age')]
    public function personneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);
        // dd($personnes); //Vs code rale pour rien encore une fois 

        return $this->render('personne/index.html.twig', [
                'personnes' => $personnes,
            ]);
        
    }

    #[Route('/stats/age/{ageMin<\d+>}/{ageMax<\d+>}', name: 'personne.list.stats')]
    public function statsPersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statPersonnesByAgeInterval($ageMin, $ageMax);
        // dd($stats); //Vs code rale pour rien encore une fois 
        
        return $this->render('personne/stats.html.twig', [
                'stats' => $stats[0],
                'ageMin' => $ageMin,
                'ageMax' => $ageMax
            ]);
        
    }

    #[Route('/all/{page?1}/{nbre?12}', name: 'personne.list.all')]
    public function indexAll(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]); // Vs code rale pour rien 'count()'
        // dd($nbPersonne);
        $nbrePage = ceil($nbPersonne / $nbre);
        $personnes = $repository->findBy([], [], $nbre, ($page - 1) * $nbre );
        
        return $this->render('personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => true,
                'nbrePage' => $nbrePage,
                'page' => $page,
                'nbre' => $nbre
            ]);
        
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
    {
    //     $repository = $doctrine->getRepository(Personne::class);
    //     $personne = $repository->find($id);

        if (!$personne) {
            $this->addFlash('error', 'La personne n\'existe pas');
            return $this->redirectToRoute('personne.list');
        }

        return $this->render('personne/detail.html.twig', [
                'personne' => $personne,
            ]);
        
    }


    #[Route('/edit/{id?0}', name: 'personne.edit')] //add + edit 
    public function addPersonne(Personne $personne = null, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)//: Response
    {
        $new =false;

        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }
        // $personne est l'image de notre formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        // Mon form va aller traiter la requete
        $form->handleRequest($request);
        //Est-ce que le form a été asujeti
        if ($form->isSubmitted() && $form->isValid()) {
            // si oui, on va ajouter l'objet dans la base de données

            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('picture_directory'), // le repertoire est a ajouté dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'photoname' property to store the PDF file name
                // instead of its contents
                $personne->setImage($newFilename);
            }


            $manager = $doctrine-> getManager();
            $manager->persist($personne);
            $manager->flush();
            // Rediriger vers la liste de personne avec un flash de succès

            if ($new) {
                $message = ' a été ajouté';
            }else{
                $message = ' a été édité';
            }

            $this->addFlash('success', $personne->getName() . $message);
            return $this->redirectToRoute('personne.list');
        } else {
            return $this->render('personne/add-personne.html.twig', [
            'form' => $form->createView()
            ]);
        }
    }

    #[Route('/delete/{id<\d+>}', name: 'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
        //récupérer l'id
        if ($personne) {
            //Si la personne exite => supprimer et retourner un flash de comfirmation
            $manager = $doctrine->getManager();
            //Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Execute la transaction
            $manager->flush();
            $this->addFlash('success', 'La personne a été supprimer avec succès');
            
        } else {
            // Sinon retourner un Flash d'erreur
            
            $this->addFlash('error', 'La personne n\'existe pas');
            
        }
        
        return $this->redirectToRoute('personne.list.all');
    }
    
    #[Route('/update/{id<\d+>}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age)
    {
        //Vérifier que la personne existe
        if ($personne) {
            //Si la personne existe => mettre a jour les data + flash succès
            $personne->setName($name)
            ->setFirstname($firstname)
            ->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            
            $manager->flush();
            
            $this->addFlash('success', 'La personne a été mise à jour avec succès');
        }else{
            
            //Sinon => flash error
            $this->addFlash('error', 'La personne n\'existe pas');  
        }

        return $this->redirectToRoute('personne.list.all');
    }
}
