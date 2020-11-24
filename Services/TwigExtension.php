<?php

namespace PouyaSoft\AppzaBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private $router;
    private $requestStack;
    private $pouyasoftFile;

    public function __construct(RouterInterface $router, RequestStack $requestStack, PouyasoftFile $pouyasoftFile)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->pouyasoftFile = $pouyasoftFile;
    }

    public function getFilters()
    {
        return [];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('pouyasoft_file', [$this->pouyasoftFile, 'webPath']),
        ];
    }
}