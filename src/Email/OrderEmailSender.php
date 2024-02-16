<?php 
namespace App\Email;

use App\Config;
use App\Entity\Order;
use App\Email\EmailSender;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class OrderEmailSender extends EmailSender
{

    public function send(Order $order)
    {
        $email = (new TemplatedEmail())
                ->from(Config::AUTO_MAIL)
                ->to($this->adminEmail)
                ->subject('Nouvelle commande : '.$order->getFullName())
                ->html($this->twig->render('emails/order_email.html.twig', [
                    'order' => $order,
                    'link' => Config::DOMAIN_URL . $this->urlGenerator->generate('admin_order_index')
                ]))
                ;
        
        $this->sendEmail($email);
    }
}

