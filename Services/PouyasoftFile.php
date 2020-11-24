<?php

namespace PouyaSoft\AppzaBundle\Services;

use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Vich\UploaderBundle\Util\ClassUtils;

class PouyasoftFile
{
    /** @var RouterInterface */
    private $router;

    /** @var UploaderHelper */
    private $helper;

    /** @var PropertyMappingFactory */
    protected $factory;

    public function __construct(RouterInterface $router, UploaderHelper $helper, PropertyMappingFactory $factory)
    {
        $this->router = $router;
        $this->helper = $helper;
        $this->factory = $factory;
    }

    public function webPath($obj, string $fieldName, string $className = null)
    {
        $propertyMapping = $this->factory->fromField($obj, $fieldName, $className);

        if(!$propertyMapping->getUriPrefix()) {
            if(empty($propertyMapping->getFileName($obj))) return null;

            $classPath = explode('\\',ClassUtils::getClass($obj));
            return $this->router->generate('download', ['entityName' => end($classPath), 'id' => $obj->getId(), 'field' => $fieldName]);
        }

        return $this->helper->asset($obj, $fieldName, $className);
    }

    public function absolutePath($obj, string $fieldName, string $className = null)
    {
        $propertyMapping = $this->factory->fromField($obj, $fieldName, $className);

        return str_replace('\\', '/', $propertyMapping->getUploadDestination()) . '/'. $propertyMapping->getFileName($obj);
    }
}