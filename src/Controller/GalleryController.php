<?php

namespace App\Controller;

use App\Repository\GalleryImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gallery')]
class GalleryController extends AbstractController
{
    #[Route('/', name: 'app_gallery_index', methods: ['GET'])]
    public function index(GalleryImageRepository $galleryImageRepository): Response
    {
        return $this->render('gallery/index.html.twig', [
            'images' => $galleryImageRepository->findBy([], ['updatedAt' => 'DESC']),
        ]);
    }
}
