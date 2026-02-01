<?php

namespace App\Controller;

use App\Entity\ChildRegistrationRequest;
use App\Form\ChildRegistrationRequestType;
use App\Repository\ChildRegistrationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parent/registration')]
#[IsGranted('ROLE_PARENT')]
class ChildRegistrationController extends AbstractController
{
    #[Route('/', name: 'app_parent_registration_index')]
    public function index(ChildRegistrationRequestRepository $requestRepo): Response
    {
        $requests = $requestRepo->findBy(['parent' => $this->getUser()], ['createdAt' => 'DESC']);
        return $this->render('parent/registration/index.html.twig', [
            'requests' => $requests,
        ]);
    }

    #[Route('/new', name: 'app_parent_registration_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $regRequest = new ChildRegistrationRequest();
        $regRequest->setParent($this->getUser());

        $form = $this->createForm(ChildRegistrationRequestType::class, $regRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($regRequest);
            $em->flush();

            $this->addFlash('success', 'تم تقديم طلب التسجيل بنجاح. سيتم مراجعته من قبل الإدارة.');
            return $this->redirectToRoute('app_parent_registration_index');
        }

        return $this->render('parent/registration/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
