<?php

declare(strict_types=1);

namespace Boatrace\Venture\Project\Crawlers;

use Boatrace\Venture\Project\Converter;
use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class ResultCrawler extends BaseCrawler implements CrawlerInterface
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
        $crawlerFormat = '%s/owpc/pc/race/raceresult?hd=%s&jcd=%02d&rno=%d';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $date->format('Ymd'), $stadiumId, $raceNumber);
        $crawler = $this->httpBrowser->request('GET', $crawlerUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (! is_null($this->filterXPath($crawler, $levelXPath))) {
            $this->baseLevel = 1;
        }

        $windFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[3]/div/span[2]';
        $windDirectionFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[4]/p';
        $waveFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[6]/div/span[2]';
        $weatherNameFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[2]/div/span';
        $temperatureFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[1]/div/span[2]';
        $waterTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[5]/div/span[2]';
        $techniqueNameFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[2]/div[2]/table/tbody/tr/td';

        $windXPath = sprintf($windFormat, $this->baseXPath, $this->baseLevel + 6);
        $windDirectionXPath = sprintf($windDirectionFormat, $this->baseXPath, $this->baseLevel + 6);
        $waveXPath = sprintf($waveFormat, $this->baseXPath, $this->baseLevel + 6);
        $weatherNameXPath = sprintf($weatherNameFormat, $this->baseXPath, $this->baseLevel + 6);
        $temperatureXPath = sprintf($temperatureFormat, $this->baseXPath, $this->baseLevel + 6);
        $waterTemperatureXPath = sprintf($waterTemperatureFormat, $this->baseXPath, $this->baseLevel + 6);
        $techniqueNameXPath = sprintf($techniqueNameFormat, $this->baseXPath, $this->baseLevel + 6);

        $wind = $this->filterXPath($crawler, $windXPath);
        $windDirection = $this->filterXPathForWindDirection($crawler, $windDirectionXPath);
        $wave = $this->filterXPath($crawler, $waveXPath);
        $weatherName = $this->filterXPath($crawler, $weatherNameXPath);
        $temperature = $this->filterXPath($crawler, $temperatureXPath);
        $waterTemperature = $this->filterXPath($crawler, $waterTemperatureXPath);
        $techniqueName = $this->filterXPath($crawler, $techniqueNameXPath);

        $wind = Converter::wind($wind);
        $windDirection = Converter::direction($windDirection);
        $wave = Converter::wave($wave);
        $weatherId = Converter::weatherIdByName($weatherName);
        $temperature = Converter::temperature($temperature);
        $waterTemperature = Converter::temperature($waterTemperature);
        $techniqueId = Converter::techniqueIdByName($techniqueName);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['date'] = $date->format('Y-m-d');
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['stadium_id'] = $stadiumId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['race_number'] = $raceNumber;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wind'] = $wind;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wind_direction'] = $windDirection;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wave'] = $wave;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['weather_id'] = $weatherId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['temperature'] = $temperature;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['water_temperature'] = $waterTemperature;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['technique_id'] = $techniqueId;

        $response = $this->crawlPlaces($response, $crawler, $stadiumId, $raceNumber);
        $response = $this->crawlCourses($response, $crawler, $stadiumId, $raceNumber);
        $response = $this->crawlRefunds($response, $crawler, $stadiumId, $raceNumber, 1);
        $response = $this->crawlRefunds($response, $crawler, $stadiumId, $raceNumber, 2);
        $response = $this->crawlRefunds($response, $crawler, $stadiumId, $raceNumber, 3);
        $response = $this->crawlRefunds($response, $crawler, $stadiumId, $raceNumber, 4);
        $response = $this->crawlRefunds($response, $crawler, $stadiumId, $raceNumber, 5);
        $response = $this->crawlRefunds($response, $crawler, $stadiumId, $raceNumber, 6);
        $response = $this->crawlRefunds($response, $crawler, $stadiumId, $raceNumber, 7);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlPlaces(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $placeFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[1]';
        $bracketFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[2]';
        $racerNumberFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[3]/span[1]';
        $racerNameFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[3]/span[2]';

        foreach (range(1, 6) as $index) {
            $placeXPath = sprintf($placeFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $bracketXPath = sprintf($bracketFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerNumberXPath = sprintf($racerNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerNameXPath = sprintf($racerNameFormat, $this->baseXPath, $this->baseLevel + 5, $index);

            $place = $this->filterXPath($crawler, $placeXPath);
            $bracket = $this->filterXPath($crawler, $bracketXPath);
            $racerNumber = $this->filterXPath($crawler, $racerNumberXPath);
            $racerName = $this->filterXPath($crawler, $racerNameXPath);

            $place = Converter::placeIdByShortName($place);
            $bracket = Converter::int($bracket);
            $racerNumber = Converter::int($racerNumber);
            $racerName = Converter::name($racerName);

            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['place'] = $place;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['bracket'] = $bracket;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['racer_number'] = $racerNumber;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['racer_name'] = $racerName;
        }

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlCourses(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $bracketFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[1]';
        $startTimingFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[3]/span';

        foreach (range(1, 6) as $course) {
            $bracketXPath = sprintf($bracketFormat, $this->baseXPath, $this->baseLevel + 5, $course);
            $startTimingXPath = sprintf($startTimingFormat, $this->baseXPath, $this->baseLevel + 5, $course);

            $bracket = $this->filterXPath($crawler, $bracketXPath);
            $startTiming = $this->filterXPath($crawler, $startTimingXPath);

            $bracket = Converter::int($bracket);
            $startTiming = Converter::startTiming($startTiming);

            $response['stadiums'][$stadiumId]['races'][$raceNumber]['courses'][$course]['course'] = $course;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['courses'][$course]['bracket'] = $bracket;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['courses'][$course]['start_timing'] = $startTiming;
        }

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @param  int                                    $purchaseType
     * @return array
     */
    protected function crawlRefunds(array $response, Crawler $crawler, int $stadiumId, int $raceNumber, int $purchaseType): array
    {
        $purchaseTypeName = ['trifecta', 'trio', 'exacta', 'quinella', 'quinella_place', 'win', 'place'];
        $refundsKey = $purchaseTypeName[$purchaseType - 1];
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['refunds'][$refundsKey] = [];
        $refunds = &$response['stadiums'][$stadiumId]['races'][$raceNumber]['refunds'][$refundsKey];
        $refundFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr[%s]/td[%s]/span';

        for ($trLevel = 1; $trLevel <= 10; $trLevel++) {
            $tdLevel = $trLevel === 1 ? 3 : 2;
            $refundXPath = sprintf($refundFormat, $this->baseXPath, $this->baseLevel + 6, $purchaseType, $trLevel, $tdLevel);
            $refund = $this->filterXPath($crawler, $refundXPath);

            if (! str_starts_with($refund ?? '', '¥')) {
                break;
            }

            $refund = str_replace('¥', '', $refund);
            $refund = str_replace(',', '', $refund);
            $refunds[] = (int) $refund;
        }

        return $response;
    }
}
