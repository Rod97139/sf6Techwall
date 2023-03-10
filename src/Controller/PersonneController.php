<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\PdfService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;



#[
    Route('personne'),
    IsGranted('ROLE_USER')       
    ]
class PersonneController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private Helpers $helpers,
        private EventDispatcherInterface $dispatcher
        ) {}

    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        return $this->render('personne/index.html.twig', [
                'personnes' => $personnes,
            ]);
        
    }

    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf)
    {
        $html = $this->render('personne/detail.html.twig', ['personne' => $personne]);
        $pdf->showPdfFile($html);
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

    #[
        Route('/all/{page?1}/{nbre?12}', name: 'personne.list.all'),
        IsGranted('ROLE_USER')       
        ]// 2eme methode de restriction
    public function indexAll(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]); // Vs code rale pour rien 'count()'
        // dd($nbPersonne);
        $nbrePage = ceil($nbPersonne / $nbre);
        $personnes = $repository->findBy([], [], $nbre, ($page - 1) * $nbre );

        //Event listener
        $listAllPersonneEvent = new ListAllPersonneEvent(count($personnes));
        $this->dispatcher->dispatch($listAllPersonneEvent, ListAllPersonneEvent::LIST_ALL_PERSONNE_EVENT);
        
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
        if (!$personne) {
            $this->addFlash('error', 'La personne n\'existe pas');
            return $this->redirectToRoute('personne.list');
        }

        return $this->render('personne/detail.html.twig', [
                'personne' => $personne,
            ]);
        
    }


    #[Route('/edit/{id?0}', name: 'personne.edit')] //add + edit 
    public function addPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mail
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // 3eme methode de restriction 
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
        //Est-ce que le form a ??t?? asujeti

        if ($form->isSubmitted() && $form->isValid()) {
        // si oui, on va ajouter l'objet dans la base de donn??es

                $photo = $form->get('photo')->getData();
                if ($photo) {
                    $directory = $this->getParameter('picture_directory');
                    $personne->setImage($uploaderService->uploadFile($photo, $directory));
                }
                
            if ($new) {
                $message = ' a ??t?? ajout??';
                $personne->setCreatedBy($this->getUser());

            }else{
                $message = ' a ??t?? ??dit??';
            }

            $manager = $doctrine-> getManager();
            $manager->persist($personne);
            $manager->flush();
            // Rediriger vers la liste de personne avec un flash de succ??s

            if ($new) {
                // On a cr???? notre ??vennement
                $addPersonneEvent = new AddPersonneEvent($personne);
                // On va maintenant dispatcher cet ??vennement
                $this->dispatcher->dispatch($addPersonneEvent, AddPersonneEvent::ADD_PERSONNE_EVENT);

            }

            
            // $mailMessage = $personne->getFirstname() . ' ' . $personne->getName() . $message;
            
            $this->addFlash('success', $personne->getName() . $message);
            // $mail->sendEmail(content: $mailMessage);
            
            return $this->redirectToRoute('personne.list');
        } else {
            return $this->render('personne/add-personne.html.twig', [
            'form' => $form->createView()
            ]);
        }
    }

    #[
        Route('/delete/{id<\d+>}', name: 'personne.delete'),
        IsGranted('ROLE_ADMIN')
    ]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
        //r??cup??rer l'id
        if ($personne) {
            //Si la personne exite => supprimer et retourner un flash de comfirmation
            $manager = $doctrine->getManager();
            //Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Execute la transaction
            $manager->flush();
            $this->addFlash('success', 'La personne a ??t?? supprimer avec succ??s');
            
        } else {
            // Sinon retourner un Flash d'erreur
            
            $this->addFlash('error', 'La personne n\'existe pas');
            
        }
        
        return $this->redirectToRoute('personne.list.all');
    }
    
    #[Route('/update/{id<\d+>}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age)
    {
        //V??rifier que la personne existe
        if ($personne) {
            //Si la personne existe => mettre a jour les data + flash succ??s
            $personne->setName($name)
            ->setFirstname($firstname)
            ->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            
            $manager->flush();
            
            $this->addFlash('success', 'La personne a ??t?? mise ?? jour avec succ??s');
        }else{
            
            //Sinon => flash error
            $this->addFlash('error', 'La personne n\'existe pas');  
        }

        return $this->redirectToRoute('personne.list.all');
    }
}
