<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class CarouselItemPaddingCalculatorRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function calc(array $items)
    {
        switch (count($items)) {
            case 3:
                return '1.76%';
                break;
            
            case 4:
                return '1.44%';
                break;

            case 5:
                return '1.19%';
                break;

            case 6:
                return '1.02%';
                break;

            case 7:
                return '0.89%';
                break;
        
            case 8:
                return '0.8%';
                break;

            case 9:
                return '0.72%';
                break;

            case 10:
                return '0.65%';
                break;

            case 11:
                return '0.595%';
                break;

            case 12:
                return '0.55%';
                break;

            case 13:
                return '0.52%';
                break;

            case 14:
                return '0.49%';
                break;

            case 15:
                return '0.46%';
                break;
            
            default:
                return '0';
                break;
        }
    }
}
