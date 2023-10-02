<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\MicroPost;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $microPost1 = new MicroPost();
        $microPost1->setTitle('Welcome to Vietnam');
        $microPost1->setText('testing testing');
        $microPost1->setCreated(new DateTime());
        
        $microPost2 = new MicroPost();
        $microPost2->setTitle('Welcome to US');
        $microPost2->setText('testing testing');
        $microPost2->setCreated(new DateTime());

        $microPost3 = new MicroPost();
        $microPost3->setTitle('Welcome to America');
        $microPost3->setText('testing testing');
        $microPost3->setCreated(new DateTime());

        $manager->persist($microPost1);
        $manager->persist($microPost2);
        $manager->persist($microPost3);

        $manager->flush();
    }
}
