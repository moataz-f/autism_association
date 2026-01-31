<?php

namespace App\Controller\Admin;

use App\Entity\PersonnelTask;
use App\Forms\PersonnelTaskType;
use App\Repository\PersonnelTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/task')]
#[IsGranted('ROLE_ADMIN')]
class AdminTaskController extends AbstractController
{
    #[Route('/', name: 'admin_task_index', methods: ['GET'])]
    public function index(PersonnelTaskRepository $taskRepository): Response
    {
        return $this->render('admin/task/index.html.twig', [
            'tasks' => $taskRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'admin_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new PersonnelTask();
        
        // Auto-fill from query params
        $personnelId = $request->query->get('personnel');
        $eventId = $request->query->get('event');
        
        if ($personnelId) {
            $personnel = $entityManager->getRepository(\App\Entity\Personnel::class)->find($personnelId);
            if ($personnel) $task->setPersonnel($personnel);
        }
        
        if ($eventId) {
            $event = $entityManager->getRepository(\App\Entity\PersonnelEvent::class)->find($eventId);
            if ($event) $task->setEvent($event);
        }

        $form = $this->createForm(PersonnelTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche créée et assignée avec succès.');
            return $this->redirectToRoute('admin_task_index');
        }

        return $this->render('admin/task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PersonnelTask $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonnelTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Tâche mise à jour.');
            return $this->redirectToRoute('admin_task_index');
        }

        return $this->render('admin/task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_task_delete', methods: ['POST'])]
    public function delete(Request $request, PersonnelTask $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'Tâche supprimée.');
        }

        return $this->redirectToRoute('admin_task_index');
    }
}
