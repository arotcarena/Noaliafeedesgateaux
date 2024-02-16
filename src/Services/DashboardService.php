<?php
namespace App\Services;

use App\Entity\User;
use App\DataModel\Dashboard;
use App\Repository\CakeRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;

class DashboardService
{
    public function __construct(
        private CakeRepository $cakeRepository,
        private OrderRepository $orderRepository,
        private VisitRepository $visitRepository,
        private UserRepository $userRepository
    )
    {
        
    }

    public function getDashboard():Dashboard
    {
        $dashboard = new Dashboard;
        $dashboard
                ->setCountCakesSpotlighted(
                    count($this->cakeRepository->findBy(['spotlighted' => true]))
                )
                ->setCountCakes(
                    count($this->cakeRepository->findAll())
                )
                ->setCountOrdersNotSeen(
                    count($this->orderRepository->findBy(['seen' => false]))
                )
                ->setCountOrders(
                    count($this->orderRepository->findAll())
                )
                ->setCountVisitsToday(
                    $this->visitRepository->countVisitsToday()
                )
                ->setCountVisits(
                    count($this->visitRepository->findBy(['visitedPageId' => 'home']))
                )
                ->setAdministrator($this->userRepository->findAdministrator())
                ;
        return $dashboard;
    }
}
