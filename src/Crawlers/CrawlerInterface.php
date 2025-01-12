<?php

declare(strict_types=1);

namespace Boatrace\Venture\Project\Crawlers;

use Carbon\CarbonImmutable as Carbon;

/**
 * @author shimomo
 */
interface CrawlerInterface
{
    /**
     * @param  array                    $response
     * @param  \Carbon\CarbonImmutable  $date
     * @param  int                      $stadiumId
     * @param  int                      $raceNumber
     * @return array
     */
    public function crawl(array $response, Carbon $date, int $stadiumId, int $raceNumber): array;
}
