<?php

namespace PouyaSoft\AppzaBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Setting as SettingEntity;
use Exception;

class Setting
{
    /** @var  EntityManagerInterface */
    private $em;

    /** @var  SettingEntity */
    private $settingEntity;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return SettingEntity
     */
    public function getEntity() {
        if($this->settingEntity)
            return $this->settingEntity;

        $this->settingEntity = $this->em->getRepository('App:Setting')->findOneBy([]);
        if ($this->settingEntity)
            return $this->settingEntity;

        try {
            $this->settingEntity = new SettingEntity();
            $this->em->persist($this->settingEntity);
            $this->em->flush();
            return $this->settingEntity;
        }
        catch (Exception $e) {
            return new SettingEntity();
        }
    }
}