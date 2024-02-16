<?php

namespace App\EventSubscriber;

use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $request
    )
    {

    }

    public function onLogout($event): void
    {
        /** @var FlashBag */
        $flashBag = $this->request->getSession()->getBag('flashes');
        $flashBag->add('success', 'Déconnexion réussie !');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
