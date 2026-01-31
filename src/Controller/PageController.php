<?php

namespace App\Controller;

use App\Entity\StaticPage;
use App\Repository\StaticPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/pages', name: 'app_page_index')]
    public function index(StaticPageRepository $repository): Response
    {
        return $this->render('page/index.html.twig', [
            'pages' => $repository->findBy(['actif' => true], ['titre' => 'ASC']),
        ]);
    }
    #[Route('/p/{slug}', name: 'app_page_show')]
    public function show(StaticPage $page): Response
    {
        if (!$page->isActif()) {
            throw $this->createNotFoundException('هذه الصفحة غير متاحة حالياً.');
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }
}
