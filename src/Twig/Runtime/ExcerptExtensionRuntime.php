<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class ExcerptExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getExcerpt($value, $maxCars = 50)
    {
        if(strlen($value) > $maxCars)
        {
            $array = explode(' ', substr($value, 0, $maxCars));
            if(count($array) > 1)
            {
                $offset = count($array) - 1;
                unset($array[$offset]);
                return implode(' ', $array) .' ...';
            }
            return implode(' ', $array) .'...';
        }
        return $value;
    }
}
