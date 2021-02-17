<?php

namespace PouyaSoft\AppzaBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private $router;
    private $requestStack;
    private $pouyasoftFile;
    private $setting;

    public function __construct(RouterInterface $router, RequestStack $requestStack, PouyasoftFile $pouyasoftFile, Setting $setting)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->pouyasoftFile = $pouyasoftFile;
        $this->setting = $setting;
    }

    public function getFilters()
    {
        return [];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('pouyasoft_file', [$this->pouyasoftFile, 'webPath']),
            new TwigFunction('getSetting', [$this, 'getSetting']),
            new TwigFunction('backUrl', [$this, 'backUrl']),
        ];
    }

    public function getSetting()
    {
        return $this->setting;
    }

    public function backUrl($default = null)
    {
        return $this->requestStack->getCurrentRequest()->headers->get('referer', $default);
    }
}