<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messaging')]
#[IsGranted('ROLE_USER')]
class MessagingController extends AbstractController
{
    #[Route('/', name: 'app_messaging_index')]
    public function index(Request $request, MessageRepository $messageRepo, UserRepository $userRepo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $search = $request->query->get('search');
        
        // Simple inbox: list users who contacted me or whom I contacted
        $sentMessages = $messageRepo->findBy(['sender' => $user]);
        $receivedMessages = $messageRepo->findBy(['receiver' => $user]);
        
        $contactedUsers = [];
        foreach ($sentMessages as $msg) $contactedUsers[$msg->getReceiver()->getId()] = $msg->getReceiver();
        foreach ($receivedMessages as $msg) $contactedUsers[$msg->getSender()->getId()] = $msg->getSender();

        $searchResults = [];
        if ($search) {
            $searchResults = $userRepo->createQueryBuilder('u')
                ->where('u.nom LIKE :query OR u.prenom LIKE :query OR u.email LIKE :query')
                ->andWhere('u.id != :myId')
                ->setParameter('query', '%'.$search.'%')
                ->setParameter('myId', $user->getId())
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        }

        // If user is Personnel or Admin, they should see parents easily
        $parents = [];
        if ($this->isGranted('ROLE_PERSONNEL') || $this->isGranted('ROLE_ADMIN')) {
            $parents = $userRepo->createQueryBuilder('u')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%ROLE_PARENT%')
                ->orderBy('u.nom', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        }
        
        // Also allow contacting admins
        $admins = $userRepo->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->andWhere('u.id != :myId')
            ->setParameter('myId', $user->getId())
            ->getQuery()
            ->getResult();

        return $this->render('messaging/index.html.twig', [
            'contacts' => array_values($contactedUsers),
            'admins' => $admins,
            'parents' => $parents,
            'searchResults' => $searchResults,
            'searchQuery' => $search
        ]);
    }

    #[Route('/t/{id}', name: 'app_messaging_thread')]
    public function thread(User $otherUser, MessageRepository $messageRepo, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $messages = $messageRepo->createQueryBuilder('m')
            ->where('(m.sender = :user AND m.receiver = :other)')
            ->orWhere('(m.sender = :other AND m.receiver = :user)')
            ->setParameter('user', $user)
            ->setParameter('other', $otherUser)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        // Mark unread as read
        foreach ($messages as $msg) {
            if ($msg->getReceiver() === $user && !$msg->isRead()) {
                $msg->setIsRead(true);
            }
        }
        $em->flush();

        return $this->render('messaging/thread.html.twig', [
            'otherUser' => $otherUser,
            'messages' => $messages
        ]);
    }

    #[Route('/send/{id}', name: 'app_messaging_send', methods: ['POST'])]
    public function send(Request $request, User $receiver, EntityManagerInterface $em): Response
    {
        $content = $request->request->get('content');
        if (!$content) {
            $this->addFlash('error', 'Le message ne peut pas être vide.');
            return $this->redirectToRoute('app_messaging_thread', ['id' => $receiver->getId()]);
        }

        $message = new Message();
        $message->setSender($this->getUser());
        $message->setReceiver($receiver);
        $message->setContent($content);

        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('app_messaging_thread', ['id' => $receiver->getId()]);
    }
}
