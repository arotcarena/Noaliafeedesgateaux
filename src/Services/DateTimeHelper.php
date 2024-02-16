<?php
namespace App\Services;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class DateTimeHelper
{
    public const DATE_TIME_ZONE = 'Europe/Paris';

    /**
     * @param string|null $dateString (e.g. '1984:12:22 15:50:12')
     * @param array|null $transformation ['dir' => 'duration'] (dir = 'sub' or 'add' / duration = e.g. 'P7D')
     * @return DateTimeImmutable
     */
    public function createImmutable(?string $dateString = 'now', array $transformation = null): DateTimeImmutable
    {
        $date = new DateTimeImmutable($dateString, $this->getDateTimeZone());
        return $this->applyTransformation($date, $transformation);
    }

    /**
     * @param string|null $dateString (e.g. '1984:12:22 15:50:12')
     * @param array|null $transformation ['dir' => 'duration'] (dir = 'sub' or 'add' / duration = e.g. 'P7D')
     * @return DateTimeImmutable
     */
    public function create(?string $dateString = 'now', array $transformation = null): DateTime
    {
        $date = new DateTime($dateString, $this->getDateTimeZone());
        return $this->applyTransformation($date, $transformation);
    }

    private function applyTransformation($date, $transformation)
    {
        if($transformation !== null)
        {
            $dir = array_key_first($transformation);
            $duration = array_values($transformation)[0];
            return $date->$dir(new DateInterval($duration));
        }
        return $date;
    }

    private function getDateTimeZone(): DateTimeZone
    {
        return new DateTimeZone(self::DATE_TIME_ZONE);
    }
}