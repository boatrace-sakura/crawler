<?php

declare(strict_types=1);

namespace Boatrace\Venture\Project;

use Boatrace\Venture\Project\Crawlers\BaseCrawler;
use DI\Container;
use DI\ContainerBuilder;
use Illuminate\Support\Collection;

/**
 * @author shimomo
 */
class Crawler
{
    /**
     * @var array
     */
    protected static array $instances;

    /**
     * @var \DI\Container
     */
    protected static Container $container;

    /**
     * @param  \Boatrace\Venture\Project\MainCrawler  $crawler
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
        return self::getInstance('Crawler')->$name(...$arguments);
    }

    /**
     * @param  string  $name
     * @return \Boatrace\Venture\Project\Crawler|\Boatrace\Venture\Project\Crawlers\BaseCrawler
     */
    public static function getInstance(string $name): Crawler|BaseCrawler
    {
        return self::$instances[$name] ?? self::$instances[$name] = self::getContainer()->get($name);
    }

    /**
     * @return \DI\Container
     */
    public static function getContainer(): Container
    {
        return self::$container ?? self::$container = (function () {
            $builder = new ContainerBuilder;
            $builder->addDefinitions(__DIR__ . '/../config/definitions.php');
            return $builder->build();
        })();
    }
}
