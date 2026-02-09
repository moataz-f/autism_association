<?php

namespace App\Controller\Admin;

use App\Repository\ContactMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/contact-messages')]
#[IsGranted('ROLE_ADMIN')]
class ContactMessageController extends AbstractController
{
    #[Route('/', name: 'admin_contact_message_index')]
    public function index(ContactMessageRepository $repository): Response
    {
        return $this->render('admin/contact_message/index.html.twig', [
            'messages' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}', name: 'admin_contact_message_show')]
    public function show(int $id, ContactMessageRepository $repository, EntityManagerInterface $em): Response
    {
        $message = $repository->find($id);
        if (!$message) {
            throw $this->createNotFoundException('Message not found.');
        }

        if (!$message->isRead()) {
            $message->setIsRead(true);
            $em->flush();
        }

        return $this->render('admin/contact_message/show.html.twig', [
            'message' => $message,
        ]);
    }
}
