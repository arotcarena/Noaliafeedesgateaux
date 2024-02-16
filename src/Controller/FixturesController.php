<?php
namespace App\Controller;

use DateInterval;
use DateTimeZone;
use Faker\Factory;
use App\Entity\Cake;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Order;
use App\Entity\Visit;
use DateTimeImmutable;
use App\Entity\Location;
use App\DataFixturesHelper\DateCreator;
use App\Repository\CakeRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FixturesController extends AbstractController 
{
    private Generator $faker;

    private array $postcodes;

    public function __construct(
        private DateCreator $dateCreator,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private UserRepository $userRepository,
        private OrderRepository $orderRepository,
        private VisitRepository $visitRepository,
        private CakeRepository $cakeRepository
    )
    {
        /** @var Generator */
        $this->faker = Factory::create('fr_FR');
        $this->postcodes = $this->generatePostcodes();
    }

    private function removeAll()
    {
        foreach($this->cakeRepository->findAll() as $cake)
        {
            $this->em->remove($cake);
        }
        foreach($this->orderRepository->findAll() as $order)
        {
            $this->em->remove($order);
        }
        foreach($this->visitRepository->findAll() as $visit)
        {
            $this->em->remove($visit);
        }
        foreach($this->userRepository->findAll() as $user)
        {
            $this->em->remove($user);
        }
        $this->em->flush();
    }

    
    #[Route('/fixtures', name: 'fixtures')]
    public function index(): Response
    {
        $this->removeAll();

        //USER
        $admin = new User;
        $admin->setRoles(['ROLE_ADMIN'])
                ->setStatus('administrator')
                ->setEmail('contact@noaliafeedesgateaux.fr')
                ->setFacebook('https://www.facebook.com')
                ->setPhone('07 53 31 15 18')
                ->setLocation(
                    (new Location)->setCity('Cannes')
                                    ->setPostcode('06400')
                                    ->setX('1023559.08')
                                    ->setY('6281139.85')
                )
                ->setPassword($this->hasher->hashPassword($admin, 'Noalia'))
                ;
        $this->em->persist($admin);

        $me = new User;
        $me->setRoles(['ROLE_ADMIN'])
                ->setStatus('dev')
                ->setEmail('arotcarena.ib@gmail.com')
                ->setFacebook('https://www.facebook.com')
                ->setPhone('06 00 00 00 00')
                ->setLocation(
                    (new Location)->setCity('Salon de Provence')
                                    ->setPostcode('13300')
                                    ->setX('1')
                                    ->setY('1')
                )
                ->setPassword($this->hasher->hashPassword($me, 'Ibai64600'))
                ;
        $this->em->persist($me);

        $this->em->flush();

        
        // //VISIT
        // for ($i=0; $i < 1000; $i++) 
        // { 
        //     $visit = new Visit;
        //     if(random_int(0, 9) > 3)
        //     {
        //         $visit->setVisitedPageId('home');
        //     }
        //     else
        //     {
        //         $visit->setVisitedPageId('cakes_index');
        //     }
        //     $visit->setVisitedAt($this->dateCreator->create());
        //     $this->em->persist($visit);
        // }
       
        // $this->em->flush();

        // //CAKES
        // for ($i=0; $i < 10; $i++) 
        // { 
        //     $cake = new Cake;

        //     if(random_int(0, 9) > 3)
        //     {
        //         $cake->setTitle('gateau '.$i);
        //     }
        //     if(random_int(0, 9) > 3)
        //     {
        //         $cake->setDescription('description du gateau '.$i);
        //     }

        //     $cake
        //         ->setCreatedAt($this->dateCreator->create())
        //         ->setSpotlighted(true)
        //         ->setCountVisit(random_int(0, 100))
        //         ->setLastVisit($this->dateCreator->create())
        //         ;
        //     $this->em->persist($cake);
        // }

        // for ($i=10; $i < 100; $i++) 
        // { 
        //     $cake = new Cake;

        //     if(random_int(0, 9) > 3)
        //     {
        //         $cake->setTitle('gateau '.$i);
        //     }
        //     if(random_int(0, 9) > 3)
        //     {
        //         $cake->setDescription('description du gateau '.$i);
        //     }

        //     $cake
        //         ->setCreatedAt($this->dateCreator->create())
        //         ->setCountVisit(random_int(0, 100))
        //         ->setLastVisit($this->dateCreator->create())
        //         ;
        //     $this->em->persist($cake);
        // }
       
        // $this->em->flush();

        // //ORDER
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
        //     $this->em->persist($order);
        // }
        // $this->em->flush();

        $this->addFlash('success', 'Les fixtures ont bien été jouées !');
        return $this->redirectToRoute('home');
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