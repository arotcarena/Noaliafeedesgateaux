<?php

namespace App\Twig\Runtime;

use App\Config;
use App\Entity\Picture;
use Twig\Extension\RuntimeExtensionInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class PicturePathExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private StorageInterface $storageInterface,
    )
    {

    }

    public function getPath(?Picture $picture, string $filePropertyName = 'file')
    {
        if($picture)
        {
            return $this->storageInterface->resolveUri($picture, $filePropertyName);
        }
        return Config::DEFAULT_PICTURE_PATH;
    }
}
