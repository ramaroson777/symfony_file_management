<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileManagementController extends AbstractController
{
    private $videoExtension = ['mp4', '3gp'];
    private $pdfExtension = ['pdf'];
    private $imageExtension = ['jpg', 'jpeg', 'png', 'gif'];
    private $randomExtension = ['jpg', 'jpeg', 'png', 'gif','mp4', '3gp','pdf'];

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
        $imagesCount = $this->countFile('images',$this->imageExtension);       
        $images = $this->getListFile('images',$this->imageExtension);       
        
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
                // $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $imagesCount = $this->countFile('images',$this->imageExtension);
                if (in_array($fileExtension, $this->imageExtension)) {
                    
                    $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images';
                    if (!file_exists($imageDirectory)) {
                        mkdir($imageDirectory, 0777, true);
                    }

                    $name = $fileName;
                    $fileWithPath = $imageDirectory."/".$name;
                    
                    if (file_exists($fileWithPath)) {
                        $message = 'L\'image existe deja';        
                        $images = $this->getListFile('images',$this->imageExtension);
                        $imagesCount = $this->countFile('images',$this->imageExtension);

                        return $this->render('file_management/addImage.html.twig', [
                            'images' => $images,
                            'message' => $message,
                            'imagesCount' => $imagesCount,
                        ]);
                    }else{
                        $file->move($imageDirectory, $name);
                        $message = 'L\'image a été uploadée avec succès.';        
                        $images = $this->getListFile('images',$this->imageExtension);
                        $imagesCount = $this->countFile('images',$this->imageExtension);

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
            $images = $this->getListFile('images',$this->imageExtension);
            $imagesCount = $this->countFile('images',$this->imageExtension); 

            return $this->render('file_management/listImage.html.twig', [
                'message' => $message,
                'images' => $images,
                'imagesCount' => $imagesCount,
            ]);
        }

        unlink($filePath);

        $message = 'Image supprimer avec success.';  
        $images = $this->getListFile('images',$this->imageExtension);
        $imagesCount = $this->countFile('images',$this->imageExtension); 

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

    #[Route('/list/pdf', name: 'listPdf')]
    public function listPdf(): Response
    {
        $pdfCount = $this->countFile('pdf',$this->pdfExtension);       
        $dataPdf = $this->getListFile('pdf',$this->pdfExtension);       
        
        return $this->render('file_management/listPdf.html.twig', [
            'dataPdf' => $dataPdf,
            'pdfCount' => $pdfCount,
        ]);
        
    }

    #[Route('/ajout/pdf', name: 'addPdf')]
    public function addPdf(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                // $validExtensions = ['pdf'];
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $pdfCount = $this->countFile('pdf',$this->pdfExtension); 
                if (in_array($fileExtension, $this->pdfExtension)) {
                    
                    $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/pdf';
                    if (!file_exists($imageDirectory)) {
                        mkdir($imageDirectory, 0777, true);
                    }

                    $name = $fileName;
                    $fileWithPath = $imageDirectory."/".$name;
                    
                    if (file_exists($fileWithPath)) {
                        $message = 'Le PDF existe deja';        
                        $dataPdf = $this->getListFile('pdf',$this->pdfExtension);
                        $pdfCount = $this->countFile('pdf',$this->pdfExtension);

                        return $this->render('file_management/addPdf.html.twig', [
                            'dataPdf' => $dataPdf,
                            'message' => $message,
                            'pdfCount' => $pdfCount,
                        ]);
                    }else{
                        $file->move($imageDirectory, $name);
                        $message = 'Le PDF a été uploadée avec succès.';        
                        $dataPdf = $this->getListFile('pdf',$this->pdfExtension);
                        $pdfCount = $this->countFile('pdf',$this->pdfExtension);

                        return $this->render('file_management/addPdf.html.twig', [
                            'dataPdf' => $dataPdf,
                            'message' => $message,
                            'pdfCount' => $pdfCount,
                        ]);
                    }
                    
                } else {
                    $message = 'Le fichier sélectionné n\'est pas un PDF.';  

                    return $this->render('file_management/addImage.html.twig', [
                        'message' => $message,
                        'pdfCount' => $pdfCount,
                    ]);
                }

            }
        }

        return $this->render('file_management/addPdf.html.twig');
    }

    #[Route('/download/pdf/{namePdf}', name: 'downloadPdf')]
    public function downloadPdf($namePdf): Response
    {
        return $this->file('pdf/'.$namePdf);
    }

    #[Route('/suppression/pdf/{namePdf}', name: 'deletePdf')]
    public function deletePdf($namePdf): Response
    {
        $images = [];

        $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/pdf';
        $filePath = $pdfDirectory . '/' . $namePdf;

        if (!file_exists($filePath)) {
            $message = 'Le Pdf n\'existe pas.';  
            $dataPdf = $this->getListFile('pdf',$this->pdfExtension);
            $pdfCount = $this->countFile('pdf',$this->pdfExtension); 

            return $this->render('file_management/listPdf.html.twig', [
                'message' => $message,
                'dataPdf' => $dataPdf,
                'pdfCount' => $pdfCount,
            ]);
        }

        unlink($filePath);

        $message = 'PDF supprimer avec success.';  
        $dataPdf = $this->getListFile('pdf',$this->pdfExtension);
        $pdfCount = $this->countFile('pdf',$this->pdfExtension); 

        return $this->render('file_management/listPdf.html.twig', [
            'message' => $message,
            'dataPdf' => $dataPdf,
            'pdfCount' => $pdfCount,
        ]);
    }

    #[Route('/ajout/video', name: 'addVideo')]
    public function addVideo(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                // $validExtensions = ['mp4','3gp'];
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $videoCount = $this->countFile('video',$this->videoExtension);
                if (in_array($fileExtension, $this->videoExtension)) {
                    
                    $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/video';
                    if (!file_exists($imageDirectory)) {
                        mkdir($imageDirectory, 0777, true);
                    }

                    $name = $fileName;
                    $fileWithPath = $imageDirectory."/".$name;
                    
                    if (file_exists($fileWithPath)) {
                        $message = 'Le video existe deja';        
                        $dataVideo = $this->getListFile('video',$this->videoExtension);
                        $videoCount = $this->countFile('video',$this->videoExtension);

                        return $this->render('file_management/addVideo.html.twig', [
                            'dataVideo' => $dataVideo,
                            'message' => $message,
                            'videoCount' => $videoCount,
                        ]);
                    }else{
                        $file->move($imageDirectory, $name);
                        $message = 'Le video a été uploadée avec succès.';        
                        $dataVideo = $this->getListFile('video',$this->videoExtension);
                        $videoCount = $this->countFile('video',$this->videoExtension);

                        return $this->render('file_management/addVideo.html.twig', [
                            'dataVideo' => $dataVideo,
                            'message' => $message,
                            'videoCount' => $videoCount,
                        ]);
                    }
                    
                } else {
                    $message = 'Le fichier sélectionné n\'est pas un video.';  

                    return $this->render('file_management/addVideo.html.twig', [
                        'message' => $message,
                        'videoCount' => $videoCount,
                    ]);
                }

            }
        }

        return $this->render('file_management/addVideo.html.twig');
    }

    #[Route('/list/video', name: 'listVideo')]
    public function listVideo(): Response
    {
        $videoCount = $this->countFile('video',$this->videoExtension);       
        $dataVideo = $this->getListFile('video',$this->videoExtension);       
    
        return $this->render('file_management/listVideo.html.twig', [
            'dataVideo' => $dataVideo,
            'videoCount' => $videoCount,
        ]);
        
    }

    #[Route('/download/video/{nameVideo}', name: 'downloadVideo')]
    public function downloadVideo($nameVideo): Response
    {
        return $this->file('video/'.$nameVideo);
    }

    #[Route('/suppression/video/{nameVideo}', name: 'deleteVideo')]
    public function deleteVideo($nameVideo): Response
    {
        $images = [];

        $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/video';
        $filePath = $pdfDirectory . '/' . $nameVideo;

        if (!file_exists($filePath)) {
            $message = 'Le video n\'existe pas.';  
            $dataVideo = $this->getListFile('video',$this->videoExtension);
            $videoCount = $this->countFile('video',$this->videoExtension); 

            return $this->render('file_management/listVideo.html.twig', [
                'message' => $message,
                'dataVideo' => $dataVideo,
                'videoCount' => $videoCount,
            ]);
        }

        unlink($filePath);

        $message = 'Video supprimer avec success.';  
        $dataVideo = $this->getListFile('video',$this->videoExtension);
        $videoCount = $this->countFile('video',$this->videoExtension); 

        return $this->render('file_management/listVideo.html.twig', [
            'message' => $message,
            'dataVideo' => $dataVideo,
            'videoCount' => $videoCount,
        ]);
    }

    #[Route('/ajout/fichier/random', name: 'addRandom')]
    public function addRandom(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileCount = $this->countFile('random',$this->randomExtension);
                if (in_array($fileExtension, $this->randomExtension)) {
                    
                    $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/random';
                    if (!file_exists($imageDirectory)) {
                        mkdir($imageDirectory, 0777, true);
                    }

                    $name = $fileName;
                    $fileWithPath = $imageDirectory."/".$name;
                    
                    if (file_exists($fileWithPath)) {
                        $message = 'Le fichier existe deja';        
                        $dataFile = $this->getListFile('random',$this->randomExtension);
                        $fileCount = $this->countFile('random',$this->randomExtension);

                        return $this->render('file_management/addRandom.html.twig', [
                            'dataFile' => $dataFile,
                            'message' => $message,
                            'fileCount' => $fileCount,
                        ]);
                    }else{
                        $file->move($imageDirectory, $name);
                        $message = 'Le fichier a été uploadée avec succès.';        
                        $dataFile = $this->getListFile('random',$this->randomExtension);
                        $fileCount = $this->countFile('random',$this->randomExtension);

                        return $this->render('file_management/addRandom.html.twig', [
                            'dataFile' => $dataFile,
                            'message' => $message,
                            'fileCount' => $fileCount,
                        ]);
                    }
                    
                } else {
                    $message = 'Le fichier sélectionné n\'est pas valide.';  

                    return $this->render('file_management/addRandom.html.twig', [
                        'message' => $message,
                        'fileCount' => $fileCount,
                    ]);
                }

            }
        }

        return $this->render('file_management/addRandom.html.twig');
    }

    #[Route('/list/fichier/random', name: 'listRandom')]
    public function listRandom(): Response
    {
        $fileCount = $this->countFile('random',$this->randomExtension);       
        $dataFile = $this->getListFile('random',$this->randomExtension);       
    
        return $this->render('file_management/listRandom.html.twig', [
            'dataFile' => $dataFile,
            'fileCount' => $fileCount,
        ]);
        
    }

    #[Route('/download/fichier/random/{nameFile}', name: 'downloadRandom')]
    public function downloadFile($nameFile): Response
    {
        return $this->file('random/'.$nameFile);
    }

    #[Route('/suppression/fichier/random/{nameFile}', name: 'deleteRandom')]
    public function deleteRandom($nameFile): Response
    {
        $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/random';
        $filePath = $pdfDirectory . '/' . $nameFile;

        if (!file_exists($filePath)) {
            $message = 'Le fichier n\'existe pas.';  
            $dataFile = $this->getListFile('random',$this->randomExtension);
            $fileCount = $this->countFile('random',$this->randomExtension); 

            return $this->render('file_management/listRandom.html.twig', [
                'message' => $message,
                'dataFile' => $dataFile,
                'fileCount' => $fileCount,
            ]);
        }

        unlink($filePath);

        $message = 'fichier supprimer avec success.';  
        $dataFile = $this->getListFile('random',$this->randomExtension);
        $fileCount = $this->countFile('random',$this->randomExtension); 

        return $this->render('file_management/listRandom.html.twig', [
            'message' => $message,
            'dataFile' => $dataFile,
            'fileCount' => $fileCount,
        ]);
    }

    public function countFile($nameFolder,$extension)
    {
        $fileDirectory = $this->getParameter('kernel.project_dir') . '/public/'.$nameFolder;
        $fileCount = 0;

        if (is_dir($fileDirectory)) {
            $files = scandir($fileDirectory);
            foreach ($files as $file) {
                $filePath = $fileDirectory . '/' . $file;
                if (is_file($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), $extension)) {
                    $fileCount++;
                }
            }
        }

        return $fileCount;
    }

    public function getListFile($nameFolder,$extension)
    {
        $fileDirectory = $this->getParameter('kernel.project_dir') . '/public/'.$nameFolder;
        $dataFile = [];
        
        if (is_dir($fileDirectory)) {
            $files = scandir($fileDirectory);
            foreach ($files as $file) {
                $filePath = $fileDirectory . '/' . $file;
                if (is_file($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), $extension)) {
                    $dataFile[] = $file;
                }
            }
        }

        return $dataFile;
    }

    

}
