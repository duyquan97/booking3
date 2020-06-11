<?php
namespace App\EventListener;


use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class UserAgentSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $router;

    public function __construct(LoggerInterface $logger, RouterInterface $router)
    {
        $this->logger = $logger;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $userAgent = $request->headers->get('User-Agent');
        $this->logger->info(sprintf('The User-Agent is "%s"', $userAgent));
    }

    public function onKernelResponse(ResponseEvent $event)
    {
//        $url = $this->router->generate('fos_user_profile_show');
//        $response = new RedirectResponse($url);
//        $event->setResponse($response);
    }
    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class  => 'onKernelRequest',
            ResponseEvent::class => 'onKernelResponse',
        ];
    }
}
