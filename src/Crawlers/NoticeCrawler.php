<?php

declare(strict_types=1);

namespace Boatrace\Venture\Project\Crawlers;

use Boatrace\Venture\Project\Converter;
use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class NoticeCrawler extends BaseCrawler implements CrawlerInterface
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
        $crawlerFormat = '%s/owpc/pc/race/beforeinfo?hd=%s&jcd=%02d&rno=%d';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $date->format('Ymd'), $stadiumId, $raceNumber);
        $crawler = $this->httpBrowser->request('GET', $crawlerUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (! is_null($this->filterXPath($crawler, $levelXPath))) {
            $this->baseLevel = 1;
        }

        $windFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[3]/div/span[2]';
        $windDirectionFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[4]/p';
        $waveFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[6]/div/span[2]';
        $weatherIdFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[2]/div/span';
        $temperatureFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[1]/div/span[2]';
        $waterTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[5]/div/span[2]';

        $windXPath = sprintf($windFormat, $this->baseXPath, $this->baseLevel + 5);
        $windDirectionXPath = sprintf($windDirectionFormat, $this->baseXPath, $this->baseLevel + 5);
        $waveXPath = sprintf($waveFormat, $this->baseXPath, $this->baseLevel + 5);
        $weatherNameXPath = sprintf($weatherIdFormat, $this->baseXPath, $this->baseLevel + 5);
        $temperatureXPath = sprintf($temperatureFormat, $this->baseXPath, $this->baseLevel + 5);
        $waterTemperatureXPath = sprintf($waterTemperatureFormat, $this->baseXPath, $this->baseLevel + 5);

        $wind = $this->filterXPath($crawler, $windXPath);
        $windDirection = $this->filterXPathForWindDirection($crawler, $windDirectionXPath);
        $wave = $this->filterXPath($crawler, $waveXPath);
        $weatherName = $this->filterXPath($crawler, $weatherNameXPath);
        $temperature = $this->filterXPath($crawler, $temperatureXPath);
        $waterTemperature = $this->filterXPath($crawler, $waterTemperatureXPath);

        $wind = Converter::wind($wind);
        $windDirection = Converter::direction($windDirection);
        $wave = Converter::wave($wave);
        $weatherId = Converter::weatherIdByName($weatherName);
        $temperature = Converter::temperature($temperature);
        $waterTemperature = Converter::temperature($waterTemperature);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['date'] = $date->format('Y-m-d');
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['stadium_id'] = $stadiumId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['race_number'] = $raceNumber;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wind_direction'] = $windDirection;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wind'] = $wind;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wave'] = $wave;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['weather_id'] = $weatherId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['temperature'] = $temperature;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['water_temperature'] = $waterTemperature;

        $response = $this->crawlRacers($response, $crawler, $stadiumId, $raceNumber);
        $response = $this->crawlCourses($response, $crawler, $stadiumId, $raceNumber);

        return $response;
    }

    /**
     * @param  array                                  $response
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlRacers(array $response, Crawler $crawler, int $stadiumId, int $raceNumber): array
    {
        $bracketFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[1]';
        $weightFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[4]';
        $weightAdjustmentFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[3]/td[1]';
        $exhibitionTimeFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[5]';
        $tiltAdjustmentFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[6]';

        foreach (range(1, 6) as $index) {
            $bracketXPath = sprintf($bracketFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $weightXPath = sprintf($weightFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $weightAdjustmentXPath = sprintf($weightAdjustmentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $exhibitionTimeXPath = sprintf($exhibitionTimeFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $tiltAdjustmentXPath = sprintf($tiltAdjustmentFormat, $this->baseXPath, $this->baseLevel + 5, $index);

            $bracket = $this->filterXPath($crawler, $bracketXPath);
            $weight = $this->filterXPath($crawler, $weightXPath);
            $weightAdjustment = $this->filterXPath($crawler, $weightAdjustmentXPath);
            $exhibitionTime = $this->filterXPath($crawler, $exhibitionTimeXPath);
            $tiltAdjustment = $this->filterXPath($crawler, $tiltAdjustmentXPath);

            $bracket = Converter::int($bracket);
            $weight = Converter::float($weight);
            $weightAdjustment = Converter::float($weightAdjustment);
            $exhibitionTime = Converter::float($exhibitionTime);
            $tiltAdjustment = Converter::float($tiltAdjustment);

            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$index]['bracket'] = $bracket;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$index]['weight'] = $weight;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$index]['weight_adjustment'] = $weightAdjustment;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$index]['exhibition_time'] = $exhibitionTime;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$index]['tilt_adjustment'] = $tiltAdjustment;
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
        $bracketFormat = '%s/div[2]/div[%s]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[1]';
        $startTimingFormat = '%s/div[2]/div[%s]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[3]';

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
}
