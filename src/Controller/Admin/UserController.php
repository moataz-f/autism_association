<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Forms\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'تم إنشاء المستخدم بنجاح.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'تم تحديث بيانات المستخدم بنجاح.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'تم حذف المستخدم بنجاح.');
        }

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/approve', name: 'admin_user_approve', methods: ['POST'])]
    public function approve(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsApproved(true);
        
        // Assign roles based on registration choice
        $role = $user->getRegistrationRole();
        if ($role === 'PARENT') {
            $user->setRoles(['ROLE_PARENT']);
        } elseif ($role === 'DONOR') {
            $user->setRoles(['ROLE_DONOR']);
        } elseif ($role === 'VOLUNTEER') {
            $user->setRoles(['ROLE_VOLUNTEER']);
        }

        $entityManager->flush();
        $this->addFlash('success', 'تم تفعيل الحساب بنجاح.');

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/reject', name: 'admin_user_reject', methods: ['POST'])]
    public function reject(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('warning', 'تم رفض وحذف الحساب.');

        return $this->redirectToRoute('admin_user_index');
    }
}
