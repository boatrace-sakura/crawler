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
     * @param  int     $seconds
     * @return array
     */
    public function crawlStadiumId(string $date, int $seconds): array
    {
        $response = [];

        $boatraceDate = Carbon::parse($date)->format('Ymd');

        $crawlerFormat = '%s/owpc/pc/race/index?hd=%s';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $boatraceDate);
        $crawler = $this->httpBrowser->request('GET', $crawlerUrl);
        sleep($seconds);

        $crawler->filter('.table1')->eq(0)->filter('table tbody td.is-arrow1.is-fBold.is-fs15')->each(function ($element) use (&$response) {
            $response[] = Converter::convertToStadiumId(str_replace('>', '', $element->filter('a')->filter('img')->attr('alt')));
        });

        return $response;
    }

    /**
     * @param  string  $date
     * @param  int     $seconds
     * @return array
     */
    public function crawlStadiumName(string $date, int $seconds): array
    {
        $response = [];

        $boatraceDate = Carbon::parse($date)->format('Ymd');

        $crawlerFormat = '%s/owpc/pc/race/index?hd=%s';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $boatraceDate);
        $crawler = $this->httpBrowser->request('GET', $crawlerUrl);
        sleep($seconds);

        $crawler->filter('.table1')->eq(0)->filter('table tbody td.is-arrow1.is-fBold.is-fs15')->each(function ($element) use (&$response) {
            $response[] = str_replace('>', '', $element->filter('a')->filter('img')->attr('alt'));
        });

        return $response;
    }
}
