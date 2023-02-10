<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            "Data Scientist",
            "Docteur ORL",
            "Statisticien",
            "Professeur des écoles",
            "Peintre",
            "Cascadeur paysagiste",
            "Electricien",
            "Ninja",
            "Humoriste funéraire",
            "Hygiéniste dentaire",
            "Contrôleur de contrôleur de gestion",
            "Dungeon Master",
        ];

        for($i = 0 ; $i<count($data) ; $i++){
            $job = new Job();
            $job->setDesignation($data[$i]);
            $manager->persist($job);
        }
        $manager->flush();
    }
}
