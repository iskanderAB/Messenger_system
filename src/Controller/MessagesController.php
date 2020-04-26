<?php

namespace App\Controller;
use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MessagesController extends AbstractController
{
    /**
     * @Route("/messages", name="messages")
     */
    public function index(UserRepository $users)
    {
        return $this->render('messages/index.html.twig', [
            'friends' => $users->findAll(),
        ]);
    }
    /**
     * @Route("/messages/conversation/{id}/show", name="conversation" )
     * @Entity("Conversation",expr="repository.find(id)")
     */
    public function show(Conversation $conversation , UserRepository $users )
    {
        return $this->render('messages/index.html.twig', [
            'friends' => $users->findAll(),
            'conversation' => $conversation
        ]);
    }

    /**
     * @Route("/messages/sendData", name="sendData")
     */
    public function sendData(Request $request,MessageBusInterface $bus)
    {
        $update = new Update();
        dump($request);
        return $this->json($request->getContent(), 200 ,[] );
    }
}
