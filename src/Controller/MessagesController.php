<?php

namespace App\Controller;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use App\Service\TokenMercure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\WebLink\Link;

class MessagesController extends AbstractController
{
//
//    public function __invoke(Request $request): Response
//    {
//        $hubUrl = $this->getParameter('mercure.default_hub');
//        $this->addLink($request, new Link('mercure', $hubUrl));
//
//        $id = $this->getUser()->getId(); // Retrieve the username of the current user
//        $token = (new Builder())
//            // set other appropriate JWT claims, such as an expiration date
//            ->withClaim('mercure', ['subscribe' => ["http://127.0.0.1:8000/user/{$id}"]]) // could also include the security roles, or anything else
//            ->getToken(new Sha256(), new Key($this->getParameter('mercure_secret_key'))); // don't forget to set this parameter! Test value: aVerySecretKey
//
//        /**/$response = $this->json("done ! ");
//        $response->headers->set(
//            'set-cookie',
//            sprintf('mercureAuthorization=%s; path=/.well-known/mercure; secure; httponly; SameSite=strict', $token)
//        );
//
//        return $response;
//    }

    /**
     * @Route("/", name="messages")
     */
    public function index(Request $request , UserRepository $users)
    {
        $hubUrl = $this->getParameter('mercure.default_hub');
        $id = $this->getUser()->getId();
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => ["http://127.0.0.1:8000/user/{$id}"]])
            ->getToken(new Sha256(), new Key($this->getParameter('mercure_secret_key')));
        $conversation = $this->getUser()->getConversations()->get(0);
        $conversation_friend = $conversation->getUsers()->get(0)!= $this->getUser() ? $conversation->getUsers()->get(0)  : $conversation->getUsers()->get(1) ;
        $response =$this->render('messages/index.html.twig', [
            'friends' => $users->findAll(),
            'conversation' => $conversation ,
            'user'=>$conversation_friend,
        ]);
        $this->addLink($request, new Link('mercure', $hubUrl));
        $response->headers->setCookie(
            new Cookie(
                'mercureAuthorization',
                $token,
                (new \DateTime())->add(new \DateInterval('PT5H')),
                "/.well-known/mercure",
                null,
                false,
                true,
                false,
                "None"
            )
        );
        return $response ;

    }
    /**
     * @Route("/messages/conversation/{id}/show", name="conversation" )
     * @Entity("Conversation",expr="repository.find(id)")
     */
    public function show(Conversation $conversation , UserRepository $users,TokenMercure $tokenMercure )
    {
        $conversation_friend = $conversation->getUsers()->get(0)!= $this->getUser() ? $conversation->getUsers()->get(0)  : $conversation->getUsers()->get(1);
        $response =  $this->render('messages/index.html.twig', [
            'friends' => $users->findAll(),
            'conversation' => $conversation,
            'user' =>$conversation_friend ,
        ]);
        return $response;
    }
    /**
     * @Route("/discover", name="descover")
     */
    public function discover(Request $request){
        $hubUrl = $this->getParameter('mercure.default_hub');
        $username = $this->getUser()->getEmail();
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => ["http://127.0.0.1:8000/user/$username"]])
            ->getToken(new Sha256(), new Key($this->getParameter('mercure_secret_key')));
        $this->addLink($request, new Link('mercure', $hubUrl));
        $response =  $this->json('done ! ') ;
        $response->headers->setCookie(
            new Cookie(
                'mercureAuthorization',
                 $token,
                (new \DateTime())->add(new \DateInterval('PT5H')),
                "/.well-known/mercure",
                null,
                false,
                true,
                false,
                "strict"
            )
        );
        return $response ;
    }
    /**
     * @Route("/messages/sendData/{conversation}", name="sendData")
     */
    public function sendData(Request $request,MessageBusInterface $bus,Conversation $conversation,SerializerInterface $serializer)
    {
        $message = new Message();
        $message->setCreatedAt(new \DateTime());
        /**
         * @var User $conversation_friend
         */
        $conversation_friend = $conversation->getUsers()->get(0)!= $this->getUser() ? $conversation->getUsers()->get(0)  : $conversation->getUsers()->get(1) ;
        $message->setFromUser($conversation_friend);
        $content = json_decode($request->getContent(),true);
        $message->setContent($content['data']);
        $conversation->addMessage($message);
        if ($content['data'] != "") {
            $this->getDoctrine()->getManager()->persist($message);
            $this->getDoctrine()->getManager()->flush();
        }
        $update = new Update("http://send.com/send",$content["data"],["http://127.0.0.1:8000/user/{$conversation_friend->getId()}"]);
        $bus->dispatch($update);
        dump($update);
        return $this->json($request->getContent(), 200 ,[] );
    }
}
