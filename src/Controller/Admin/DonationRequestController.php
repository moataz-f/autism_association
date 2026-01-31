<?php

namespace App\Controller\Admin;

use App\Entity\DonationCampaign;
use App\Entity\DonationRequest;
use App\Forms\DonationRequestType;
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
            'stats' => [
                'pending' => $repository->count(['statut' => 'pending']),
                'confirmed' => $repository->count(['statut' => 'confirmed']),
                'count_materiel' => $repository->count(['type' => 'materiel', 'statut' => 'confirmed']),
                'total_amount' => $repository->createQueryBuilder('r')
                    ->select('SUM(r.montant)')
                    ->where('r.statut = :status')
                    ->setParameter('status', 'confirmed')
                    ->getQuery()
                    ->getSingleScalarResult() ?? 0,
            ]
        ]);
    }

    #[Route('/{id}/confirm', name: 'admin_donation_request_confirm', methods: ['POST'])]
    public function confirm(DonationRequest $donationRequest, EntityManagerInterface $em): Response
    {
        if ($donationRequest->getStatut() === 'pending') {
            $donationRequest->setStatut('confirmed');
            
            // Generate receipt number if not exists
            if (!$donationRequest->getReceiptNumber()) {
                $donationRequest->setReceiptNumber('REC-' . date('Ymd') . '-' . sprintf('%04d', $donationRequest->getId()));
            }

            if ($donationRequest->getType() === 'financier' && $donationRequest->getCampaign()) {
                $campaign = $donationRequest->getCampaign();
                $newTotal = (float) $campaign->getMontantCollecte() + (float) $donationRequest->getMontant();
                $campaign->setMontantCollecte((string) $newTotal);

                // Auto-switch logic
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
            
            $em->flush();
            $this->addFlash('success', 'تم تأكيد التبرع وإصدار رقم الوصل: ' . $donationRequest->getReceiptNumber());
        }

        return $this->redirectToRoute('admin_donation_request_index');
    }

    #[Route('/{id}/receipt', name: 'admin_donation_request_receipt', methods: ['GET'])]
    public function receipt(DonationRequest $request): Response
    {
        if ($request->getStatut() !== 'confirmed') {
            $this->addFlash('error', 'لا يمكن إصدار وصل لتبرع غير مؤكد.');
            return $this->redirectToRoute('admin_donation_request_index');
        }

        return $this->render('admin/donation_request/receipt.html.twig', [
            'donation' => $request
        ]);
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
            if ($donationRequest->getType() === 'financier' && $campaign) {
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
            $em->flush(); // flush once to get ID for receipt number

            if (!$donationRequest->getReceiptNumber()) {
                $donationRequest->setReceiptNumber('REC-' . date('Ymd') . '-' . sprintf('%04d', $donationRequest->getId()));
                $em->flush();
            }

            $this->addFlash('success', 'تم إضافة التبرع اليدوي بنجاح. رقم الوصل: ' . $donationRequest->getReceiptNumber());
            return $this->redirectToRoute('admin_donation_request_index');
        }

        return $this->render('admin/donation_request/manual.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
