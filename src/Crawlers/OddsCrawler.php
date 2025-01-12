<?php

declare(strict_types=1);

namespace Boatrace\Venture\Project\Crawlers;

use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class OddsCrawler extends BaseCrawler implements CrawlerInterface
{
    /**
     * @var string
     */
    protected string $baseXPath = 'descendant-or-self::body/main/div/div/div';

    /**
     * @var int
     */
    protected int $baseLevel = 0;

    /**
     * @param  array                    $response
     * @param  \Carbon\CarbonImmutable  $date
     * @param  int                      $stadiumId
     * @param  int                      $raceNumber
     * @return array
     */
    public function crawl(array $response, Carbon $date, int $stadiumId, int $raceNumber): array
    {
        $crawler1Format = '%s/owpc/pc/race/oddstf?hd=%s&jcd=%02d&rno=%d';
        $crawler1Url = sprintf($crawler1Format, $this->baseUrl, $date->format('Ymd'), $stadiumId, $raceNumber);
        $crawler1 = $this->httpBrowser->request('GET', $crawler1Url);
        sleep($this->seconds);

        $crawler2Format = '%s/owpc/pc/race/odds2tf?hd=%s&jcd=%02d&rno=%d';
        $crawler2Url = sprintf($crawler2Format, $this->baseUrl, $date->format('Ymd'), $stadiumId, $raceNumber);
        $crawler2 = $this->httpBrowser->request('GET', $crawler2Url);
        sleep($this->seconds);

        $crawler3Format = '%s/owpc/pc/race/oddsk?hd=%s&jcd=%02d&rno=%d';
        $crawler3Url = sprintf($crawler3Format, $this->baseUrl, $date->format('Ymd'), $stadiumId, $raceNumber);
        $crawler3 = $this->httpBrowser->request('GET', $crawler3Url);
        sleep($this->seconds);

        $crawler4Format = '%s/owpc/pc/race/odds3t?hd=%s&jcd=%02d&rno=%d';
        $crawler4Url = sprintf($crawler4Format, $this->baseUrl, $date->format('Ymd'), $stadiumId, $raceNumber);
        $crawler4 = $this->httpBrowser->request('GET', $crawler4Url);
        sleep($this->seconds);

        $crawler5Format = '%s/owpc/pc/race/odds3f?hd=%s&jcd=%02d&rno=%d';
        $crawler5Url = sprintf($crawler5Format, $this->baseUrl, $date->format('Ymd'), $stadiumId, $raceNumber);
        $crawler5 = $this->httpBrowser->request('GET', $crawler5Url);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (! is_null($this->filterXPath($crawler1, $levelXPath))) {
            $this->baseLevel = 1;
        }

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['date'] = $date->format('Y-m-d');
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['stadium_id'] = $stadiumId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['race_number'] = $raceNumber;

        $response = $this->crawlWin($response, $crawler1, $stadiumId, $raceNumber);
        $response = $this->crawlPlace($response, $crawler1, $stadiumId, $raceNumber);
        $response = $this->crawlExacta($response, $crawler2, $stadiumId, $raceNumber);
        $response = $this->crawlQuinella($response, $crawler2, $stadiumId, $raceNumber);
        $response = $this->crawlQuinellaPlace($response, $crawler3, $stadiumId, $raceNumber);
        $response = $this->crawlTrifecta($response, $crawler4, $stadiumId, $raceNumber);
        $response = $this->crawlTrio($response, $crawler5, $stadiumId, $raceNumber);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlTrifecta(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $trifecta123XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta124XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta125XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta126XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta132XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta134XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta135XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta136XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta142XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta143XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta145XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[11]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta146XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[12]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta152XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[13]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta153XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[14]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta154XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[15]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta156XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[16]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta162XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[17]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta163XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[18]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta164XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[19]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta165XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[20]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta213XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta214XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta215XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta216XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta231XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta234XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta235XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta236XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta241XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta243XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta245XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[11]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta246XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[12]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta251XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[13]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta253XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[14]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta254XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[15]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta256XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[16]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta261XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[17]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta263XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[18]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta264XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[19]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta265XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[20]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta312XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[9]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta314XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta315XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta316XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta321XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[9]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta324XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta325XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta326XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta341XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[9]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta342XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta345XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[11]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta346XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[12]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta351XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[13]/td[9]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta352XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[14]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta354XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[15]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta356XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[16]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta361XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[17]/td[9]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta362XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[18]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta364XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[19]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta365XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[20]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta412XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta413XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta415XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta416XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta421XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta423XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta425XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta426XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta431XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta432XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta435XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[11]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta436XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[12]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta451XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[13]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta452XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[14]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta453XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[15]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta456XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[16]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta461XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[17]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta462XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[18]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta463XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[19]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta465XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[20]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta512XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[15]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta513XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta514XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta516XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta521XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[15]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta523XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta524XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta526XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta531XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[15]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta532XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta534XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[11]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta536XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[12]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta541XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[13]/td[15]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta542XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[14]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta543XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[15]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta546XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[16]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta561XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[17]/td[15]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta562XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[18]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta563XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[19]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta564XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[20]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta612XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[18]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta613XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta614XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta615XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta621XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[18]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta623XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta624XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta625XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta631XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[18]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta632XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta634XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[11]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta635XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[12]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta641XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[13]/td[18]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta642XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[14]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta643XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[15]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta645XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[16]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta651XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[17]/td[18]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta652XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[18]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta653XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[19]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $trifecta654XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[20]/td[12]', $this->baseXPath, $this->baseLevel + 7);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][2][3] = $this->filterXPathForOdds($crawler, $trifecta123XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][2][4] = $this->filterXPathForOdds($crawler, $trifecta124XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][2][5] = $this->filterXPathForOdds($crawler, $trifecta125XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][2][6] = $this->filterXPathForOdds($crawler, $trifecta126XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][3][2] = $this->filterXPathForOdds($crawler, $trifecta132XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][3][4] = $this->filterXPathForOdds($crawler, $trifecta134XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][3][5] = $this->filterXPathForOdds($crawler, $trifecta135XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][3][6] = $this->filterXPathForOdds($crawler, $trifecta136XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][4][2] = $this->filterXPathForOdds($crawler, $trifecta142XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][4][3] = $this->filterXPathForOdds($crawler, $trifecta143XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][4][5] = $this->filterXPathForOdds($crawler, $trifecta145XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][4][6] = $this->filterXPathForOdds($crawler, $trifecta146XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][5][2] = $this->filterXPathForOdds($crawler, $trifecta152XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][5][3] = $this->filterXPathForOdds($crawler, $trifecta153XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][5][4] = $this->filterXPathForOdds($crawler, $trifecta154XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][5][6] = $this->filterXPathForOdds($crawler, $trifecta156XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][6][2] = $this->filterXPathForOdds($crawler, $trifecta162XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][6][3] = $this->filterXPathForOdds($crawler, $trifecta163XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][6][4] = $this->filterXPathForOdds($crawler, $trifecta164XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][1][6][5] = $this->filterXPathForOdds($crawler, $trifecta165XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][1][3] = $this->filterXPathForOdds($crawler, $trifecta213XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][1][4] = $this->filterXPathForOdds($crawler, $trifecta214XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][1][5] = $this->filterXPathForOdds($crawler, $trifecta215XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][1][6] = $this->filterXPathForOdds($crawler, $trifecta216XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][3][1] = $this->filterXPathForOdds($crawler, $trifecta231XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][3][4] = $this->filterXPathForOdds($crawler, $trifecta234XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][3][5] = $this->filterXPathForOdds($crawler, $trifecta235XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][3][6] = $this->filterXPathForOdds($crawler, $trifecta236XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][4][1] = $this->filterXPathForOdds($crawler, $trifecta241XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][4][3] = $this->filterXPathForOdds($crawler, $trifecta243XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][4][5] = $this->filterXPathForOdds($crawler, $trifecta245XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][4][6] = $this->filterXPathForOdds($crawler, $trifecta246XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][5][1] = $this->filterXPathForOdds($crawler, $trifecta251XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][5][3] = $this->filterXPathForOdds($crawler, $trifecta253XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][5][4] = $this->filterXPathForOdds($crawler, $trifecta254XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][5][6] = $this->filterXPathForOdds($crawler, $trifecta256XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][6][1] = $this->filterXPathForOdds($crawler, $trifecta261XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][6][3] = $this->filterXPathForOdds($crawler, $trifecta263XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][6][4] = $this->filterXPathForOdds($crawler, $trifecta264XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][2][6][5] = $this->filterXPathForOdds($crawler, $trifecta265XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][1][2] = $this->filterXPathForOdds($crawler, $trifecta312XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][1][4] = $this->filterXPathForOdds($crawler, $trifecta314XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][1][5] = $this->filterXPathForOdds($crawler, $trifecta315XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][1][6] = $this->filterXPathForOdds($crawler, $trifecta316XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][2][1] = $this->filterXPathForOdds($crawler, $trifecta321XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][2][4] = $this->filterXPathForOdds($crawler, $trifecta324XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][2][5] = $this->filterXPathForOdds($crawler, $trifecta325XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][2][6] = $this->filterXPathForOdds($crawler, $trifecta326XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][4][1] = $this->filterXPathForOdds($crawler, $trifecta341XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][4][2] = $this->filterXPathForOdds($crawler, $trifecta342XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][4][5] = $this->filterXPathForOdds($crawler, $trifecta345XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][4][6] = $this->filterXPathForOdds($crawler, $trifecta346XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][5][1] = $this->filterXPathForOdds($crawler, $trifecta351XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][5][2] = $this->filterXPathForOdds($crawler, $trifecta352XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][5][4] = $this->filterXPathForOdds($crawler, $trifecta354XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][5][6] = $this->filterXPathForOdds($crawler, $trifecta356XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][6][1] = $this->filterXPathForOdds($crawler, $trifecta361XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][6][2] = $this->filterXPathForOdds($crawler, $trifecta362XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][6][4] = $this->filterXPathForOdds($crawler, $trifecta364XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][3][6][5] = $this->filterXPathForOdds($crawler, $trifecta365XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][1][2] = $this->filterXPathForOdds($crawler, $trifecta412XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][1][3] = $this->filterXPathForOdds($crawler, $trifecta413XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][1][5] = $this->filterXPathForOdds($crawler, $trifecta415XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][1][6] = $this->filterXPathForOdds($crawler, $trifecta416XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][2][1] = $this->filterXPathForOdds($crawler, $trifecta421XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][2][3] = $this->filterXPathForOdds($crawler, $trifecta423XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][2][5] = $this->filterXPathForOdds($crawler, $trifecta425XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][2][6] = $this->filterXPathForOdds($crawler, $trifecta426XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][3][1] = $this->filterXPathForOdds($crawler, $trifecta431XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][3][2] = $this->filterXPathForOdds($crawler, $trifecta432XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][3][5] = $this->filterXPathForOdds($crawler, $trifecta435XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][3][6] = $this->filterXPathForOdds($crawler, $trifecta436XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][5][1] = $this->filterXPathForOdds($crawler, $trifecta451XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][5][2] = $this->filterXPathForOdds($crawler, $trifecta452XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][5][3] = $this->filterXPathForOdds($crawler, $trifecta453XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][5][6] = $this->filterXPathForOdds($crawler, $trifecta456XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][6][1] = $this->filterXPathForOdds($crawler, $trifecta461XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][6][2] = $this->filterXPathForOdds($crawler, $trifecta462XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][6][3] = $this->filterXPathForOdds($crawler, $trifecta463XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][4][6][5] = $this->filterXPathForOdds($crawler, $trifecta465XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][1][2] = $this->filterXPathForOdds($crawler, $trifecta512XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][1][3] = $this->filterXPathForOdds($crawler, $trifecta513XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][1][4] = $this->filterXPathForOdds($crawler, $trifecta514XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][1][6] = $this->filterXPathForOdds($crawler, $trifecta516XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][2][1] = $this->filterXPathForOdds($crawler, $trifecta521XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][2][3] = $this->filterXPathForOdds($crawler, $trifecta523XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][2][4] = $this->filterXPathForOdds($crawler, $trifecta524XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][2][6] = $this->filterXPathForOdds($crawler, $trifecta526XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][3][1] = $this->filterXPathForOdds($crawler, $trifecta531XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][3][2] = $this->filterXPathForOdds($crawler, $trifecta532XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][3][4] = $this->filterXPathForOdds($crawler, $trifecta534XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][3][6] = $this->filterXPathForOdds($crawler, $trifecta536XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][4][1] = $this->filterXPathForOdds($crawler, $trifecta541XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][4][2] = $this->filterXPathForOdds($crawler, $trifecta542XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][4][3] = $this->filterXPathForOdds($crawler, $trifecta543XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][4][6] = $this->filterXPathForOdds($crawler, $trifecta546XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][6][1] = $this->filterXPathForOdds($crawler, $trifecta561XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][6][2] = $this->filterXPathForOdds($crawler, $trifecta562XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][6][3] = $this->filterXPathForOdds($crawler, $trifecta563XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][5][6][4] = $this->filterXPathForOdds($crawler, $trifecta564XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][1][2] = $this->filterXPathForOdds($crawler, $trifecta612XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][1][3] = $this->filterXPathForOdds($crawler, $trifecta613XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][1][4] = $this->filterXPathForOdds($crawler, $trifecta614XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][1][5] = $this->filterXPathForOdds($crawler, $trifecta615XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][2][1] = $this->filterXPathForOdds($crawler, $trifecta621XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][2][3] = $this->filterXPathForOdds($crawler, $trifecta623XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][2][4] = $this->filterXPathForOdds($crawler, $trifecta624XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][2][5] = $this->filterXPathForOdds($crawler, $trifecta625XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][3][1] = $this->filterXPathForOdds($crawler, $trifecta631XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][3][2] = $this->filterXPathForOdds($crawler, $trifecta632XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][3][4] = $this->filterXPathForOdds($crawler, $trifecta634XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][3][5] = $this->filterXPathForOdds($crawler, $trifecta635XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][4][1] = $this->filterXPathForOdds($crawler, $trifecta641XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][4][2] = $this->filterXPathForOdds($crawler, $trifecta642XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][4][3] = $this->filterXPathForOdds($crawler, $trifecta643XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][4][5] = $this->filterXPathForOdds($crawler, $trifecta645XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][5][1] = $this->filterXPathForOdds($crawler, $trifecta651XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][5][2] = $this->filterXPathForOdds($crawler, $trifecta652XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][5][3] = $this->filterXPathForOdds($crawler, $trifecta653XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trifecta'][6][5][4] = $this->filterXPathForOdds($crawler, $trifecta654XPath);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlTrio(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $trio123XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trio124XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trio125XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trio126XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trio134XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trio135XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trio136XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trio145XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trio146XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $trio156XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $trio234XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trio235XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[6]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trio236XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[7]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trio245XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trio246XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $trio256XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trio345XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[8]/td[9]', $this->baseXPath, $this->baseLevel + 7);
        $trio346XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[9]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $trio356XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[9]', $this->baseXPath, $this->baseLevel + 7);
        $trio456XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[10]/td[12]', $this->baseXPath, $this->baseLevel + 7);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][2][3] = $this->filterXPathForOdds($crawler, $trio123XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][2][4] = $this->filterXPathForOdds($crawler, $trio124XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][2][5] = $this->filterXPathForOdds($crawler, $trio125XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][2][6] = $this->filterXPathForOdds($crawler, $trio126XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][3][4] = $this->filterXPathForOdds($crawler, $trio134XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][3][5] = $this->filterXPathForOdds($crawler, $trio135XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][3][6] = $this->filterXPathForOdds($crawler, $trio136XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][4][5] = $this->filterXPathForOdds($crawler, $trio145XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][4][6] = $this->filterXPathForOdds($crawler, $trio146XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][1][5][6] = $this->filterXPathForOdds($crawler, $trio156XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][2][3][4] = $this->filterXPathForOdds($crawler, $trio234XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][2][3][5] = $this->filterXPathForOdds($crawler, $trio235XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][2][3][6] = $this->filterXPathForOdds($crawler, $trio236XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][2][4][5] = $this->filterXPathForOdds($crawler, $trio245XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][2][4][6] = $this->filterXPathForOdds($crawler, $trio246XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][2][5][6] = $this->filterXPathForOdds($crawler, $trio256XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][3][4][5] = $this->filterXPathForOdds($crawler, $trio345XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][3][4][6] = $this->filterXPathForOdds($crawler, $trio346XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][3][5][6] = $this->filterXPathForOdds($crawler, $trio356XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['trio'][4][5][6] = $this->filterXPathForOdds($crawler, $trio456XPath);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlExacta(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $exacta12XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $exacta13XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $exacta14XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $exacta15XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $exacta16XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $exacta21XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $exacta23XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $exacta24XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $exacta25XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $exacta26XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $exacta31XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $exacta32XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $exacta34XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $exacta35XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $exacta36XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $exacta41XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $exacta42XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $exacta43XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $exacta45XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $exacta46XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $exacta51XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $exacta52XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $exacta53XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $exacta54XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $exacta56XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[10]', $this->baseXPath, $this->baseLevel + 7);
        $exacta61XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $exacta62XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $exacta63XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $exacta64XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[12]', $this->baseXPath, $this->baseLevel + 7);
        $exacta65XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[12]', $this->baseXPath, $this->baseLevel + 7);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][1][2] = $this->filterXPathForOdds($crawler, $exacta12XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][1][3] = $this->filterXPathForOdds($crawler, $exacta13XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][1][4] = $this->filterXPathForOdds($crawler, $exacta14XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][1][5] = $this->filterXPathForOdds($crawler, $exacta15XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][1][6] = $this->filterXPathForOdds($crawler, $exacta16XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][2][1] = $this->filterXPathForOdds($crawler, $exacta21XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][2][3] = $this->filterXPathForOdds($crawler, $exacta23XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][2][4] = $this->filterXPathForOdds($crawler, $exacta24XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][2][5] = $this->filterXPathForOdds($crawler, $exacta25XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][2][6] = $this->filterXPathForOdds($crawler, $exacta26XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][3][1] = $this->filterXPathForOdds($crawler, $exacta31XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][3][2] = $this->filterXPathForOdds($crawler, $exacta32XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][3][4] = $this->filterXPathForOdds($crawler, $exacta34XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][3][5] = $this->filterXPathForOdds($crawler, $exacta35XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][3][6] = $this->filterXPathForOdds($crawler, $exacta36XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][4][1] = $this->filterXPathForOdds($crawler, $exacta41XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][4][2] = $this->filterXPathForOdds($crawler, $exacta42XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][4][3] = $this->filterXPathForOdds($crawler, $exacta43XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][4][5] = $this->filterXPathForOdds($crawler, $exacta45XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][4][6] = $this->filterXPathForOdds($crawler, $exacta46XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][5][1] = $this->filterXPathForOdds($crawler, $exacta51XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][5][2] = $this->filterXPathForOdds($crawler, $exacta52XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][5][3] = $this->filterXPathForOdds($crawler, $exacta53XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][5][4] = $this->filterXPathForOdds($crawler, $exacta54XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][5][6] = $this->filterXPathForOdds($crawler, $exacta56XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][6][1] = $this->filterXPathForOdds($crawler, $exacta61XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][6][2] = $this->filterXPathForOdds($crawler, $exacta62XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][6][3] = $this->filterXPathForOdds($crawler, $exacta63XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][6][4] = $this->filterXPathForOdds($crawler, $exacta64XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['exacta'][6][5] = $this->filterXPathForOdds($crawler, $exacta65XPath);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlQuinella(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $quinella12XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[2]', $this->baseXPath, $this->baseLevel + 9);
        $quinella13XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[2]', $this->baseXPath, $this->baseLevel + 9);
        $quinella14XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[2]', $this->baseXPath, $this->baseLevel + 9);
        $quinella15XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[2]', $this->baseXPath, $this->baseLevel + 9);
        $quinella16XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[2]', $this->baseXPath, $this->baseLevel + 9);
        $quinella23XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[4]', $this->baseXPath, $this->baseLevel + 9);
        $quinella24XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[4]', $this->baseXPath, $this->baseLevel + 9);
        $quinella25XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[4]', $this->baseXPath, $this->baseLevel + 9);
        $quinella26XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[4]', $this->baseXPath, $this->baseLevel + 9);
        $quinella34XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[6]', $this->baseXPath, $this->baseLevel + 9);
        $quinella35XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[6]', $this->baseXPath, $this->baseLevel + 9);
        $quinella36XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[6]', $this->baseXPath, $this->baseLevel + 9);
        $quinella45XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[8]', $this->baseXPath, $this->baseLevel + 9);
        $quinella46XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[8]', $this->baseXPath, $this->baseLevel + 9);
        $quinella56XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[10]', $this->baseXPath, $this->baseLevel + 9);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][1][2] = $this->filterXPathForOdds($crawler, $quinella12XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][1][3] = $this->filterXPathForOdds($crawler, $quinella13XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][1][4] = $this->filterXPathForOdds($crawler, $quinella14XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][1][5] = $this->filterXPathForOdds($crawler, $quinella15XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][1][6] = $this->filterXPathForOdds($crawler, $quinella16XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][2][3] = $this->filterXPathForOdds($crawler, $quinella23XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][2][4] = $this->filterXPathForOdds($crawler, $quinella24XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][2][5] = $this->filterXPathForOdds($crawler, $quinella25XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][2][6] = $this->filterXPathForOdds($crawler, $quinella26XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][3][4] = $this->filterXPathForOdds($crawler, $quinella34XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][3][5] = $this->filterXPathForOdds($crawler, $quinella35XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][3][6] = $this->filterXPathForOdds($crawler, $quinella36XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][4][5] = $this->filterXPathForOdds($crawler, $quinella45XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][4][6] = $this->filterXPathForOdds($crawler, $quinella46XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella'][5][6] = $this->filterXPathForOdds($crawler, $quinella56XPath);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlQuinellaPlace(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $quinellaPlace12XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[1]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace13XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace14XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace15XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace16XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[2]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace23XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[2]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace24XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace25XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace26XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[4]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace34XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[3]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace35XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace36XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[6]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace45XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[4]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace46XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[8]', $this->baseXPath, $this->baseLevel + 7);
        $quinellaPlace56XPath = sprintf('%s/div[2]/div[%s]/table/tbody/tr[5]/td[10]', $this->baseXPath, $this->baseLevel + 7);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][1][2] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace12XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][1][3] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace13XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][1][4] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace14XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][1][5] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace15XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][1][6] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace16XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][2][3] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace23XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][2][4] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace24XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][2][5] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace25XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][2][6] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace26XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][3][4] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace34XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][3][5] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace35XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][3][6] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace36XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][4][5] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace45XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][4][6] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace46XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['quinella_place'][5][6] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $quinellaPlace56XPath);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlWin(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $win1XPath = sprintf('%s/div[2]/div[%s]/div[1]/div[2]/table/tbody[1]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $win2XPath = sprintf('%s/div[2]/div[%s]/div[1]/div[2]/table/tbody[2]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $win3XPath = sprintf('%s/div[2]/div[%s]/div[1]/div[2]/table/tbody[3]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $win4XPath = sprintf('%s/div[2]/div[%s]/div[1]/div[2]/table/tbody[4]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $win5XPath = sprintf('%s/div[2]/div[%s]/div[1]/div[2]/table/tbody[5]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $win6XPath = sprintf('%s/div[2]/div[%s]/div[1]/div[2]/table/tbody[6]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['win'][1] = $this->filterXPathForOdds($crawler, $win1XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['win'][2] = $this->filterXPathForOdds($crawler, $win2XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['win'][3] = $this->filterXPathForOdds($crawler, $win3XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['win'][4] = $this->filterXPathForOdds($crawler, $win4XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['win'][5] = $this->filterXPathForOdds($crawler, $win5XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['win'][6] = $this->filterXPathForOdds($crawler, $win6XPath);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlPlace(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $place1XPath = sprintf('%s/div[2]/div[%s]/div[2]/div[2]/table/tbody[1]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $place2XPath = sprintf('%s/div[2]/div[%s]/div[2]/div[2]/table/tbody[2]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $place3XPath = sprintf('%s/div[2]/div[%s]/div[2]/div[2]/table/tbody[3]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $place4XPath = sprintf('%s/div[2]/div[%s]/div[2]/div[2]/table/tbody[4]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $place5XPath = sprintf('%s/div[2]/div[%s]/div[2]/div[2]/table/tbody[5]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);
        $place6XPath = sprintf('%s/div[2]/div[%s]/div[2]/div[2]/table/tbody[6]/tr/td[3]', $this->baseXPath, $this->baseLevel + 6);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['place'][1] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $place1XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['place'][2] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $place2XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['place'][3] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $place3XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['place'][4] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $place4XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['place'][5] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $place5XPath);
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['oddses']['place'][6] = $this->filterXPathForOddsWithLowerLimitAndUpperLimit($crawler, $place6XPath);

        return $response;
    }
}
