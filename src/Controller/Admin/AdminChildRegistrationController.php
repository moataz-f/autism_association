<?php

namespace App\Controller\Admin;

use App\Entity\Beneficiaire;
use App\Entity\ChildRegistrationRequest;
use App\Repository\ChildRegistrationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/child-registrations')]
#[IsGranted('ROLE_ADMIN')]
class AdminChildRegistrationController extends AbstractController
{
    #[Route('/', name: 'admin_child_registration_index')]
    public function index(ChildRegistrationRequestRepository $repository): Response
    {
        return $this->render('admin/child_registration/index.html.twig', [
            'requests' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}/approve', name: 'admin_child_registration_approve', methods: ['POST'])]
    public function approve(ChildRegistrationRequest $registrationRequest, EntityManagerInterface $em): Response
    {
        if ($registrationRequest->getStatus() === 'PENDING') {
            // 1. Create a new Beneficiaire (Child)
            $child = new Beneficiaire();
            $child->setNom($registrationRequest->getNom());
            $child->setPrenom($registrationRequest->getPrenom());
            $child->setDateNaissance($registrationRequest->getDateNaissance());
            $child->setGenre($registrationRequest->getGenre());
            $child->setNiveauAutisme($registrationRequest->getNiveauAutisme());
            $child->setActif(true);
            
            // 2. Link the child to the parent (User)
            $parent = $registrationRequest->getParent();
            $child->addParent($parent);
            
            // 3. Update request status
            $registrationRequest->setStatus('APPROVED');
            
            $em->persist($child);
            $em->flush();
            
            $this->addFlash('success', 'تم قبول طلب التسجيل وإنشاء ملف الطفل بنجاح.');
        }

        return $this->redirectToRoute('admin_child_registration_index');
    }

    #[Route('/{id}/reject', name: 'admin_child_registration_reject', methods: ['POST'])]
    public function reject(ChildRegistrationRequest $registrationRequest, EntityManagerInterface $em): Response
    {
        if ($registrationRequest->getStatus() === 'PENDING') {
            $registrationRequest->setStatus('REJECTED');
            $em->flush();
            $this->addFlash('warning', 'تم رفض طلب التسجيل.');
        }

        return $this->redirectToRoute('admin_child_registration_index');
    }
}
