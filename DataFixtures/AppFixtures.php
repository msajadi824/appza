<?php

namespace PouyaSoft\AppzaBundle\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin@yahoo.com');
        $userAdmin->setPlainPassword('1234');
        $userAdmin->setEmail('admin@yahoo.com');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->setFullName('admin');
//        $userAdmin->setCodeMelli('0123456789'); todo
        $userAdmin->setMobile('09139709553');
        $userAdmin->setEnabled(true);
//        $userAdmin->setStatus(User::STATUS_VERIFIED); todo
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
