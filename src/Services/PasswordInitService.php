<?php
namespace App\Services;

use DateTime;
use Exception;
use DateInterval;
use DateTimeZone;
use App\Entity\User;
use DateTimeImmutable;
use App\Services\TokenGenerator;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Email\Security\PasswordInitEmail;
use Symfony\Component\HttpFoundation\Request;

class PasswordInitService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordInitEmail $passwordInitEmail,
        private TokenGenerator $tokenGenerator,
        private EntityManagerInterface $em
    )
    {
        
    }
    public function requireInit(string $email)
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if(!$user) 
        {
            throw new Exception('Vous demandez la réinitialisation du mot de passe avec un email ne correspondant à aucun utilisateur connu');
        }
        $user->setToken($this->tokenGenerator->generate(60));
        $user->setTokenExpireAt(
            (new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))->add(
                new DateInterval('P1D')
            )
            );
        $this->em->flush();
        $this->passwordInitEmail->send($user);
    }
    public function verifyToken(Request $request): ?User
    {
        if($request->get('id') && $request->get('token'))
        {
            $user = $this->userRepository->find($request->get('id'));
            if($user && $user->getToken() === $request->get('token'))
            {
                $user->setToken(null);
                $this->em->flush();
                if($user->getTokenExpireAt()->format('Y:m:d H:i:s') > (new DateTime('now', new DateTimeZone('Europe/Paris')))->format('Y:m:d H:i:s'))
                {
                    return $user;
                }
            }
            elseif($user && $request->getMethod() === 'POST')
            {
                return $user;
            }
        }
        return null;
    }
}