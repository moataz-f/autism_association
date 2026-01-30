<?php

namespace App\Controller;

use App\Entity\DonationCampaign;
use App\Repository\DonationCampaignRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/donation')]
class DonationController extends AbstractController
{
    #[Route('/', name: 'app_donation_index', methods: ['GET'])]
    public function index(DonationCampaignRepository $donationCampaignRepository): Response
    {
        return $this->render('donation/index.html.twig', [
            'campaigns' => $donationCampaignRepository->findBy(['actif' => true], ['dateFin' => 'DESC']),
        ]);
    }

    #[Route('/{id}', name: 'app_donation_show', methods: ['GET'])]
    public function show(DonationCampaign $donationCampaign): Response
    {
        if (!$donationCampaign->isActif()) {
            throw $this->createNotFoundException('Cette campagne n\'est plus active.');
        }

        return $this->render('donation/show.html.twig', [
            'campaign' => $donationCampaign,
        ]);
    }
}
