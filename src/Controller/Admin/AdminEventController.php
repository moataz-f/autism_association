<?php

namespace App\Controller\Admin;

use App\Entity\PersonnelEvent;
use App\Forms\PersonnelEventType;
use App\Repository\PersonnelEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/event')]
#[IsGranted('ROLE_ADMIN')]
class AdminEventController extends AbstractController
{
    #[Route('/', name: 'admin_event_index', methods: ['GET'])]
    public function index(PersonnelEventRepository $eventRepository): Response
    {
        return $this->render('admin/event/index.html.twig', [
            'events' => $eventRepository->findBy([], ['startDate' => 'DESC']),
        ]);
    }

    #[Route('/{id}', name: 'admin_event_show', methods: ['GET'])]
    public function show(PersonnelEvent $event): Response
    {
        return $this->render('admin/event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/new', name: 'admin_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new PersonnelEvent();
        $form = $this->createForm(PersonnelEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé et assigné avec succès.');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PersonnelEvent $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonnelEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Événement mis à jour.');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_event_delete', methods: ['POST'])]
    public function delete(Request $request, PersonnelEvent $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash('success', 'Événement supprimé.');
        }

        return $this->redirectToRoute('admin_event_index');
    }
}
