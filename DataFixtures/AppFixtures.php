<?php

namespace PouyaSoft\AppzaBundle\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setPlainPassword('1234');
        $userAdmin->setEmail('admin@yahoo.com');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->setFullName('admin');
        $userAdmin->setMobile('09139709553');
        $userAdmin->setEnabled(true);
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
