<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Repository\ActualiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/news')]
class ActualiteController extends AbstractController
{
    #[Route('/', name: 'app_actualite_index')]
    public function index(ActualiteRepository $repository): Response
    {
        return $this->render('actualite/index.html.twig', [
            'news' => $repository->findBy(['actif' => true], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{slug}', name: 'app_actualite_show')]
    public function show(Actualite $actualite): Response
    {
        if (!$actualite->isActif()) {
            throw $this->createNotFoundException('هذا الخبر غير متاح حالياً.');
        }

        return $this->render('actualite/show.html.twig', [
            'item' => $actualite,
        ]);
    }
}
