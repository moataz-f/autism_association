<?php

namespace App\Controller\Admin;

use App\Entity\StaticPage;
use App\Forms\StaticPageType;
use App\Repository\StaticPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/page')]
class StaticPageController extends AbstractController
{
    #[Route('/', name: 'admin_page_index')]
    public function index(StaticPageRepository $repository): Response
    {
        return $this->render('admin/page/index.html.twig', [
            'pages' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_page_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $page = new StaticPage();
        $form = $this->createForm(StaticPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug($slugger->slug($page->getTitre())->lower());
            $em->persist($page);
            $em->flush();

            $this->addFlash('success', 'تم إضافة الصفحة بنجاح!');
            return $this->redirectToRoute('admin_page_index');
        }

        return $this->render('admin/page/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_page_edit')]
    public function edit(Request $request, StaticPage $page, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(StaticPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug($slugger->slug($page->getTitre())->lower());
            $em->flush();

            $this->addFlash('success', 'تم تحديث الصفحة بنجاح!');
            return $this->redirectToRoute('admin_page_index');
        }

        return $this->render('admin/page/edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_page_delete', methods: ['POST'])]
    public function delete(Request $request, StaticPage $page, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $em->remove($page);
            $em->flush();
            $this->addFlash('success', 'تم حذف الصفحة بنجاح!');
        }

        return $this->redirectToRoute('admin_page_index');
    }
}
