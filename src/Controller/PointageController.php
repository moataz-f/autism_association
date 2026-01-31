<?php

namespace App\Controller;

use App\Entity\Pointage;
use App\Repository\PersonnelRepository;
use App\Repository\PointageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Security;

#[Route('/pointage')]
class PointageController extends AbstractController
{
    #[Route('/self-log/{scannedToken}', name: 'app_pointage_self_log', methods: ['POST'])]
    public function selfLog(
        string $scannedToken,
        PointageRepository $pointageRepo,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        if (!$user) {
             return new JsonResponse(['success' => false, 'message' => 'يجب تسجيل الدخول'], 401);
        }

        $personnel = $user->getPersonnel();
        if (!$personnel) {
            return new JsonResponse([
                'success' => false, 
                'message' => 'هذا الحساب غير مرتبط بملف موظف. يرجى إضافته في قائمة الموظفين أولاً باستخدام نفس البريد الإلكتروني.'
            ], 403);
        }

        // Parse Daily Token: DAILY:ACTION:DATE
        if (!str_starts_with($scannedToken, 'DAILY:')) {
            return new JsonResponse(['success' => false, 'message' => 'رمز غير صحيح'], 400);
        }

        $parts = explode(':', $scannedToken);
        if (count($parts) < 3) {
            return new JsonResponse(['success' => false, 'message' => 'رمز غير مكتمل'], 400);
        }

        $action = $parts[1]; // IN or OUT
        $dateStr = $parts[2]; // YYYY-MM-DD

        if ($dateStr !== date('Y-m-d')) {
            return new JsonResponse(['success' => false, 'message' => 'هذا الرمز انتهت صلاحيته'], 400);
        }

        $today = new \DateTime('today');
        $pointage = $pointageRepo->findOneBy([
            'personnel' => $personnel,
            'date' => $today
        ]);

        $now = new \DateTime();

        if ($action === 'IN') {
            if ($pointage) {
                 return new JsonResponse(['success' => false, 'message' => 'لقد قمت بتسجيل الدخول مسبقاً اليوم']);
            }
            $pointage = new Pointage();
            $pointage->setPersonnel($personnel);
            $pointage->setDate($today);
            $pointage->setHeureEntree($now);
            $em->persist($pointage);
            $message = 'تم تسجيل دخولك بنجاح';
        } else {
            if (!$pointage) {
                 return new JsonResponse(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً']);
            }
            if ($pointage->getHeureSortie()) {
                 return new JsonResponse(['success' => false, 'message' => 'لقد قمت بتسجيل الخروج مسبقاً اليوم']);
            }
            $pointage->setHeureSortie($now);
            $message = 'تم تسجيل خروجك بنجاح';
        }

        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => $message,
            'time' => $now->format('H:i')
        ]);
    }
    #[Route('/scan', name: 'app_pointage_scan')]
    public function scan(): Response
    {
        return $this->render('pointage/scan.html.twig');
    }

    #[Route('/log/{scannedToken}', name: 'app_pointage_log', methods: ['POST'])]
    public function log(
        string $scannedToken,
        PersonnelRepository $personnelRepo,
        PointageRepository $pointageRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        // Keep this for backward compatibility if ever needed, but redirect most logic to selfLog
        return $this->selfLog($scannedToken, $pointageRepo, $em, $this->container->get('security.helper'));
    }
}
