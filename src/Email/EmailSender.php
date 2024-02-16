<?php 
namespace App\Email;

use App\Config;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailSender
{
    protected Mailer $mailer;

    protected string $adminEmail;

    protected Environment $twig;

    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(UserRepository $userRepository, Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $transport = Transport::fromDsn(Config::MAIL_DSN);
        $this->mailer = new Mailer($transport);

        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->adminEmail = $userRepository->findAdministrator()->getEmail();
    }

    public function sendEmail(TemplatedEmail $email)
    {
        // $this->mailer->send($email);
    }
}

