<?php
namespace App\Controller;

use App\Repository\PageContentRepository;
use App\Repository\GalleryImageRepository;
use App\Repository\DonationCampaignRepository;
use App\Repository\ActualiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        PageContentRepository $contentRepo,
        GalleryImageRepository $galleryRepo,
        DonationCampaignRepository $donationRepo,
        ActualiteRepository $newsRepo
    ): Response {
        // Récupérer le contenu dynamique
        $heroContent = $contentRepo->findOneBy(['sectionKey' => 'hero', 'actif' => true]);
        $aboutContent = $contentRepo->findOneBy(['sectionKey' => 'about', 'actif' => true]);
        
        // Récupérer les images de la galerie
        $galleryImages = $galleryRepo->findBy(
            ['actif' => true],
            ['ordre' => 'ASC'],
            6
        );
        
        // Récupérer la campagne de donation principale
        $mainCampaign = $donationRepo->findOneBy(['principale' => true, 'actif' => true]);

        // Récupérer les dernières actualités
        $recentNews = $newsRepo->findBy(['actif' => true], ['createdAt' => 'DESC'], 3);
        
        return $this->render('home/index.html.twig', [
            'heroContent' => $heroContent,
            'aboutContent' => $aboutContent,
            'galleryImages' => $galleryImages,
            'mainCampaign' => $mainCampaign,
            'news' => $recentNews,
        ]);
    }
}