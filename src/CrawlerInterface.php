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
     * @param  int     $seconds
     * @return array
     */
    public function crawl(array $response, string $date, int $stadiumId, int $raceNumber, int $seconds): array;
}
