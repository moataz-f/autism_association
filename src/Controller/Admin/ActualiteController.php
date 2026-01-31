<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Forms\ActualiteType;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/actualite')]
class ActualiteController extends AbstractController
{
    #[Route('/', name: 'admin_actualite_index')]
    public function index(ActualiteRepository $repository): Response
    {
        return $this->render('admin/actualite/index.html.twig', [
            'actualites' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'admin_actualite_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $actualite = new Actualite();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actualite->setSlug($slugger->slug($actualite->getTitre())->lower());
            $em->persist($actualite);
            $em->flush();

            $this->addFlash('success', 'تم إضافة الخبر بنجاح!');
            return $this->redirectToRoute('admin_actualite_index');
        }

        return $this->render('admin/actualite/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_actualite_edit')]
    public function edit(Request $request, Actualite $actualite, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actualite->setSlug($slugger->slug($actualite->getTitre())->lower());
            $em->flush();

            $this->addFlash('success', 'تم تحديث الخبر بنجاح!');
            return $this->redirectToRoute('admin_actualite_index');
        }

        return $this->render('admin/actualite/edit.html.twig', [
            'form' => $form->createView(),
            'actualite' => $actualite,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_actualite_delete', methods: ['POST'])]
    public function delete(Request $request, Actualite $actualite, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actualite->getId(), $request->request->get('_token'))) {
            $em->remove($actualite);
            $em->flush();
            $this->addFlash('success', 'تم حذف الخبر بنجاح!');
        }

        return $this->redirectToRoute('admin_actualite_index');
    }
}
