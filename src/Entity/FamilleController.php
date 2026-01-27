<?php
namespace App\Controller;

use App\Entity\Famille;
use App\Form\FamilleType;
use App\Repository\FamilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/famille')]
class FamilleController extends AbstractController
{
    #[Route('/', name: 'app_famille_index', methods: ['GET'])]
    public function index(
        FamilleRepository $familleRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $familleRepository->createQueryBuilder('f')
            ->orderBy('f.dateInscription', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('famille/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_famille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $famille = new Famille();
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($famille);
            $entityManager->flush();

            $this->addFlash('success', 'Famille ajoutée avec succès!');
            return $this->redirectToRoute('app_famille_index');
        }

        return $this->render('famille/new.html.twig', [
            'famille' => $famille,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_famille_show', methods: ['GET'])]
    public function show(Famille $famille): Response
    {
        return $this->render('famille/show.html.twig', [
            'famille' => $famille,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_famille_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Famille $famille, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Famille modifiée avec succès!');
            return $this->redirectToRoute('app_famille_index');
        }

        return $this->render('famille/edit.html.twig', [
            'famille' => $famille,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_famille_delete', methods: ['POST'])]
    public function delete(Request $request, Famille $famille, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$famille->getId(), $request->request->get('_token'))) {
            $entityManager->remove($famille);
            $entityManager->flush();
            $this->addFlash('success', 'Famille supprimée avec succès!');
        }

        return $this->redirectToRoute('app_famille_index');
    }
}