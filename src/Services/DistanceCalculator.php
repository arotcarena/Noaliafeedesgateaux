<?php 
namespace App\Services;

use App\Repository\UserRepository;

class DistanceCalculator 
{
    public function __construct(
        private UserRepository $userRepository
    )
    {

    }

    public function calculate(int $x, int $y): int
    {
        $admin = $this->userRepository->findOneBy([]);
        $adminX = $admin->getLocation()->getX();
        $adminY = $admin->getLocation()->getY();

        $diffX = $adminX > $x ? $adminX - $x: $x - $adminX;
        $diffY = $adminY > $y ? $adminY - $y: $y - $adminY;

        $distance = sqrt(pow($diffX, 2) + pow($diffY, 2));
        return round($distance / 100000);
    }
}