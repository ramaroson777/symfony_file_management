<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfFileController extends AbstractController
{
    #[Route('/pdf/file', name: 'app_pdf_file')]
    public function index(): Response
    {
        return $this->render('pdf_file/index.html.twig', [
            'controller_name' => 'PdfFileController',
        ]);
    }
}
