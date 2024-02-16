<?php
namespace App\DataFixturesHelper;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Faker\Factory;
use Faker\Generator;

class DateCreator
{  

    public function create():DateTimeImmutable
    {
        return (new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))
                ->sub(new DateInterval($this->createIntervalString()))
                ;
    }


    private function createIntervalString():string 
    {
        return 'P' . 
                random_int(0, 3) .'Y'. 
                random_int(0, 11) .'M'.
                random_int(0, 30) .'D'.
                'T' . 
                random_int(0, 23) .'H'. 
                random_int(0, 59) .'M'.
                random_int(0, 59) .'S'
                ;
    }

}