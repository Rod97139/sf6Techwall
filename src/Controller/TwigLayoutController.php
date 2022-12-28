<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwigLayoutController extends AbstractController
{
    #[Route('/twig', name: 'twig_layout')]
    public function index(): Response
    {
        return $this->render('twig_layout/index.html.twig', [
            'controller_name' => 'TwigLayoutController',
        ]);
    }

    #[Route('/twig/layout', name: 'layout')]
    public function layout(): Response
    {
        return $this->render('layout.html.twig');
    }
}
