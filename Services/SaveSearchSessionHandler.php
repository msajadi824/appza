<?php


namespace PouyaSoft\AppzaBundle\Services;

use Doctrine\Common\Annotations\Reader;
use ReflectionObject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SaveSearchSessionHandler implements EventSubscriberInterface
{
    /** @var Reader */
    private $reader;

    /** @var RequestStack */
    private $requestStack;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(Reader $reader, RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->reader = $reader;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function onKernelController(ControllerEvent $event)
    {
        if (!is_array($controllers = $event->getController())) return;

        list($controller, $methodName) = $controllers;

        $reflectionObject = new ReflectionObject($controller);
        $reflectionMethod = $reflectionObject->getMethod($methodName);

        if (!$this->reader->getMethodAnnotation($reflectionMethod, SaveSearchSession::class)) return;

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        $allQuery = $request->query->all();

        if(count($allQuery) == 0 && count($query = $request->getSession()->get($route, [])) > 0) {
            $redirect = new RedirectResponse($this->router->generate($route, $query));
            $event->setController(function() use ($redirect) { return $redirect; });
            return;
        }

        unset($allQuery['excel']);
        $request->getSession()->set($route, $allQuery);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}