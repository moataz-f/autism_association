<?php
// src/Controller/PersonnelController.php
namespace App\Controller;

use App\Entity\Personnel;
use App\Form\PersonnelType;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/personnel')]
class PersonnelController extends AbstractController
{
    #[Route('/', name: 'app_personnel_index', methods: ['GET'])]
    public function index(
        PersonnelRepository $personnelRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $personnelRepository->createQueryBuilder('p')
            ->orderBy('p.dateEmbauche', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('personnel/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_personnel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($personnel);
            $entityManager->flush();

            $this->addFlash('success', 'Personnel ajouté avec succès!');
            return $this->redirectToRoute('app_personnel_index');
        }

        return $this->render('personnel/new.html.twig', [
            'personnel' => $personnel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_personnel_show', methods: ['GET'])]
    public function show(Personnel $personnel): Response
    {
        return $this->render('personnel/show.html.twig', [
            'personnel' => $personnel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_personnel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Personnel $personnel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Personnel modifié avec succès!');
            return $this->redirectToRoute('app_personnel_index');
        }

        return $this->render('personnel/edit.html.twig', [
            'personnel' => $personnel,
            'form' => $form,
        ]);
    }
}