<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RandomFileController extends AbstractController
{
    #[Route('/random/file', name: 'app_random_file')]
    public function index(): Response
    {
        return $this->render('random_file/index.html.twig', [
            'controller_name' => 'RandomFileController',
        ]);
    }
}
