<?php 
namespace App\Email\Security;

use App\Config;
use App\Entity\User;
use App\Email\EmailSender;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class PasswordInitEmail extends EmailSender
{
    public function send(User $user)
    {
        $email = (new TemplatedEmail())
                ->from(Config::AUTO_MAIL)
                ->to($user->getEmail())
                ->subject('Réinitialisation du mot de passe')
                ->html($this->twig->render('emails/security/password_init.html.twig', [
                    'user' => $user,
                    'link' => Config::DOMAIN_URL . $this->urlGenerator->generate('security_passwordInit', [
                        'id' => $user->getId(),
                        'token' => $user->getToken()
                    ])
                ]))
                ;
        
        $this->sendEmail($email);
    }
}

