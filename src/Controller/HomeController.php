<?php
namespace App\Controller;

use App\DataFixturesHelper\DateCreator;
use App\Email\OrderEmailSender;
use App\Entity\Order;
use App\Services\VisitCounter;
use App\Repository\CakeRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController 
{
    public function __construct(
        private CakeRepository $cakeRepository,
        private VisitCounter $visitCounter,
        private ValidatorInterface $validator
    )
    {

    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $this->visitCounter->count('home');

        $cakesSpotlighted = $this->cakeRepository->findAllSpotlighted();

        //entre 1 et 3 cakes, le carousel ne fonctionnera pas bien, donc:
        switch (count($cakesSpotlighted)) {
            case 1:
                $cakesSpotlighted[] = $cakesSpotlighted[0];
                $cakesSpotlighted[] = $cakesSpotlighted[0];
                $cakesSpotlighted[] = $cakesSpotlighted[0];
                break;
                case 2:
                    $cakesSpotlighted[] = $cakesSpotlighted[0];
                    $cakesSpotlighted[] = $cakesSpotlighted[1];
                    break;
                    case 3:
                        $cakesSpotlighted[] = $cakesSpotlighted[1];
                        break;
        }

        return $this->render('home/index.html.twig', [
            'current_menu' => 'home',
            'cakes_spotlighted' => $cakesSpotlighted
        ]);
    }
}