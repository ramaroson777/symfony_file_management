<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageFileController extends AbstractController
{
    #[Route('/image/file', name: 'app_image_file')]
    public function index(): Response
    {
        return $this->render('image_file/index.html.twig', [
            'controller_name' => 'ImageFileController',
        ]);
    }
}
