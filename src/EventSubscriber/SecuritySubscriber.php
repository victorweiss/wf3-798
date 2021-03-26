<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class SecuritySubscriber implements EventSubscriberInterface
{
    private $security;
    private $router;

    public function __construct(
        Security $security,
        RouterInterface $router
    ) {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        if ($user->isBannedNow()) {
            $route = $this->router->generate('app_logout');
            $response = new RedirectResponse($route);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
