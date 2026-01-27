<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activite')]
class ActiviteController extends AbstractController
{
    #[Route('/', name: 'app_activite_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('activite/index.html.twig');
    }
}