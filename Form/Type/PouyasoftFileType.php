<?php
namespace PouyaSoft\AppzaBundle\Form\Type;

use Exception;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;
use function is_callable;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;
use Vich\UploaderBundle\Util\ClassUtils;

class PouyasoftFileType extends VichImageType
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(RouterInterface $router, StorageInterface $storage, UploadHandler $handler, PropertyMappingFactory $factory, PropertyAccessorInterface $propertyAccessor = null)
    {
        parent::__construct($storage, $handler, $factory, $propertyAccessor);

        $this->router = $router;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'download_label' => 'دانلود',
            'download_uri' => true,
            'view_label' => 'مشاهده',
            'image_uri' => true,
            'delete_label' => 'حذف',
            'allow_delete' => true,
            'croppieOptions' => false,
        ]);

        $resolver->setAllowedTypes('view_label', ['bool', 'string', 'callable', PropertyPath::class]);
        $resolver->setAllowedTypes('croppieOptions', ['bool', 'array', 'null']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        if($options['croppieOptions']) {
            $builder
                ->add('picFileCropped', HiddenType::class, ['mapped' => false])
                ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                    $form = $event->getForm();

                    if($form->get('picFileCropped')->getData()) {
                        try {
                            $temp = tempnam(sys_get_temp_dir(), 'TMP_');
                            file_put_contents($temp, base64_decode(explode(',', explode(';', $form->get('picFileCropped')->getData())[1])[1]));

                            $event->setData(['file' => new UploadedFile($temp, '12345.png', null, null, true)]);
                        }
                        catch (Exception $e) {}
                    }
                })
            ;
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $object = $form->getParent()->getData();
        $view->vars['object'] = $object;
        $view->vars['image_uri'] = null;
        $view->vars['download_uri'] = null;

        if (null !== $object) {
            $view->vars = array_replace(
                $view->vars,
                $this->resolveDownloadLabel($options['download_label'], $object, $form),
                $this->resolveViewLabel($options['view_label'], $object, $form)
            );

            $view->vars['image_uri'] = $this->resolveUriOption($options['image_uri'], $object, $form);
            $view->vars['download_uri'] = $this->resolveUriOption($options['download_uri'], $object, $form);

            if($object->getId()) $options['required'] = false;
        }

        $view->vars['required'] = $options['required'];
        $view->vars['croppieOptions'] = $options['croppieOptions'];
    }

    protected function resolveUriOption($uriOption, $object, FormInterface $form)
    {
        if(true === $uriOption) {
            $propertyMapping = $this->factory->fromField($object, $form->getName());

            if(!$propertyMapping->getUriPrefix()) {
                if(empty($propertyMapping->getFileName($object))) return null;

                $classPath = explode('\\',ClassUtils::getClass($object));
                return $this->router->generate('download', ['entityName' => end($classPath), 'id' => $object->getId(), 'field' => $form->getName()]);
            }
        }

        return parent::resolveUriOption($uriOption, $object, $form);
    }

    protected function resolveViewLabel($viewLabel, $object, FormInterface $form): array
    {
        if (true === $viewLabel) {
            $mapping = $this->factory->fromField($object, $form->getName());

            return ['view_label' => $mapping->readProperty($object, 'originalName'), 'translation_domain' => false];
        }

        if (is_callable($viewLabel)) {
            $result = $viewLabel($object);

            return [
                'view_label' => $result['view_label'] ?? $result,
                'translation_domain' => $result['translation_domain'] ?? false,
            ];
        }

        if ($viewLabel instanceof PropertyPath) {
            return [
                'download_label' => $this->propertyAccessor->getValue($object, $viewLabel),
                'translation_domain' => false,
            ];
        }

        return ['view_label' => $viewLabel];
    }

    public function getBlockPrefix() : string
    {
        return 'pouyasoft_file';
    }
}