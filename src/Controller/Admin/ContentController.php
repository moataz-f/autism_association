<?php
// src/Controller/Admin/ContentController.php
namespace App\Controller\Admin;

use App\Entity\PageContent;
use App\Form\PageContentType;
use App\Repository\PageContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/content')]
class ContentController extends AbstractController
{
    #[Route('/', name: 'admin_content_index')]
    public function index(PageContentRepository $repository): Response
    {
        $contents = $repository->findAll();
        
        return $this->render('admin/content/index.html.twig', [
            'contents' => $contents,
        ]);
    }

    #[Route('/new', name: 'admin_content_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $content = new PageContent();
        $form = $this->createForm(PageContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($content);
            $em->flush();

            $this->addFlash('success', 'تم إضافة المحتوى بنجاح!');
            return $this->redirectToRoute('admin_content_index');
        }

        return $this->render('admin/content/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_content_edit')]
    public function edit(Request $request, PageContent $content, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PageContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'تم تحديث المحتوى بنجاح!');
            return $this->redirectToRoute('admin_content_index');
        }

        return $this->render('admin/content/edit.html.twig', [
            'form' => $form,
            'content' => $content,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_content_delete', methods: ['POST'])]
    public function delete(Request $request, PageContent $content, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$content->getId(), $request->request->get('_token'))) {
            $em->remove($content);
            $em->flush();
            $this->addFlash('success', 'تم حذف المحتوى بنجاح!');
        }

        return $this->redirectToRoute('admin_content_index');
    }
}