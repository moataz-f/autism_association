<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Entity\Seance;
use App\Forms\RapportType;
use App\Repository\RapportRepository;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/specialiste')]
#[IsGranted('ROLE_PERSONNEL')]
class SpecialisteController extends AbstractController
{
    #[Route('/planning', name: 'app_specialiste_planning')]
    public function planning(SeanceRepository $seanceRepo): Response
    {
        $user = $this->getUser();
        $sessions = $seanceRepo->findBy(['specialiste' => $user], ['date' => 'ASC', 'heureDebut' => 'ASC']);

        // Group sessions by date for the template
        $groupedSessions = [];
        foreach ($sessions as $session) {
            $date = $session->getDate()->format('Y-m-d');
            $groupedSessions[$date][] = $session;
        }

        return $this->render('specialiste/planning.html.twig', [
            'groupedSessions' => $groupedSessions,
            'sessions' => $sessions
        ]);
    }

    #[Route('/sessions', name: 'app_specialiste_sessions')]
    public function sessions(SeanceRepository $seanceRepo): Response
    {
        $user = $this->getUser();
        $sessions = $seanceRepo->findBy(['specialiste' => $user], ['date' => 'DESC', 'heureDebut' => 'DESC']);

        return $this->render('specialiste/sessions.html.twig', [
            'sessions' => $sessions
        ]);
    }

    #[Route('/reports', name: 'app_specialiste_reports')]
    public function reports(RapportRepository $rapportRepo): Response
    {
        $user = $this->getUser();
        $reports = $rapportRepo->findBy(['createdBy' => $user], ['date' => 'DESC']);

        return $this->render('specialiste/reports/index.html.twig', [
            'reports' => $reports
        ]);
    }

    #[Route('/reports/new', name: 'app_specialiste_report_new')]
    public function newReport(Request $request, EntityManagerInterface $em): Response
    {
        $report = new Rapport();
        $report->setCreatedBy($this->getUser());
        
        $form = $this->createForm(RapportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($report);
            $em->flush();

            $this->addFlash('success', 'تم إنشاء التقرير بنجاح');
            return $this->redirectToRoute('app_specialiste_reports');
        }

        return $this->render('specialiste/reports/new.html.twig', [
            'form' => $form,
            'report' => $report
        ]);
    }

    #[Route('/reports/{id}/edit', name: 'app_specialiste_report_edit')]
    public function editReport(Request $request, Rapport $report, EntityManagerInterface $em): Response
    {
        if ($report->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce rapport.');
        }

        $form = $this->createForm(RapportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'تم تحديث التقرير بنجاح');
            return $this->redirectToRoute('app_specialiste_reports');
        }

        return $this->render('specialiste/reports/edit.html.twig', [
            'form' => $form,
            'report' => $report
        ]);
    }
}
