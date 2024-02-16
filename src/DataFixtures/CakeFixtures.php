<?php

namespace App\DataFixtures;

use App\Entity\Cake;
use App\DataFixturesHelper\DateCreator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class CakeFixtures extends Fixture
{
    public function __construct(
        private DateCreator $dateCreator
    )
    {

    }

    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 10; $i++) 
        { 
            $cake = new Cake;

            if(random_int(0, 9) > 3)
            {
                $cake->setTitle('gateau '.$i);
            }
            if(random_int(0, 9) > 3)
            {
                $cake->setDescription('description du gateau '.$i);
            }

            $cake
                ->setCreatedAt($this->dateCreator->create())
                ->setSpotlighted(true)
                ->setCountVisit(random_int(0, 100))
                ->setLastVisit($this->dateCreator->create())
                ;
            $manager->persist($cake);
        }

        for ($i=10; $i < 100; $i++) 
        { 
            $cake = new Cake;

            if(random_int(0, 9) > 3)
            {
                $cake->setTitle('gateau '.$i);
            }
            if(random_int(0, 9) > 3)
            {
                $cake->setDescription('description du gateau '.$i);
            }

            $cake
                ->setCreatedAt($this->dateCreator->create())
                ->setCountVisit(random_int(0, 100))
                ->setLastVisit($this->dateCreator->create())
                ;
            $manager->persist($cake);
        }
       
        $manager->flush();
    }
}
