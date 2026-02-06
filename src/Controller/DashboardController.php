<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Repository\BeneficiaireRepository;
use App\Repository\ActiviteRepository;
use App\Repository\PersonnelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]

    public function index(
        BeneficiaireRepository $beneficiaireRepo,
        ActiviteRepository $activiteRepo,
        PersonnelRepository $personnelRepo,
        \App\Repository\SeanceRepository $seanceRepo,
        \App\Repository\UserRepository $userRepo
    ): Response {
        $user = $this->getUser();
        $data = [
            'totalBeneficiaires' => $beneficiaireRepo->count(['actif' => true]),
            'totalActivites' => $activiteRepo->count(['actif' => true]),
            'totalPersonnel' => $personnelRepo->count(['actif' => true]),
            'pendingUsers' => $userRepo->count(['isApproved' => false]),
            'myTasks' => [],
            'myEvents' => [],
            'personnel' => null,
            'myChildren' => [],
            'upcomingSessions' => [],
            'childSessions' => []
        ];

        if ($user) {
            // Personnel / Specialist Data
            if (method_exists($user, 'getPersonnel') && $personnel = $user->getPersonnel()) {
                $data['personnel'] = $personnel;
                $data['myTasks'] = $personnel->getTasks();
                $data['myEvents'] = $personnel->getEvents();
            }

            // Specialist Specific: Upcoming Sessions
            if ($this->isGranted('ROLE_SPECIALISTE') || $this->isGranted('ROLE_SPECIALIST')) {
                $data['upcomingSessions'] = $seanceRepo->createQueryBuilder('s')
                    ->where('s.specialiste = :user')
                    ->andWhere('s.date >= :today')
                    ->setParameter('user', $user)
                    ->setParameter('today', new \DateTime('today'))
                    ->orderBy('s.date', 'ASC')
                    ->addOrderBy('s.heureDebut', 'ASC')
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult();
            }

            // Parent Specific: Children and their Sessions
            if ($this->isGranted('ROLE_PARENT')) {
                $data['myChildren'] = $user->getChildren();
                if (count($data['myChildren']) > 0) {
                    $data['childSessions'] = $seanceRepo->createQueryBuilder('s')
                        ->innerJoin('s.beneficiaires', 'b')
                        ->where('b IN (:children)')
                        ->andWhere('s.date >= :today')
                        ->setParameter('children', $data['myChildren'])
                        ->setParameter('today', new \DateTime('today'))
                        ->orderBy('s.date', 'ASC')
                        ->addOrderBy('s.heureDebut', 'ASC')
                        ->setMaxResults(10)
                        ->getQuery()
                        ->getResult();
                }
            }
        }

        return $this->render('dashboard/index.html.twig', $data);
    }
}
