<?php

namespace App\Controller\Admin;

use App\Entity\DonationCampaign;
use App\Entity\DonationRequest;
use App\Formss\DonationRequestType;
use App\Repository\DonationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/donation-requests')]
class DonationRequestController extends AbstractController
{
    #[Route('/', name: 'admin_donation_request_index')]
    public function index(DonationRequestRepository $repository): Response
    {
        return $this->render('admin/donation_request/index.html.twig', [
            'requests' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}/confirm', name: 'admin_donation_request_confirm', methods: ['POST'])]
    public function confirm(DonationRequest $donationRequest, EntityManagerInterface $em): Response
    {
        if ($donationRequest->getStatut() === 'pending') {
            $donationRequest->setStatut('confirmed');
            
            $campaign = $donationRequest->getCampaign();
            if ($campaign) {
                $newTotal = (float) $campaign->getMontantCollecte() + (float) $donationRequest->getMontant();
                $campaign->setMontantCollecte((string) $newTotal);

                // Auto-switch logic: if campaign is filled
                if ($campaign->isFilled()) {
                    $campaign->setPrincipale(false);
                    $campaign->setActif(false); // Optionally deactivate filled campaign or just remove principal status

                    // Find next available campaign to set as principal
                    $nextCampaign = $em->getRepository(DonationCampaign::class)->findOneBy(
                        ['actif' => true, 'principale' => false],
                        ['id' => 'ASC']
                    );
                    
                    if ($nextCampaign) {
                        $nextCampaign->setPrincipale(true);
                        $this->addFlash('info', 'الحملة اكتملت! تم تحويل الحملة الرئيسية تلقائياً إلى : ' . $nextCampaign->getTitre());
                    }
                }
            }
            
            $em->flush();
            $this->addFlash('success', 'تم تأكيد التبرع وإضافته للحملة بنجاح.');
        }

        return $this->redirectToRoute('admin_donation_request_index');
    }

    #[Route('/{id}/reject', name: 'admin_donation_request_reject', methods: ['POST'])]
    public function reject(DonationRequest $donationRequest, EntityManagerInterface $em): Response
    {
        if ($donationRequest->getStatut() === 'pending') {
            $donationRequest->setStatut('rejected');
            $em->flush();
            $this->addFlash('warning', 'تم رفض طلب التبرع.');
        }

        return $this->redirectToRoute('admin_donation_request_index');
    }

    #[Route('/manual/new', name: 'admin_donation_request_manual', methods: ['GET', 'POST'])]
    public function manualNew(Request $request, EntityManagerInterface $em): Response
    {
        $donationRequest = new DonationRequest();
        $donationRequest->setStatut('confirmed'); // Automatic confirmation for manual entry
        
        $form = $this->createForm(DonationRequestType::class, $donationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign = $donationRequest->getCampaign();
            if ($campaign) {
                $newTotal = (float) $campaign->getMontantCollecte() + (float) $donationRequest->getMontant();
                $campaign->setMontantCollecte((string) $newTotal);

                // Reuse auto-switch logic
                if ($campaign->isFilled()) {
                    $campaign->setPrincipale(false);
                    $campaign->setActif(false);
                    $nextCampaign = $em->getRepository(DonationCampaign::class)->findOneBy(
                        ['actif' => true, 'principale' => false],
                        ['id' => 'ASC']
                    );
                    if ($nextCampaign) {
                        $nextCampaign->setPrincipale(true);
                    }
                }
            }

            $em->persist($donationRequest);
            $em->flush();

            $this->addFlash('success', 'تم إضافة التبرع اليدوي بنجاح وتحديث الحملة.');
            return $this->redirectToRoute('admin_donation_request_index');
        }

        return $this->render('admin/donation_request/manual.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
