<?php
namespace App\Converter;

use App\Entity\Cake;
use App\Twig\Runtime\PicturePathExtensionRuntime;

class CakeConverter
{
    public function __construct(
        private PicturePathExtensionRuntime $picturePathExtension
    )
    {

    }


    public function getImageShowDataToJson(Cake $cake):string
    {
        return json_encode($this->getImageShowData($cake));
    }

    /**
     * @param Cake[] $cake
     */
    public function getInfinitePaginationDataToJson(array $cakes):string 
    {
        return json_encode($this->getInfinitePaginationGroupData($cakes));
    }

    
    /**
     * @param Cake[] $cake
     */
    public function getInfinitePaginationGroupData(array $cakes):array 
    {
        $array = [];
        foreach($cakes as $cake)
        {
            $data['id'] = $cake->getId();
            $data['title'] = $cake->getTitle();
            $data['firstPicture'] = $this->picturePathExtension->getPath($cake->getFirstPicture());
            $array[] = $data;
        }
        return $array;
    }

    public function getImageShowData(Cake $cake):array
    {
        $data['id'] = $cake->getId();
        $data['title'] = $cake->getTitle();
        $data['description'] = $cake->getDescription();
        $data['firstPicture'] = $this->picturePathExtension->getPath($cake->getFirstPicture());
        foreach($cake->getPictures() as $picture)
        {
            $data['secondaryPictures'][] = $this->picturePathExtension->getPath($picture);
        }
        return $data;
    }
}