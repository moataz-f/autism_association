<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Forms\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/seance')]
#[IsGranted('ROLE_STAFF')]
class SeanceController extends AbstractController
{
    #[Route('/', name: 'admin_seance_index', methods: ['GET'])]
    public function index(SeanceRepository $seanceRepository): Response
    {
        return $this->render('admin/seance/index.html.twig', [
            'seances' => $seanceRepository->findBy([], ['date' => 'DESC', 'heureDebut' => 'DESC']),
        ]);
    }

    #[Route('/planning', name: 'admin_seance_planning', methods: ['GET'])]
    public function planning(): Response
    {
        return $this->render('admin/seance/planning.html.twig');
    }

    #[Route('/api/events', name: 'admin_seance_api_events', methods: ['GET'])]
    public function apiEvents(SeanceRepository $seanceRepository): Response
    {
        $seances = $seanceRepository->findAll();
        $events = [];

        foreach ($seances as $seance) {
            $events[] = [
                'id' => $seance->getId(),
                'title' => $seance->getTitre() . ' (' . ($seance->getType() === 'Individuelle' ? 'فردية' : 'جماعية') . ')',
                'start' => $seance->getDate()->format('Y-m-d') . 'T' . $seance->getHeureDebut()->format('H:i:s'),
                'end' => $seance->getDate()->format('Y-m-d') . 'T' . $seance->getHeureFin()->format('H:i:s'),
                'color' => $seance->getType() === 'Individuelle' ? '#8E97FD' : '#A78BFA',
                'extendedProps' => [
                    'specialiste' => $seance->getSpecialiste()->getPrenom() . ' ' . $seance->getSpecialiste()->getNom(),
                    'beneficiaires' => count($seance->getBeneficiaires()),
                    'statut' => $seance->getEtat()
                ],
                'url' => $this->generateUrl('admin_seance_show', ['id' => $seance->getId()])
            ];
        }

        return $this->json($events);
    }

    #[Route('/new', name: 'admin_seance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SeanceRepository $seanceRepository): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Conflict Detection
            $conflicts = $seanceRepository->findOverlappingSessions(
                $seance->getSpecialiste(),
                $seance->getDate(),
                $seance->getHeureDebut(),
                $seance->getHeureFin()
            );

            if (count($conflicts) > 0) {
                $this->addFlash('error', 'هذا الأخصائي لديه حصة أخرى في نفس الوقت.');
            } else {
                $entityManager->persist($seance);
                $entityManager->flush();

                $this->addFlash('success', 'تمت إضافة الحصة بنجاح.');
                return $this->redirectToRoute('admin_seance_index');
            }
        }

        return $this->render('admin/seance/new.html.twig', [
            'seance' => $seance,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_seance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seance $seance, EntityManagerInterface $entityManager, SeanceRepository $seanceRepository): Response
    {
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Conflict Detection (excluding current ID)
            $conflicts = $seanceRepository->findOverlappingSessions(
                $seance->getSpecialiste(),
                $seance->getDate(),
                $seance->getHeureDebut(),
                $seance->getHeureFin(),
                $seance->getId()
            );

            if (count($conflicts) > 0) {
                $this->addFlash('error', 'هذا الأخصائي لديه حصة أخرى في نفس الوقت.');
            } else {
                $entityManager->flush();

                $this->addFlash('success', 'تم تحديث الحصة بنجاح.');
                return $this->redirectToRoute('admin_seance_index');
            }
        }

        return $this->render('admin/seance/edit.html.twig', [
            'seance' => $seance,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_seance_show', methods: ['GET'])]
    public function show(Seance $seance): Response
    {
        return $this->render('admin/seance/show.html.twig', [
            'seance' => $seance,
        ]);
    }

    #[Route('/{id}', name: 'admin_seance_delete', methods: ['POST'])]
    public function delete(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($seance);
            $entityManager->flush();
            $this->addFlash('success', 'تم حذف الحصة بنجاح.');
        }

        return $this->redirectToRoute('admin_seance_index');
    }
}
