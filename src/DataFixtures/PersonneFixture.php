<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PersonneFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = Factory::create(locale: 'fr_FR');
        // $product = new Product();
        // $manager->persist($product);
        for ($i=0; $i < 100; $i++){
            $personne = new Personne();
            $personne->setFirstname($faker->firstName);
            $personne->setName($faker->lastName);
            $personne->setAge( $faker->numberBetween(18, 65) );

            $manager->persist($personne);
        }

        $manager->flush();
    }
}
