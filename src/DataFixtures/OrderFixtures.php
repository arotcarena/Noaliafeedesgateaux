<?php

namespace App\DataFixtures;

use DateInterval;
use DateTimeZone;
use Faker\Generator;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\Location;
use App\DataFixturesHelper\DateCreator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class OrderFixtures extends Fixture
{
    private DateCreator $dateCreator;

    private Generator $faker;

    private array $postcodes;

    public function __construct(DateCreator $dateCreator)
    {
        // $this->dateCreator = $dateCreator;
        // /** @var Generator */
        // $this->faker = \Faker\Factory::create('fr_FR');
        // $this->postcodes = $this->generatePostcodes();
    }

    public function load(ObjectManager $manager): void
    {
        // for ($i=0; $i < 100; $i++) { 
        //     $date = $this->dateCreator->create();
        //     $order = new Order;
        //     $order->setFullName($this->faker->name())
        //         ->setEmail($this->faker->email())
        //         ->setCreatedAt($date)
        //         ->setInvoiceNumber($date->format('Ymd').'-'.random_int(1000, 9999))
        //         ;
            
        //     if(random_int(0, 9) >= 4)
        //     {
        //         $order->setPhone($this->faker->phoneNumber());
        //     }   
        //     if(random_int(0, 9) >= 4)
        //     {
        //         $order->setLocation($this->createLocation(true));
        //     }
        //     else
        //     {
        //         $order->setLocation($this->createLocation());
        //     }
        //     if(random_int(0, 9) >= 4)
        //     {
        //         $order->setMessage($this->faker->paragraph(6));
        //     }
        //     if(random_int(0, 9) > 1)
        //     {
        //         $order->setSeen(true);
        //     }


        //     if($i === 3)
        //     {
        //         $order->setCreatedAt((new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))->sub(new DateInterval('P1DT2H')));
        //     }
        //     elseif($i === 4)
        //     {
        //         $order->setCreatedAt((new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))->sub(new DateInterval('PT3H')));
        //     }
        //     $manager->persist($order);
        // }
        // $manager->flush();
    }

    

    private function createLocation(?bool $full = false): Location
    {
        $location = new Location;

        if($full)
        {
            $this->hydrateLocationWithApiData($location);
        }
        else
        {
            $location->setCityManualEntry('ma ville');
        }
        return $location; 
    }

    private function hydrateLocationWithApiData(Location $location)
    {
         /*api adresse.gouv ***********************/
        // create curl resource
        $curl = curl_init(
                            'https://api-adresse.data.gouv.fr/search?q='
                            . $this->faker->randomElement($this->postcodes) .
                            '&limit=1&type=municipality'
                        );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $data = json_decode(curl_exec($curl));
        // close curl resource to free up system resources
        curl_close($curl);
        if(!empty($data->features))
        {
            $properties = $data->features[0]->properties;
            if(isset($properties->x) && isset($properties->y) && isset($properties->city) && isset($properties->postcode))
            {        
                $location->setCity($properties->city)
                        ->setPostcode($properties->postcode)
                        ->setX($properties->x)
                        ->setY($properties->y)
                        ;
            }
        }
    }

    private function generatePostcodes(): array
    {
        /*postcodes entrés manuellement*/
        $postCodes = [
            '64100', '64600', '64200', '13300', '75000', '69000', '33000', '13000', '59000', '31000', '65000', '64000', '40000'
        ];


        /*postcodes générés aléatoirement*/
        for($i=0; $i < 500; $i++) 
        {
            $postalCode = (string)(random_int(0, 9));
            $postalCode .= (string)(random_int(0, 9));
            $postalCode .= (string)(random_int(0, 9));
            if(random_int(0, 9) < 7)
            {
                $postalCode .= '0';
            }
            else
            {
                $postalCode .= (string)(random_int(0, 9));
            }
            $postalCode .= '0';
            if(!in_array($postalCode, $postCodes))
            {
                $postCodes[] = $postalCode;
            }
        }
        return $postCodes;
    }
}
