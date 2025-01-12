<?php

declare(strict_types=1);

return [
    'Crawler' => \DI\create('\Boatrace\Venture\Project\Crawler')->constructor(
        \DI\get('MainCrawler')
    ),
    'MainCrawler' => function ($container) {
        return $container->get('\Boatrace\Venture\Project\MainCrawler');
    },
    'NoticeCrawler' => \DI\create('\Boatrace\Venture\Project\Crawlers\NoticeCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'OddsCrawler' => \DI\create('\Boatrace\Venture\Project\Crawlers\OddsCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'ProgramCrawler' => \DI\create('\Boatrace\Venture\Project\Crawlers\ProgramCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'ResultCrawler' => \DI\create('\Boatrace\Venture\Project\Crawlers\ResultCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'StadiumCrawler' => \DI\create('\Boatrace\Venture\Project\Crawlers\StadiumCrawler')->constructor(
        \DI\get('HttpBrowser')
    ),
    'HttpBrowser' => function ($container) {
        return $container->get('\Symfony\Component\BrowserKit\HttpBrowser');
    },
];
