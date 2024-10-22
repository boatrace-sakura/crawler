<?php

declare(strict_types=1);

namespace Boatrace\Sakura;

use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
abstract class BaseCrawler
{
    /**
     * @var \Symfony\Component\BrowserKit\HttpBrowser
     */
    protected $httpBrowser;

    /**
     * @var string
     */
    protected $baseUrl = 'https://www.boatrace.jp';

    /**
     * @var int
     */
    protected $seconds = 1;

    /**
     * @param  \Symfony\Component\BrowserKit\HttpBrowser  $httpBrowser
     * @return void
     */
    protected function __construct(HttpBrowser $httpBrowser)
    {
        $this->httpBrowser = $httpBrowser;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  string                                 $xpath
     * @return string|null
     */
    protected function filterXPath(Crawler $crawler, string $xpath): ?string
    {
        return count($crawler->filterXPath($xpath))
            ? Converter::convertToString($crawler->filterXPath($xpath)->text())
            : null;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  string                                 $xpath
     * @return string
     */
    protected function filterXPathForWindDirection(Crawler $crawler, string $xpath): ?string
    {
        return count($crawler->filterXPath($xpath))
            ? Converter::convertToString($crawler->filterXPath($xpath)->attr('class'))
            : null;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  string                                 $xpath
     * @return float|null
     */
    protected function filterXPathForOdds(Crawler $crawler, string $xpath): ?float
    {
        return count($crawler->filterXPath($xpath))
            ? Converter::convertToFloat($crawler->filterXPath($xpath)->text())
            : null;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  string                                 $xpath
     * @return array
     */
    protected function filterXPathForOddsWithLowerLimitAndUpperLimit(Crawler $crawler, string $xpath): array
    {
        $response = [];

        if (count($crawler->filterXPath($xpath))) {
            if (count($oddses = explode('-', $crawler->filterXPath($xpath)->text())) === 2) {
                $lowerLimit = Converter::convertToFloat(array_shift($oddses));
                $upperLimit = Converter::convertToFloat(array_shift($oddses));
            }
        }

        $response['lower_limit'] = $lowerLimit ?? null;
        $response['upper_limit'] = $upperLimit ?? null;

        return $response;
    }
}
