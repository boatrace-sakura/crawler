<?php

declare(strict_types=1);

namespace Boatrace\Sakura;

use DI\Container;
use DI\ContainerBuilder;
use Illuminate\Support\Collection;

/**
 * @author shimomo
 */
class Crawler
{
    /**
     * @var \Boatrace\Sakura\MainCrawler
     */
    protected $crawler;

    /**
     * @var \Boatrace\Sakura\Crawler
     */
    protected static $instance;

    /**
     * @var \DI\Container
     */
    protected static $container;

    /**
     * @param  \Boatrace\Sakura\MainCrawler  $crawler
     * @return void
     */
    public function __construct(MainCrawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return \Illuminate\Support\Collection
     */
    public function __call(string $name, array $arguments): Collection
    {
        return call_user_func_array([$this->crawler, $name], $arguments);
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return \Illuminate\Support\Collection
     */
    public static function __callStatic(string $name, array $arguments): Collection
    {
        return call_user_func_array([self::getInstance(), $name], $arguments);
    }

    /**
     * @return \Boatrace\Sakura\Crawler
     */
    public static function getInstance(): Crawler
    {
        return self::$instance ?? self::$instance = (
            self::$container ?? self::$container = self::getContainer()
        )->get('Crawler');
    }

    /**
     * @return \DI\Container
     */
    public static function getContainer(): Container
    {
        $builder = new ContainerBuilder;
        $builder->addDefinitions(__DIR__ . '/../config/definitions.php');
        return $builder->build();
    }
}
