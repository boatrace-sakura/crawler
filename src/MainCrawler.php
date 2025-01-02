<?php

declare(strict_types=1);

namespace Boatrace\Sakura;

use Carbon\CarbonImmutable as Carbon;
use DI\Container;
use DI\ContainerBuilder;
use Illuminate\Support\Collection;

/**
 * @author shimomo
 */
class MainCrawler
{
    /**
     * @var array
     */
    protected array $instances;

    /**
     * @var \DI\Container
     */
    protected Container $container;

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
        return $this->crawl(mb_strtolower(str_replace('crawl', '', $name)), ...$arguments);
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
        $stadiumIds = is_null($stadiumId)
            ? $this->crawlStadiumId($date)->all()
            : [$stadiumId];

        $raceNumbers = is_null($raceNumber)
            ? range(1, 12)
            : [$raceNumber];

        $date = Carbon::parse($date);

        $response = [];
        foreach ($stadiumIds as $stadiumId) {
            foreach ($raceNumbers as $raceNumber) {
                $response = $this->getCrawler($name)
                    ->crawl($response, $date, $stadiumId, $raceNumber);
            }
        }

        return collect($response)->recursive();
    }

    /**
     * @param  string  $date
     * @return \Illuminate\Support\Collection
     */
    public function crawlStadium(string $date): Collection
    {
        return collect($this->getCrawler('stadium')->crawl(
            Carbon::parse($date)
        ))->recursive();
    }

    /**
     * @param  string  $date
     * @return \Illuminate\Support\Collection
     */
    public function crawlStadiumId(string $date): Collection
    {
        return $this->crawlStadium($date)->keys();
    }

    /**
     * @param  string  $date
     * @return \Illuminate\Support\Collection
     */
    public function crawlStadiumName(string $date): Collection
    {
        return $this->crawlStadium($date)->values();
    }

    /**
     * @param  string  $name
     * @return \Boatrace\Sakura\BaseCrawler
     */
    public function getCrawler(string $name): BaseCrawler
    {
        return $this->instances[$name] ?? $this->instances[$name] = (
            $this->container ?? $this->container = $this->getContainer()
        )->get(ucfirst($name) . 'Crawler');
    }

    /**
     * @return \DI\Container
     */
    public function getContainer(): Container
    {
        $builder = new ContainerBuilder;
        $builder->addDefinitions(__DIR__ . '/../config/definitions.php');
        return $builder->build();
    }
}
