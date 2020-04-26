<?php

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $user = new User();
         $user->setFullName("iskande abbassi");
         $user->setEmail("iskander@gmail.com");
         $user->setRoles(["ROLE_USER"]);
         //password = "0000"
         $user->setPassword("\$argon2id\$v=19\$m=65536,t=4,p=1\$S0VoSkhMNHlvc21TODlPSQ\$YNVAgoA8whMIlcfKrdn7jgyHcOx9ZCCLo+WMjd+5pxs");
         $friend = new User() ;
         $friend->setFullName("abdallah lahbib");
         $friend->setEmail("abdallah@gmail.com");
         $friend->setRoles(["ROLE_USER"]);
         //password = "0000"
         $friend->setPassword("\$argon2id\$v=19\$m=65536,t=4,p=1\$S0VoSkhMNHlvc21TODlPSQ\$YNVAgoA8whMIlcfKrdn7jgyHcOx9ZCCLo+WMjd+5pxs");
         $conversation= new Conversation();
         $user->addConversation($conversation);
         $conversation->addUser($friend);
         for($i = 0 ; $i< 10 ; $i++ ){
             $name = ($i%2==0) ? "iskander" : "abdallah";
             $sender  = ($i%2==0) ? $user : $friend;
             $message = new Message();
             $message->setContent("hello my friend $name");
             $message->setFromUser($sender);
             $message->setCreatedAt(new \DateTime());
             $manager->persist($message);
             $conversation->addMessage($message);
         }
         $manager->persist($conversation);
         $manager->persist($friend);
         $manager->persist($user);
         $manager->flush();
    }
}
