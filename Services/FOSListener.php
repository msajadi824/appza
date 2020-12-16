<?php
namespace PouyaSoft\AppzaBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FOSListener implements EventSubscriberInterface
{
    use TargetPathTrait;

    /** @var  UrlGeneratorInterface */
    private $router;

    /** @var  SMS */
    private $sms;

    /** @var  EntityManagerInterface */
    private $em;

    /** @var  SessionInterface */
    private $session;

    public function __construct(UrlGeneratorInterface $router, SMS $sms, EntityManagerInterface $em, SessionInterface $session)
    {
        $this->router = $router;
        $this->sms = $sms;
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_SUCCESS =>            'onProfileEditSuccess',
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS =>         'onProfileEditSuccess',
            FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM =>    'onResettingSendEmailConfirm',
            FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE => 'onResettingDisabledUser',
            FOSUserEvents::RESETTING_RESET_INITIALIZE =>      'onResettingDisabledUser',
        );
    }

    public function onProfileEditSuccess(FormEvent $event)
    {
        if($targetPath = $this->getTargetPath($this->session, 'main'))
            $this->removeTargetPath($this->session, 'main');

        $event->setResponse(new RedirectResponse($targetPath ?: $this->router->generate('fos_user_profile_edit')));
    }

    public function onResettingSendEmailConfirm(GetResponseUserEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
//        $this->sms->userResettingPassword($user, $url, $this->em); todo this line can override by project

        $event->setResponse(new RedirectResponse($this->router->generate('fos_user_resetting_check_email', ['username' => $user->getUsername()])));
    }

    public function onResettingDisabledUser(GetResponseUserEvent $event)
    {
        $user = $event->getUser();

        if($user && !$user->isEnabled())
            $event->setResponse(new RedirectResponse($this->router->generate('fos_user_resetting_check_email', array('username' => $user->getUsername(), 't' => 0))));
    }
}