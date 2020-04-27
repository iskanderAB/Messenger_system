<?php


namespace App\Service;


use App\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class TokenMercure
{
    private $secretKey ;
    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey ;
    }
    public function generate(User  $user){
        $username = $user->getUsername();
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => ["http://127.0.0.1:8000/user/$username"]])
            ->sign(new Sha256() ,$this->secretKey )
            ->getToken();
         return "mercureAuthorization={$token}; path=/.well-known/mercure; httponly; domainSameSite=strict";
    }
}