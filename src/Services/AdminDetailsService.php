<?php
namespace App\Services;

use App\Entity\Location;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Twig\Runtime\LocationExtensionRuntime;

class AdminDetailsService
{
    private ?User $administrator = null;

    public function __construct(
        private UserRepository $userRepository,
        private LocationExtensionRuntime $locationFormater
    )
    {
        $this->administrator = $this->userRepository->findAdministrator();
    }
    public function getEmailStart(): ?string 
    {
        if(!$this->administrator)
        {
            return null;
        }
        return explode('@', $this->administrator->getEmail())[0];
    }
    public function getEmailEnd(): ?string 
    {
        if(!$this->administrator)
        {
            return null;
        }
        return explode('@', $this->administrator->getEmail())[1];
    }
    public function getEmail(): ?string
    {
        if(!$this->administrator)
        {
            return null;
        }
        return $this->administrator->getEmail();
    }
    public function getPhone(): ?string 
    {
        if(!$this->administrator)
        {
            return null;
        }
        return $this->administrator->getPhone();
    }
    public function getLocation(): ?Location 
    {
        if(!$this->administrator)
        {
            return null;
        }
        return $this->administrator->getLocation();
    }
    public function getFacebook(): ?string 
    {
        if(!$this->administrator)
        {
            return null;
        }
        return $this->administrator->getFacebook();
    }
}