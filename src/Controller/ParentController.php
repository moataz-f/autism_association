<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BeneficiaireRepository;
use App\Repository\SeanceRepository;
use App\Repository\RapportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parent')]
#[IsGranted('ROLE_PARENT')]
class ParentController extends AbstractController
{
    #[Route('/children', name: 'app_parent_children')]
    public function children(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $children = $user->getChildren();

        return $this->render('parent/children.html.twig', [
            'children' => $children,
        ]);
    }

    #[Route('/sessions', name: 'app_parent_sessions')]
    public function sessions(SeanceRepository $seanceRepo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $children = $user->getChildren();
        $sessions = [];

        if (!$children->isEmpty()) {
            $sessions = $seanceRepo->createQueryBuilder('s')
                ->join('s.beneficiaires', 'b')
                ->where('b IN (:children)')
                ->setParameter('children', $children)
                ->orderBy('s.date', 'DESC')
                ->addOrderBy('s.heureDebut', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('parent/sessions.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/reports', name: 'app_parent_reports')]
    public function reports(RapportRepository $rapportRepo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $children = $user->getChildren();
        $reports = [];

        if (!$children->isEmpty()) {
            $reports = $rapportRepo->createQueryBuilder('r')
                ->where('r.beneficiaire IN (:children)')
                ->setParameter('children', $children)
                ->orderBy('r.date', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('parent/reports.html.twig', [
            'reports' => $reports,
        ]);
    }
}
