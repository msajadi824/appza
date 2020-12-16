<?php

namespace PouyasoftBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

class TwoFactorSms implements AuthCodeMailerInterface
{
    /** @var  SMS */
    private $sms;

    /** @var  EntityManagerInterface */
    private $em;

    public function __construct(SMS $sms, EntityManagerInterface $em)
    {
        $this->sms = $sms;
        $this->em = $em;
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        /** @var User $user */
        $this->sms->userLogin($user, $this->em);
    }
}