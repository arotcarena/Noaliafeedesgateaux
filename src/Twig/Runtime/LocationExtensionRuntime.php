<?php

namespace App\Twig\Runtime;

use App\Entity\Location;
use Twig\Extension\RuntimeExtensionInterface;

class LocationExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function format(?Location $location, $withParenthesis = false):string
    {
        if(!$location instanceof Location)
        {
            return '';
        }

        if($location->getCity() && $location->getPostcode())
        {
            if($withParenthesis)
            {
                return $location->getCity() . ' (' .$location->getPostcode() .')';
            }
            return $location->getCity() . ', ' .$location->getPostcode();
        }
        elseif($location->getCityManualEntry())
        {
            return $location->getCityManualEntry();
        }
    }
}
