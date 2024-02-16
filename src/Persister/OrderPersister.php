<?php 
namespace App\Persister;

use DateTime;
use DateTimeZone;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderPersister
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    )
    {

    }

    public function persist(Object $data): ?Order
    {
        //si le user a utilisé la suggest list, on enregistre la location complète
        if(!empty($data->city) && !empty($data->postcode) && !empty($data->x) && !empty($data->y))
        {
            $location = new Location();
            $location 
                ->setCity($data->city)
                ->setPostcode($data->postcode)
                ->setX($data->x)
                ->setY($data->y)
                ;
        }
        //sinon si il a au moins rempli le champ, on enregistre juste ça
        elseif(!empty($data->cityManualEntry))
        {
            $location = new Location();
            $location->setCityManualEntry($data->cityManualEntry);
        }
        //sinon pas de location du tout
        else
        {
            $location = null;
        }

        $order = new Order();
        $order
            ->setFullName($data->fullName)
            ->setEmail($data->email)
            ->setPhone($data->phone)
            ->setMessage($data->message)
            ->setLocation($location)
            ->setInvoiceNumber(
                'C' . (string)(random_int(0, 9999)) . '-' . (new DateTime())->format('dmY')
            )
            ->setCreatedAt(new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))
            ;
        

        $errors = $this->validator->validate($order);
        $locationErrors = $location === null ? []: $this->validator->validate($location);
        if((count($errors) === 0) && count($locationErrors) === 0)
        {
            $this->em->persist($order);
            $this->em->flush();
            return $order;
        }
        return null;
    }
}