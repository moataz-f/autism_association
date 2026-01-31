<?php

namespace App\Controller;

use App\Entity\PersonnelTask;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/personnel/task')]
#[IsGranted('ROLE_PERSONNEL')]
class PersonnelTaskController extends AbstractController
{
    #[Route('/{id}/toggle', name: 'app_personnel_task_toggle', methods: ['POST'])]
    public function toggleStatus(PersonnelTask $task, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !method_exists($user, 'getPersonnel') || $task->getPersonnel() !== $user->getPersonnel()) {
            return new JsonResponse(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $newStatus = ($task->getStatus() === 'COMPLETED') ? 'PENDING' : 'COMPLETED';
        $task->setStatus($newStatus);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'newStatus' => $newStatus,
            'message' => 'Statut mis à jour'
        ]);
    }
}
