<?php
// src/Controller/PersonnelController.php
namespace App\Controller;

use App\Entity\Personnel;
use App\Entity\User;
use App\Forms\PersonnelType;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create associated User
            $user = new User();
            $user->setEmail($personnel->getEmail());
            $user->setNom($personnel->getNom());
            $user->setPrenom($personnel->getPrenom());
            $user->setNumtlf($personnel->getTelephone());
            
            // Set default password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    'pass123'
                )
            );

            // Determine role
            $roles = ['ROLE_PERSONNEL'];
            if ($personnel->getRole() === 'Administrateur') {
                $roles[] = 'ROLE_ADMIN';
            } elseif ($personnel->getRole() === 'Éducateur') {
                $roles[] = 'ROLE_EDUCATEUR';
            }
            $user->setRoles($roles);

            // Link them
            $personnel->setUser($user);

            $entityManager->persist($user);
            $entityManager->persist($personnel);
            $entityManager->flush();

            $this->addFlash('success', 'Personnel ajouté avec succès! Un compte utilisateur a été créé (Mdp: pass123).');
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
            // Sync User if exists
            if ($personnel->getUser()) {
                $user = $personnel->getUser();
                $user->setEmail($personnel->getEmail());
                $user->setNom($personnel->getNom());
                $user->setPrenom($personnel->getPrenom());
                $user->setNumtlf($personnel->getTelephone());
                
                // Update roles based on new role
                $roles = $user->getRoles();
                // Reset basic roles
                $roles = array_diff($roles, ['ROLE_ADMIN', 'ROLE_EDUCATEUR']);
                $roles[] = 'ROLE_PERSONNEL'; // Ensure base role
                
                if ($personnel->getRole() === 'Administrateur') {
                    $roles[] = 'ROLE_ADMIN';
                } elseif ($personnel->getRole() === 'Éducateur') {
                    $roles[] = 'ROLE_EDUCATEUR';
                }
                
                $user->setRoles(array_unique($roles));
            }

            $entityManager->flush();

            $this->addFlash('success', 'Personnel et compte utilisateur modifiés avec succès!');
            return $this->redirectToRoute('app_personnel_index');
        }

        return $this->render('personnel/edit.html.twig', [
            'personnel' => $personnel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_personnel_delete', methods: ['POST'])]
    public function delete(Request $request, Personnel $personnel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personnel->getId(), $request->request->get('_token'))) {
            // User will be auto-deleted due to cascade remove if configured, 
            // but we configured it on the Personnel side (cascade=['persist', 'remove']).
            // So removing personnel should remove user.
            $entityManager->remove($personnel);
            $entityManager->flush();
            $this->addFlash('success', 'Personnel supprimé avec succès!');
        }

        return $this->redirectToRoute('app_personnel_index');
    }
}