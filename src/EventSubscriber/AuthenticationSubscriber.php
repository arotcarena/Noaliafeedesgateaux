<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $request,
        private Security $security
    )
    {

    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        if($this->security->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            /** @var FlashBag */
            $flashBag = $this->request->getSession()->getBag('flashes');
            $flashBag->add('success', 'Connexion réussie !');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
}
