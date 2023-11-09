<?php

namespace Boatrace\Sakura;

use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
class StadiumCrawler extends BaseCrawler
{
    /**
     * @param  \Symfony\Component\BrowserKit\HttpBrowser  $httpBrowser
     * @return void
     */
    public function __construct(HttpBrowser $httpBrowser)
    {
        parent::__construct($httpBrowser);
    }

    /**
     * @param  string  $date
     * @return array
     */
    public function crawlStadiumId(string $date): array
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
            $response[] = Converter::convertToStadiumId(
                str_replace('>', '', $element->filter('a')->filter('img')->attr('alt'))
            );
        });

        return $response;
    }

    /**
     * @param  string  $date
     * @return array
     */
    public function crawlStadiumName(string $date): array
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
            $response[] = str_replace('>', '', $element->filter('a')->filter('img')->attr('alt'));
        });

        return $response;
    }
}
