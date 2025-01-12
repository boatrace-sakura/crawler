<?php

declare(strict_types=1);

namespace Boatrace\Venture\Project;

use Carbon\CarbonImmutable as Carbon;
use Illuminate\Support\Collection;

/**
 * @author shimomo
 */
class MainCrawler
{
    /**
     * @return void
     */
    public function __construct()
    {
        Collection::macro('recursive', fn() => $this->map(fn($value) =>
            is_array($value) || is_object($value)
                ? collect($value)->recursive()
                : $value
        ));
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return \Illuminate\Support\Collection
     */
    public function __call(string $name, array $arguments): Collection
    {
        return $this->crawl($name, ...$arguments);
    }

    /**
     * @param  string    $name
     * @param  string    $date
     * @param  int|null  $stadiumId
     * @param  int|null  $raceNumber
     * @return \Illuminate\Support\Collection
     */
    public function crawl(string $name, string $date, ?int $stadiumId = null, ?int $raceNumber = null): Collection
    {
        $stadiumIds = $this->generateStadiumIds($stadiumId, $date);
        $raceNumbers = $this->generateRaceNumbers($raceNumber);
        $date = Carbon::parse($date);

        $response = [];
        foreach ($stadiumIds as $stadiumId) {
            foreach ($raceNumbers as $raceNumber) {
                $response = Crawler::getInstance(ucfirst($name) . 'Crawler')
                    ->crawl($response, $date, $stadiumId, $raceNumber);
            }
        }

        return collect($response)->recursive();
    }

    /**
     * @param  string  $date
     * @return \Illuminate\Support\Collection
     */
    public function stadium(string $date): Collection
    {
        return collect(Crawler::getInstance('StadiumCrawler')->crawl(
            Carbon::parse($date)
        ))->recursive();
    }

    /**
     * @param  string  $date
     * @return \Illuminate\Support\Collection
     */
    public function stadiumId(string $date): Collection
    {
        return $this->stadium($date)->keys();
    }

    /**
     * @param  string  $date
     * @return \Illuminate\Support\Collection
     */
    public function stadiumName(string $date): Collection
    {
        return $this->stadium($date)->values();
    }

    /**
     * @param  int|null  $stadiumId
     * @param  string    $date
     * @return array
     */
    protected function generateStadiumIds(?int $stadiumId, string $date): array
    {
        if (is_null($stadiumId)) {
            return $this->stadiumId($date)->all();
        }

        return [$stadiumId];
    }

    /**
     * @param  int|null  $raceNumber
     * @return array
     */
    protected function generateRaceNumbers(?int $raceNumber): array
    {
        if (is_null($raceNumber)) {
            return range(1, 12);
        }

        return [$raceNumber];
    }
}
