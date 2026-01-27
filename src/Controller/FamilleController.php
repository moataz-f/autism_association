<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/famille')]
class FamilleController extends AbstractController
{
    #[Route('/', name: 'app_famille_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('famille/index.html.twig', [
            'familles' => [],
        ]);
    }
}