<?php

namespace App\Controller;

use App\Entity\DonationCampaign;
use App\Entity\DonationRequest;
use App\Formss\DonationRequestType;
use App\Repository\DonationCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/request/new', name: 'app_donation_request', methods: ['GET', 'POST'])]
    public function electronicDonation(Request $request, EntityManagerInterface $em): Response
    {
        $donationRequest = new DonationRequest();
        
        // Pre-fill campaign if ID is provided in query
        if ($campaignId = $request->query->get('campaign')) {
            $campaign = $em->getRepository(DonationCampaign::class)->find($campaignId);
            if ($campaign) {
                if ($campaign->isFilled()) {
                    $this->addFlash('warning', 'هذه الحملة قد اكتملت بالفعل. شكراً لسخائكم!');
                    return $this->redirectToRoute('app_donation_index');
                }
                $donationRequest->setCampaign($campaign);
            }
        }

        $form = $this->createForm(DonationRequestType::class, $donationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign = $donationRequest->getCampaign();
            if ($campaign && $campaign->isFilled()) {
                $this->addFlash('error', 'عذراً، هذه الحملة اكتملت للتو. يمكنك التبرع لحملة أخرى.');
                return $this->redirectToRoute('app_donation_index');
            }

            $em->persist($donationRequest);
            $em->flush();

            $this->addFlash('success', 'شكراً لك! تم إرسال طلب التبرع بنجاح. سنتصل بك قريباً للتأكيد.');
            return $this->redirectToRoute('app_donation_index');
        }

        return $this->render('donation/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
