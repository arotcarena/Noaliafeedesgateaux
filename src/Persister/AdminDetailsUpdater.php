<?php
namespace App\Persister;

use App\Entity\User;
use App\Entity\Location;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminDetailsUpdater
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private UserRepository $userRepository
    )
    {

    }

    public function update(Object $data):bool
    {

        $location = new Location;
        $location
                ->setCity($data->city)
                ->setPostcode($data->postcode)
                ->setX($data->x)
                ->setY($data->y)
                ;

        $admin = $this->userRepository->findAdministrator();
        $admin
                ->setPhone($data->phone)
                ->setEmail($data->email)
                ->setFacebook($data->facebook)
                ->setLocation(
                    $location
                )
                ;

        $errors = $this->validator->validate($admin);

        // on rajoute manuellement la validation du remplissage de tous les champs de location (car pour les users classiques les champs de Location doivent rester nullables)
        if(count($errors) === 0 && !empty($location->getCity()) && !empty($location->getPostcode()) && !empty($location->getX()) && !empty($location->getY()))
        {
            $this->em->flush();
            return true;
        }
        return false;
    }
}

