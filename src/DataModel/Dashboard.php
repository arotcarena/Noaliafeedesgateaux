<?php

namespace App\DataModel;

use App\Config;
use App\Entity\User;


class Dashboard
{
    private ?int $id = null;

    private ?int $countCakesSpotlighted = null;

    private ?int $countCakes = null;

    private ?int $countOrdersNotSeen = null;

    private ?int $countOrders = null;

    private ?int $countVisitsToday = null;

    private ?int $countVisits = null;

    private ?User $administrator = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountCakesSpotlighted(): ?int
    {
        return $this->countCakesSpotlighted;
    }

    public function setCountCakesSpotlighted(int $countCakesSpotlighted): self
    {
        $this->countCakesSpotlighted = $countCakesSpotlighted;

        return $this;
    }

    public function getCountCakes(): ?int
    {
        return $this->countCakes;
    }

    public function setCountCakes(int $countCakes): self
    {
        $this->countCakes = $countCakes;

        return $this;
    }

    public function getCountOrdersNotSeen(): ?int
    {
        return $this->countOrdersNotSeen;
    }

    public function setCountOrdersNotSeen(int $countOrdersNotSeen): self
    {
        $this->countOrdersNotSeen = $countOrdersNotSeen;

        return $this;
    }

    public function getCountOrders(): ?int
    {
        return $this->countOrders;
    }

    public function setCountOrders(int $countOrders): self
    {
        $this->countOrders = $countOrders;

        return $this;
    }

    public function getCountVisitsToday(): ?int
    {
        return $this->countVisitsToday;
    }

    public function setCountVisitsToday(int $countVisitsToday): self
    {
        $this->countVisitsToday = $countVisitsToday;

        return $this;
    }

    public function getCountVisits(): ?int
    {
        return $this->countVisits;
    }

    public function setCountVisits(int $countVisits): self
    {
        $this->countVisits = $countVisits;

        return $this;
    }

    public function getAdministrator(): ?User
    {
        return $this->administrator;
    }

    public function setAdministrator(User $administrator): self
    {
        $this->administrator = $administrator;

        return $this;
    }
    
    public function getMaxCakes(): int
    {
        return Config::MAX_CAKES;
    }

    public function getMaxOrders(): int 
    {
        return Config::MAX_ORDERS;
    }
    
    public function getMaxVisits(): int 
    {
        return Config::MAX_VISITS;
    }
}
