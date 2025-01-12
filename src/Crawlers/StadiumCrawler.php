<?php

declare(strict_types=1);

namespace Boatrace\Venture\Project\Crawlers;

use Boatrace\Venture\Project\Converter;
use Carbon\CarbonImmutable as Carbon;

/**
 * @author shimomo
 */
class StadiumCrawler extends BaseCrawler
{
    /**
     * @param  \Carbon\CarbonImmutable  $date
     * @return array
     */
    public function crawl(Carbon $date): array
    {
        $crawlerFormat = '%s/owpc/pc/race/index?hd=%s';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $date->format('Ymd'));
        $crawler = $this->httpBrowser->request('GET', $crawlerUrl);
        sleep($this->seconds);

        $response = [];
        $crawler = $crawler->filter('.table1')->eq(0);
        $crawler = $crawler->filter('table tbody td.is-arrow1.is-fBold.is-fs15');
        $crawler->each(function ($element) use (&$response) {
            $stadiumName = str_replace('>', '', $element->filter('a')->filter('img')->attr('alt'));
            $stadiumId = Converter::stadiumIdByShortName($stadiumName);
            $response[$stadiumId] = $stadiumName;
        });

        return $response;
    }
}
