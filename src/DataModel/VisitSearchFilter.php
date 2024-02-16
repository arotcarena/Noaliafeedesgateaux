<?php
namespace App\DataModel;

class VisitSearchFilter
{
    public const PERIOD_TODAY = 'today';
    public const PERIOD_LAST_WEEK = 'lastWeek';
    public const PERIOD_LAST_MONTH = 'lastMonth';
    public const PERIOD_LAST_THREE_MONTHS = 'lastThreeMonths';
    public const PERIOD_LAST_YEAR = 'lastYear';

    public const PAGE_HOME = 'home';
    public const PAGE_CAKES = 'cakes_index';

    public const SORT_LATEST = 'latest';
    public const SORT_OLDEST = 'oldest';

    public ?string $period = null;

    public ?string $pageId = null;

    public string $sortBy = VisitSearchFilter::SORT_LATEST;
}