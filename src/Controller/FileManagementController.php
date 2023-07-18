<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileManagementController extends AbstractController
{
    #[Route('/', name: 'acceuil')]
    public function index(): Response
    {
        return $this->render('file_management/acceuil.html.twig', [
            'controller_name' => 'FileManagementController',
        ]);
    }

    #[Route('/list/image', name: 'listImage')]
    public function listImage(): Response
    {
        $imagesCount = $this->countFileImage();       
        $images = $this->getListFileImage();       
        
        return $this->render('file_management/listImage.html.twig', [
            'images' => $images,
            'imagesCount' => $imagesCount,
        ]);
        
    }

    #[Route('/ajout/image', name: 'addImage')]
    public function addImage(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $imagesCount = $this->countFileImage();
                if (in_array($fileExtension, $validExtensions)) {
                    
                    $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images';
                    if (!file_exists($imageDirectory)) {
                        mkdir($imageDirectory, 0777, true);
                    }

                    $name = $fileName;
                    $fileWithPath = $imageDirectory."/".$name;
                    
                    if (file_exists($fileWithPath)) {
                        $message = 'L\'image existe deja';        
                        $images = $this->getListFileImage();
                        $imagesCount = $this->countFileImage();

                        return $this->render('file_management/addImage.html.twig', [
                            'images' => $images,
                            'message' => $message,
                            'imagesCount' => $imagesCount,
                        ]);
                    }else{
                        $file->move($imageDirectory, $name);
                        $message = 'L\'image a été uploadée avec succès.';        
                        $images = $this->getListFileImage();
                        $imagesCount = $this->countFileImage();

                        return $this->render('file_management/addImage.html.twig', [
                            'images' => $images,
                            'message' => $message,
                            'imagesCount' => $imagesCount,
                        ]);
                    }
                    
                } else {
                    $message = 'Le fichier sélectionné n\'est pas une image valide.';  

                    return $this->render('file_management/addImage.html.twig', [
                        'message' => $message,
                        'imagesCount' => $imagesCount,
                    ]);
                }

            }
        }

        return $this->render('file_management/addImage.html.twig');
    }

    #[Route('/suppression/image/{nameImage}', name: 'deleteImage')]
    public function deleteImage($nameImage): Response
    {
        $images = [];

        $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images';
        $filePath = $imageDirectory . '/' . $nameImage;

        if (!file_exists($filePath)) {
            $message = 'L\'image demandée n\'existe pas.';  
            $images = $this->getListFileImage();
            $imagesCount = $this->countFileImage(); 

            return $this->render('file_management/listImage.html.twig', [
                'message' => $message,
                'images' => $images,
                'imagesCount' => $imagesCount,
            ]);
        }

        unlink($filePath);

        $message = 'Image supprimer avec success.';  
        $images = $this->getListFileImage();
        $imagesCount = $this->countFileImage(); 

        return $this->render('file_management/listImage.html.twig', [
            'message' => $message,
            'images' => $images,
            'imagesCount' => $imagesCount,
        ]);
    }

    #[Route('/download/image/{nameImage}', name: 'downloadImage')]
    public function downloadImage($nameImage): Response
    {
        return $this->file('image/'.$nameImage);
    }

    public function countFileImage()
    {
        $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images';
        $imagesCount = 0;

        if (is_dir($imageDirectory)) {
            $files = scandir($imageDirectory);
            foreach ($files as $file) {
                $filePath = $imageDirectory . '/' . $file;
                if (is_file($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $imagesCount++;
                }
            }
        }

        return $imagesCount;
    }

    public function getListFileImage()
    {
        $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images';
        $images = [];
        
        if (is_dir($imageDirectory)) {
            $files = scandir($imageDirectory);
            foreach ($files as $file) {
                $filePath = $imageDirectory . '/' . $file;
                if (is_file($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $images[] = $file;
                }
            }
        }

        return $images;
    }
}
