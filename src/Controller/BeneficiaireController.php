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
use App\Entity\BeneficiaireDocument;
use App\Entity\Rapport;
use App\Forms\DocumentType;
use App\Forms\RapportType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

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
            ->leftJoin('b.parents', 'p')
            ->addSelect('p')
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

    #[Route('/{id}', name: 'app_beneficiaire_show', methods: ['GET', 'POST'])]
    public function show(
        Beneficiaire $beneficiaire, 
        Request $request, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        // Form 1: Document Upload
        $document = new BeneficiaireDocument();
        $docForm = $this->createForm(DocumentType::class, $document);
        
        // Form 2: Rapport Creation
        $rapport = new Rapport();
        $rapport->setBeneficiaire($beneficiaire);
        $rapport->setCreatedBy($this->getUser());
        $rapportForm = $this->createForm(RapportType::class, $rapport);

        $docForm->handleRequest($request);
        $rapportForm->handleRequest($request);

        // Handle Document Upload
        if ($docForm->isSubmitted() && $docForm->isValid()) {
            $file = $docForm->get('file')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/documents/beneficiaires';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $file->move($uploadDir, $newFilename);
                    
                    $document->setFilename($newFilename);
                    $document->setOriginalName($file->getClientOriginalName());
                    $document->setBeneficiaire($beneficiaire);
                    
                    $entityManager->persist($document);
                    $entityManager->flush();

                    $this->addFlash('success', 'تم تحميل الملف بنجاح: ' . $document->getOriginalName());
                } catch (\Exception $e) {
                    $this->addFlash('error', 'خطأ أثناء الحفظ: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'يرجى اختيار ملف للرفع.');
            }
            return $this->redirectToRoute('app_beneficiaire_show', ['id' => $beneficiaire->getId()]);
        }

        // Handle Rapport Creation
        if ($rapportForm->isSubmitted() && $rapportForm->isValid()) {
            $entityManager->persist($rapport);
            $entityManager->flush();

            $this->addFlash('success', 'تم إضافة التقرير بنجاح.');
            return $this->redirectToRoute('app_beneficiaire_show', ['id' => $beneficiaire->getId()]);
        }

        return $this->render('beneficiaire/show.html.twig', [
            'beneficiaire' => $beneficiaire,
            'docForm' => $docForm->createView(),
            'rapportForm' => $rapportForm->createView(),
            'documents' => $beneficiaire->getDocuments(),
            'rapports' => $beneficiaire->getRapports()
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