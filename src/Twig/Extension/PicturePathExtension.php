<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\PicturePathExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PicturePathExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('picture_path', [PicturePathExtensionRuntime::class, 'getPath']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('picture_path', [PicturePathExtensionRuntime::class, 'getPath']),
        ];
    }
}
