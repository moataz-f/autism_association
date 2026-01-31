<?php
// src/Controller/BeneficiaireController.php
namespace App\Controller;

use App\Entity\Beneficiaire;
use App\Forms\BeneficiaireType;
use App\Repository\BeneficiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/beneficiaire')]
class BeneficiaireController extends AbstractController
{
    #[Route('/', name: 'app_beneficiaire_index', methods: ['GET'])]
    public function index(
        BeneficiaireRepository $beneficiaireRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $beneficiaireRepository->createQueryBuilder('b')
            ->leftJoin('b.famille', 'f')
            ->orderBy('b.dateInscription', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('beneficiaire/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_beneficiaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $beneficiaire = new Beneficiaire();
        $form = $this->createForm(BeneficiaireType::class, $beneficiaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($beneficiaire);
            $entityManager->flush();

            $this->addFlash('success', 'Bénéficiaire ajouté avec succès!');
            return $this->redirectToRoute('app_beneficiaire_index');
        }

        return $this->render('beneficiaire/new.html.twig', [
            'beneficiaire' => $beneficiaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_beneficiaire_show', methods: ['GET'])]
    public function show(Beneficiaire $beneficiaire): Response
    {
        return $this->render('beneficiaire/show.html.twig', [
            'beneficiaire' => $beneficiaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_beneficiaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Beneficiaire $beneficiaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BeneficiaireType::class, $beneficiaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Bénéficiaire modifié avec succès!');
            return $this->redirectToRoute('app_beneficiaire_index');
        }

        return $this->render('beneficiaire/edit.html.twig', [
            'beneficiaire' => $beneficiaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_beneficiaire_delete', methods: ['POST'])]
    public function delete(Request $request, Beneficiaire $beneficiaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$beneficiaire->getId(), $request->request->get('_token'))) {
            $beneficiaire->setActif(false);
            $entityManager->flush();
            $this->addFlash('success', 'Bénéficiaire désactivé avec succès!');
        }

        return $this->redirectToRoute('app_beneficiaire_index');
    }
}