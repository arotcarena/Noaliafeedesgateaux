<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CarouselItemPaddingCalculatorRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CarouselItemPaddingCalculatorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('carouselItem_padding_calc', [CarouselItemPaddingCalculatorRuntime::class, 'calc']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('carouselItem_padding_calc', [CarouselItemPaddingCalculatorRuntime::class, 'calc']),
        ];
    }
}
