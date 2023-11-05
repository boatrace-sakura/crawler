<?php

return [
    'Crawler' => \DI\create('\Boatrace\Sakura\Crawler')->constructor(
        \DI\get('MainCrawler')
    ),
    'MainCrawler' => function ($container) {
        return $container->get('\Boatrace\Sakura\MainCrawler');
    },
    'NoticeCrawler' => \DI\create('\Boatrace\Sakura\NoticeCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'OddsCrawler' => \DI\create('\Boatrace\Sakura\OddsCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'ProgramCrawler' => \DI\create('\Boatrace\Sakura\ProgramCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'ResultCrawler' => \DI\create('\Boatrace\Sakura\ResultCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'StadiumCrawler' => \DI\create('\Boatrace\Sakura\StadiumCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'HttpBrowser' => function ($container) {
        return $container->get('\Symfony\Component\BrowserKit\HttpBrowser');
    },
];
