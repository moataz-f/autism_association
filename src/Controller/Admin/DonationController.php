<?php
namespace App\Controller\Admin;

use App\Entity\DonationCampaign;
use App\Forms\DonationCampaignType;
use App\Repository\DonationCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/donation-campaigns')]
class DonationController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'form.factory' => '?Symfony\Component\Form\FormFactoryInterface',
        ]);
    }

    #[Route('/', name: 'admin_donation_index')]
    public function index(DonationCampaignRepository $repository): Response
    {
        $campaigns = $repository->findBy([], ['id' => 'DESC']);
        
        return $this->render('admin/donation/index.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }

    #[Route('/new', name: 'admin_donation_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $campaign = new DonationCampaign();
        $form = $this->createForm(DonationCampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($campaign->isPrincipale()) {
                $em->createQuery('UPDATE App\Entity\DonationCampaign c SET c.principale = false')
                    ->execute();
            }

            $em->persist($campaign);
            $em->flush();

            $this->addFlash('success', 'تم إضافة الحملة بنجاح!');
            return $this->redirectToRoute('admin_donation_index');
        }

        return $this->render('admin/donation/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_donation_edit')]
    public function edit(Request $request, DonationCampaign $campaign, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DonationCampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($campaign->isPrincipale()) {
                $em->createQuery('UPDATE App\Entity\DonationCampaign c SET c.principale = false WHERE c.id != :id')
                    ->setParameter('id', $campaign->getId())
                    ->execute();
            }

            $em->flush();

            $this->addFlash('success', 'تم تحديث الحملة بنجاح!');
            return $this->redirectToRoute('admin_donation_index');
        }

        return $this->render('admin/donation/edit.html.twig', [
            'form' => $form,
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_donation_delete', methods: ['POST'])]
    public function delete(Request $request, DonationCampaign $campaign, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campaign->getId(), $request->request->get('_token'))) {
            $em->remove($campaign);
            $em->flush();
            $this->addFlash('success', 'تم حذف الحملة بنجاح!');
        }

        return $this->redirectToRoute('admin_donation_index');
    }
}