<?php

namespace App\Twig\Runtime;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Twig\Extension\RuntimeExtensionInterface;

class OrderDateFormatRuntime implements RuntimeExtensionInterface
{
    const TRANSLATE = [
        'Jan' => 'Jan.',
        'Feb' => 'Fév',
        'Mar' => 'Mar',
        'Apr' => 'Avr',
        'May' => 'Mai',
        'Jun' => 'Juin',
        'Jul' => 'Juil',
        'Aug' => 'Août',
        'Sep' => 'Sep',
        'Oct' => 'Oct',
        'Nov' => 'Nov',
        'Dec' => 'Déc',
        
        'Mon' => 'lun',
        'Tue' => 'mar',
        'Wed' => 'mer',
        'Thu' => 'jeu',
        'Fri' => 'ven',
        'Sat' => 'sam',
        'Sun' => 'dim'
    ];


    private ?string $dayNumeric;
    private ?string $dayString;
    private ?string $monthString;
    private ?string $year;
    private ?string $hour;
    private ?string $minute;

    private DateTimeImmutable $date;

    private DateTimeImmutable $today;

    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function orderDateFormat(DateTimeImmutable $value, bool $max = false)
    {
        $value->setTimezone(new DateTimeZone('Europe/Paris'));
        $this->date = $value;
        $this->today = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
        $this->initialize();
        
        if($max)
        {
            return $this->getMaxString();
        }
        return $this->getMinString();
    }

    private function getMinString():string
    {
        if($this->isSameDay())
        {
            return $this->getTime();
        }
        if($this->isYesterday())
        {
            return 'hier';
        }
        elseif($this->isSameWeek())
        {
            return $this->dayString;
        }
        elseif($this->isSameYear())
        {
            return $this->dayNumeric . ' ' .$this->monthString;
        }
        elseif($this->isPastYear())
        {
            return $this->monthString . ' ' . $this->year;
        }
        else
        {
            return $this->year;
        }
    }

    private function getMaxString():string 
    {
        if($this->isSameDay())
        {
            return 'Aujourd\'hui, ' . $this->getTime();
        }
        elseif($this->isYesterday())
        {
            return 'Hier, ' .$this->getTime();
        }
        elseif($this->isSameWeek())
        {
            return $this->dayString . ', ' . $this->getTime();
        }
        elseif($this->isSameYear())
        {
            return $this->dayNumeric . ' ' . $this->monthString . ', ' . $this->getTime();
        }
        else
        {
            return $this->dayNumeric . ' ' . $this->monthString . ' ' . $this->year . ', ' . $this->getTime();
        }
    }

    private function initialize():void
    {
        $this->dayNumeric = $this->date->format('d');

        $this->dayString = self::TRANSLATE[$this->date->format('D')] ?? $this->date->format('D');
        $this->monthString = self::TRANSLATE[$this->date->format('M')] ?? $this->date->format('M');
        $this->year = $this->date->format('Y');
        $this->hour = $this->date->format('H');
        $this->minute = $this->date->format('i');
    }

    private function getTime():string
    {
        return $this->hour . ':' . $this->minute;
    }

    private function isSameDay():bool
    {
        return $this->date->format('Y:m:d') === $this->today->format('Y:m:d');
    }
    private function isYesterday():bool 
    {
        return $this->date->format('Y:m:d') === $this->today->format('Y:m').':'.($this->today->format('d') - 1); 
    }
    private function isSameWeek():bool
    {
        $day = new DateTimeImmutable($this->today->format('Ymd'));
        $oneWeekBefore = $day->sub(new DateInterval('P7D'));
        $oneWeekAfter = $day->add(new DateInterval('P8D'));
        return ($oneWeekBefore < $this->date && $oneWeekAfter > $this->date);
    }
    private function isSameYear():bool
    {
        return $this->date->format('Y') === $this->today->format('Y');
    }
    private function isPastYear()
    {
        return (int)($this->date->format('Y')) === ((int)($this->today->format('Y')) - 1);
    }
}
