<?php

declare(strict_types=1);

namespace Boatrace\Sakura;

use Carbon\CarbonImmutable as Carbon;

/**
 * @author shimomo
 */
class StadiumCrawler extends BaseCrawler
{
    /**
     * @param  string  $date
     * @return array
     */
    public function crawl(string $date): array
    {
        $response = [];

        $boatraceDate = Carbon::parse($date)->format('Ymd');

        $crawlerFormat = '%s/owpc/pc/race/index?hd=%s';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $boatraceDate);
        $crawler = $this->httpBrowser->request('GET', $crawlerUrl);
        sleep($this->seconds);

        $crawler = $crawler->filter('.table1')->eq(0);
        $crawler = $crawler->filter('table tbody td.is-arrow1.is-fBold.is-fs15');
        $crawler->each(function ($element) use (&$response) {
            $stadiumName = str_replace('>', '', $element->filter('a')->filter('img')->attr('alt'));
            $stadiumId = Converter::convertToStadiumId($stadiumName);
            $response[$stadiumId] = $stadiumName;
        });

        return $response;
    }
}
