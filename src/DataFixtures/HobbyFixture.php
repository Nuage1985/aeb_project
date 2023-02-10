<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            "Yoga",
            "Jeux vidéo",
            "Jeux de rôle",
            "Arts martiaux",
            "Cinéma",
            "Animé",
            "BD",
            "Escrime",
            "Echec",
            "Série TV",
            "Curling",
            "Lancer de bûches",
        ];

        for($i = 0 ; $i<count($data) ; $i++){
            $hobby = new Hobby();
            $hobby->setDesignation($data[$i]);
            $manager->persist($hobby);
        }
        $manager->flush();
    }
}
