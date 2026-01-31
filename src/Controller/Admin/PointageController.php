<?php

namespace App\Controller\Admin;

use App\Repository\PointageRepository;
use App\Repository\PersonnelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/pointage')]
#[IsGranted('ROLE_ADMIN')]
class PointageController extends AbstractController
{
    #[Route('/', name: 'admin_pointage_index')]
    public function index(PointageRepository $pointageRepo, Request $request): Response
    {
        $date = $request->query->get('date') ? new \DateTime($request->query->get('date')) : new \DateTime('today');
        
        $pointages = $pointageRepo->findBy(['date' => $date], ['heureEntree' => 'DESC']);

        return $this->render('admin/pointage/index.html.twig', [
            'pointages' => $pointages,
            'currentDate' => $date
        ]);
    }

    #[Route('/print-daily', name: 'admin_pointage_print_daily')]
    public function printDaily(): Response
    {
        return $this->render('admin/pointage/print_daily.html.twig');
    }

    #[Route('/reports', name: 'admin_pointage_reports')]
    public function reports(PointageRepository $pointageRepo, PersonnelRepository $personnelRepo, Request $request): Response
    {
        $month = $request->query->get('month', date('m'));
        $year = $request->query->get('year', date('Y'));
        
        $personnels = $personnelRepo->findBy(['actif' => true]);
        $reportData = [];

        foreach ($personnels as $p) {
            $qb = $pointageRepo->createQueryBuilder('p')
                ->where('p.personnel = :p')
                ->andWhere('p.date LIKE :date')
                ->setParameter('p', $p)
                ->setParameter('date', "$year-$month-%");
            
            $pointages = $qb->getQuery()->getResult();
            $totalMinutes = 0;
            $daysPresent = count($pointages);

            foreach ($pointages as $pt) {
                if ($pt->getHeureSortie()) {
                    $diff = $pt->getHeureEntree()->diff($pt->getHeureSortie());
                    $totalMinutes += ($diff->h * 60) + $diff->i;
                }
            }

            $reportData[] = [
                'personnel' => $p,
                'daysPresent' => $daysPresent,
                'totalHours' => round($totalMinutes / 60, 1)
            ];
        }

        return $this->render('admin/pointage/reports.html.twig', [
            'reportData' => $reportData,
            'currentMonth' => $month,
            'currentYear' => $year
        ]);
    }
}
