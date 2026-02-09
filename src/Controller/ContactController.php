<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $fullName = $request->request->get('fullName');
            $email = $request->request->get('email');
            $subject = $request->request->get('subject');
            $messageContent = $request->request->get('message');

            if ($fullName && $email && $messageContent) {
                $contactMessage = new ContactMessage();
                $contactMessage->setFullName($fullName);
                $contactMessage->setEmail($email);
                $contactMessage->setSubject($subject);
                $contactMessage->setMessage($messageContent);

                $entityManager->persist($contactMessage);
                $entityManager->flush();

                $this->addFlash('success', 'تم إرسال رسالتك بنجاح! شكراً لتواصلك معنا.');
                return $this->redirectToRoute('app_contact');
            }

            $this->addFlash('error', 'يرجى ملء جميع الحقول المطلوبة بشكل صحيح.');
        }

        return $this->render('contact/index.html.twig');
    }
}
