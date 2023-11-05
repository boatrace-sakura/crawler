<?php

namespace Boatrace\Sakura;

use DI\Container;
use DI\ContainerBuilder;
use Illuminate\Support\Collection;
use Boatrace\Sakura\BaseCrawler;

/**
 * @author shimomo
 */
class MainCrawler
{
    /**
     * @var array
     */
    protected $instances;

    /**
     * @var \DI\Container
     */
    protected $container;

    /**
     * @return void
     */
    public function __construct()
    {
        Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                return is_array($value) || is_object($value) ? collect($value)->recursive() : $value;
            });
        });
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return \Illuminate\Support\Collection
     */
    public function __call(string $name, array $arguments): Collection
    {
        return call_user_func_array([$this, 'crawl'], array_merge([mb_strtolower(str_replace('crawl', '', $name))], $arguments));
    }

    /**
     * @param  string    $name
     * @param  string    $date
     * @param  int|null  $stadiumId
     * @param  int|null  $raceNumber
     * @param  int|null  $seconds
     * @return \Illuminate\Support\Collection
     */
    public function crawl(string $name, string $date, int $stadiumId = null, int $raceNumber = null, int $seconds = null): Collection
    {
        if (is_null($seconds) || $seconds < 0) {
            $seconds = 1;
        }

        if (is_null($stadiumId)) {
            $stadiumIds = $this->getStadiumIds($date, $seconds);
        } else {
            $stadiumIds = [$stadiumId];
        }

        if (is_null($raceNumber)) {
            $raceNumbers = $this->getRaceNumbers();
        } else {
            $raceNumbers = [$raceNumber];
        }

        $response = [];

        foreach ($stadiumIds as $stadiumId) {
            foreach ($raceNumbers as $raceNumber) {
                $response = $this->getCrawler($name)->crawl($response, $date, $stadiumId, $raceNumber, $seconds);
            }
        }

        return collect($response)->recursive();
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

    /**
     * @param  string  $date
     * @param  int     $seconds
     * @return array
     */
    protected function getStadiumIds(string $date, int $seconds): array
    {
        return $this->getCrawler('stadium')->crawlStadiumId($date, $seconds);
    }

    /**
     * @return array
     */
    protected function getRaceNumbers(): array
    {
        return range(1, 12);
    }
}
