<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Repository\BeneficiaireRepository;
use App\Repository\ActiviteRepository;
use App\Repository\FamilleRepository;
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
        FamilleRepository $familleRepo,
        PersonnelRepository $personnelRepo
    ): Response {
        $totalBeneficiaires = $beneficiaireRepo->count(['actif' => true]);
        $totalFamilles = $familleRepo->count([]);
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

        return $this->render('dashboard/index.html.twig', [
            'totalBeneficiaires' => $totalBeneficiaires,
            'totalFamilles' => $totalFamilles,
            'totalActivites' => $totalActivites,
            'totalPersonnel' => $totalPersonnel,
            'beneficiairesRecents' => $beneficiairesRecents,
            'activitesProchaines' => $activitesProchaines,
            'statsParNiveau' => $statsParNiveau,
        ]);
    }
}
