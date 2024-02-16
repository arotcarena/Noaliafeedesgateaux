<?php

namespace App\Twig\Runtime;

use App\DataModel\VisitSearchFilter;
use Twig\Extension\RuntimeExtensionInterface;

class PageNameConverterRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function convert($value):string
    {
        if($value === VisitSearchFilter::PAGE_CAKES)
        {
            return 'Toutes mes créations';
        }
        elseif($value === VisitSearchFilter::PAGE_HOME)
        {
            return 'Page d\'Accueil';
        }
        return '';
    }
}
