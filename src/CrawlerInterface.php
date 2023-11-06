<?php

namespace Boatrace\Sakura;

/**
 * @author shimomo
 */
interface CrawlerInterface
{
    /**
     * @param  array   $response
     * @param  string  $date
     * @param  int     $stadiumId
     * @param  int     $raceNumber
     * @return array
     */
    public function crawl(array $response, string $date, int $stadiumId, int $raceNumber): array;
}
