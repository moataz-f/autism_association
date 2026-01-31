<?php
namespace App\Controller\Admin;

use App\Entity\GalleryImage;
use App\Forms\GalleryImageType;
use App\Repository\GalleryImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/gallery')]
class GalleryController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'form.factory' => '?Symfony\Component\Form\FormFactoryInterface',
        ]);
    }

    #[Route('/', name: 'admin_gallery_index')]
    public function index(
        GalleryImageRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $repository->createQueryBuilder('g')
            ->orderBy('g.ordre', 'ASC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('admin/gallery/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'admin_gallery_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $image = new GalleryImage();
        $form = $this->createForm(GalleryImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($image);
            $em->flush();

            $this->addFlash('success', 'تم إضافة الصورة بنجاح!');
            return $this->redirectToRoute('admin_gallery_index');
        }

        return $this->render('admin/gallery/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_gallery_edit')]
    public function edit(Request $request, GalleryImage $image, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(GalleryImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'تم تحديث الصورة بنجاح!');
            return $this->redirectToRoute('admin_gallery_index');
        }

        return $this->render('admin/gallery/edit.html.twig', [
            'form' => $form,
            'image' => $image,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_gallery_delete', methods: ['POST'])]
    public function delete(Request $request, GalleryImage $image, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $em->remove($image);
            $em->flush();
            $this->addFlash('success', 'تم حذف الصورة بنجاح!');
        }

        return $this->redirectToRoute('admin_gallery_index');
    }
}