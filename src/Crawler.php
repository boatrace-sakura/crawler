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
     * @var \Boatrace\Sakura\Crawler
     */
    protected static Crawler $instance;

    /**
     * @var \DI\Container
     */
    protected static Container $container;

    /**
     * @param  \Boatrace\Sakura\MainCrawler  $crawler
     * @return void
     */
    public function __construct(protected MainCrawler $crawler){}

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return \Illuminate\Support\Collection
     */
    public function __call(string $name, array $arguments): Collection
    {
        return $this->crawler->$name(...$arguments);
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return \Illuminate\Support\Collection
     */
    public static function __callStatic(string $name, array $arguments): Collection
    {
        return self::getInstance()->$name(...$arguments);
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
