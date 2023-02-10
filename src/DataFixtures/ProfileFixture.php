<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile1 = new Profile();
        $profile1->setRs(rs: 'Facebook');
        $profile1->setUrl(url: 'https://www.facebook.com/');


        $profile2 = new Profile();
        $profile2->setRs(rs: 'Twitter');
        $profile2->setUrl(url: 'https://www.twitter.com/');


        $profile3 = new Profile();
        $profile3->setRs(rs: 'LinkedIn');
        $profile3->setUrl(url: 'https://www.linkedin.com/in/renaud-fontaine/');


        $profile4 = new Profile();
        $profile4->setRs(rs: 'Github');
        $profile4->setUrl(url: 'https://github.com/Nuage1985');


        $manager->persist($profile1);
        $manager->persist($profile2);
        $manager->persist($profile3);
        $manager->persist($profile4);

        $manager->flush();
    }
}
