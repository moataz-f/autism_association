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
        PersonnelRepository $personnelRepo
    ): Response {
        $totalBeneficiaires = $beneficiaireRepo->count(['actif' => true]);
        $totalActivites = $activiteRepo->count(['actif' => true]);
        $totalPersonnel = $personnelRepo->count(['actif' => true]);

        $beneficiairesRecents = $beneficiaireRepo->findBy(
            ['actif' => true],
            ['dateInscription' => 'DESC'],
            5
        );

        $activitesProchaines = $activiteRepo->createQueryBuilder('a')
            ->where('a.dateDebut > :now')
            ->andWhere('a.actif = true')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.dateDebut', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $statsParNiveau = $beneficiaireRepo->createQueryBuilder('b')
            ->select('b.niveauAutisme, COUNT(b) as total')
            ->where('b.actif = true')
            ->groupBy('b.niveauAutisme')
            ->getQuery()
            ->getResult();

        // Tasks and Events for logged in user
        $user = $this->getUser();
        $myTasks = [];
        $myEvents = [];
        $personnel = null;
        $myChildren = [];

        if ($user) {
            if (method_exists($user, 'getPersonnel') && $personnel = $user->getPersonnel()) {
                $myTasks = $personnel->getTasks();
                $myEvents = $personnel->getEvents();
            }

            if ($this->isGranted('ROLE_PARENT')) {
                $myChildren = $user->getChildren();
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'totalBeneficiaires' => $totalBeneficiaires,
            'totalActivites' => $totalActivites,
            'totalPersonnel' => $totalPersonnel,
            'beneficiairesRecents' => $beneficiairesRecents,
            'activitesProchaines' => $activitesProchaines,
            'statsParNiveau' => $statsParNiveau,
            'myTasks' => $myTasks,
            'myEvents' => $myEvents,
            'personnel' => $personnel,
            'myChildren' => $myChildren
        ]);
    }
}
