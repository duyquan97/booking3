<?php
namespace App\EventListener;


use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
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
//        $request = $event->getRequest();
//        $userAgent = $request->headers->get('User-Agent');
        $this->logger->info('This is Event Request');
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $this->logger->info('This is Event Response');
    }

    public function onKernelController(ControllerEvent $event)
    {

        $this->logger->info('This is Event Controller');
    }

    public function onKernelTerminate(TerminateEvent $event)
    {

        $this->logger->info('This is Event Terminate');
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $this->logger->info('This is Event Exception');
    }

    public function onKernelView(ViewEvent $event)
    {
        $this->logger->info('This is Event View');
    }

    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        $this->logger->info('This is Event Finish Request');
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event)
    {
        $this->logger->info('This is Event Controller Arguments');
    }
    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class  => 'onKernelRequest',
            ResponseEvent::class => 'onKernelResponse',
            ControllerEvent::class => 'onKernelController',
            TerminateEvent::class => 'onKernelTerminate',
            ExceptionEvent::class => 'onKernelException',
            ViewEvent::class => 'onKernelView',
            FinishRequestEvent::class => 'onKernelFinishRequest',
            ControllerArgumentsEvent::class => 'onKernelControllerArguments'
        ];
    }
}
