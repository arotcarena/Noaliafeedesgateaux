<?php

namespace App\DataFixtures;

use App\DataFixturesHelper\DateCreator;
use App\Entity\Visit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class VisitFixtures extends Fixture
{
    public function __construct(
        private DateCreator $dateCreator
    )
    {

    }

    public function load(ObjectManager $manager): void
    {
        // for ($i=0; $i < 1000; $i++) 
        // { 
        //     $visit = new Visit;
        //     if(random_int(0, 9) > 3)
        //     {
        //         $visit->setVisitedPageId('home');
        //     }
        //     else
        //     {
        //         $visit->setVisitedPageId('cakes_index');
        //     }
        //     $visit->setVisitedAt($this->dateCreator->create());
        //     $manager->persist($visit);
        // }
       
        // $manager->flush();
    }
}
