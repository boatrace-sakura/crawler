<?php

namespace Boatrace\Sakura;

use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class ResultCrawler extends BaseCrawler implements CrawlerInterface
{
    /**
     * @var int
     */
    protected $baseLevel = 0;

    /**
     * @var string
     */
    protected $baseXPath = 'descendant-or-self::body/main/div/div/div';

    /**
     * @param  \Symfony\Component\BrowserKit\HttpBrowser  $httpBrowser
     * @return void
     */
    public function __construct(HttpBrowser $httpBrowser)
    {
        parent::__construct($httpBrowser);
    }

    /**
     * @param  array   $response
     * @param  string  $date
     * @param  int     $stadiumId
     * @param  int     $raceNumber
     * @return array
     */
    public function crawl(array $response, string $date, int $stadiumId, int $raceNumber): array
    {
        $date = Converter::convertToDate($date);
        $stadiumId = Converter::convertToInt($stadiumId);
        $raceNumber = Converter::convertToInt($raceNumber);
        $boatraceDate = Carbon::parse($date)->format('Ymd');

        $crawlerFormat = '%s/owpc/pc/race/raceresult?hd=%s&jcd=%02d&rno=%d';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $boatraceDate, $stadiumId, $raceNumber);
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

        $wind = Converter::convertToWind($wind);
        $windDirection = Converter::convertToWindDirection($windDirection);
        $wave = Converter::convertToWave($wave);
        $weatherId = Converter::convertToWeatherId($weatherName);
        $temperature = Converter::convertToTemperature($temperature);
        $waterTemperature = Converter::convertToTemperature($waterTemperature);
        $techniqueId = Converter::convertToTechniqueId($techniqueName);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['date'] = $date;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['stadium_id'] = $stadiumId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['race_number'] = $raceNumber;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wind'] = $wind;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wind_direction'] = $windDirection;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['wave'] = $wave;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['weather_id'] = $weatherId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['temperature'] = $temperature;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['water_temperature'] = $waterTemperature;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['technique_id'] = $techniqueId;

        $response = $this->crawlPlaces($crawler, $response, $date, $stadiumId, $raceNumber);
        $response = $this->crawlCourses($crawler, $response, $date, $stadiumId, $raceNumber);
        $response = $this->crawlTrifectaOddses($crawler, $response, $date, $stadiumId, $raceNumber);
        $response = $this->crawlTrioOddses($crawler, $response, $date, $stadiumId, $raceNumber);

        return $response;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  array                                  $response
     * @param  string                                 $date
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlPlaces(Crawler $crawler, array $response, string $date, int $stadiumId, int $raceNumber): array
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

            $place = Converter::convertToPlaceId($place);
            $bracket = Converter::convertToInt($bracket);
            $racerNumber = Converter::convertToInt($racerNumber);
            $racerName = Converter::convertToName($racerName);

            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['place'] = $place;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['bracket'] = $bracket;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['racer_number'] = $racerNumber;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['places'][$index]['racer_name'] = $racerName;
        }

        return $response;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  array                                  $response
     * @param  string                                 $date
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlCourses(Crawler $crawler, array $response, string $date, int $stadiumId, int $raceNumber): array
    {
        $bracketFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[1]';
        $startTimingFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[3]/span';

        foreach (range(1, 6) as $course) {
            $bracketXPath = sprintf($bracketFormat, $this->baseXPath, $this->baseLevel + 5, $course);
            $startTimingXPath = sprintf($startTimingFormat, $this->baseXPath, $this->baseLevel + 5, $course);

            $bracket = $this->filterXPath($crawler, $bracketXPath);
            $startTiming = $this->filterXPath($crawler, $startTimingXPath);

            $bracket = Converter::convertToInt($bracket);
            $startTiming = Converter::convertToStartTiming($startTiming);

            $response['stadiums'][$stadiumId]['races'][$raceNumber]['courses'][$course]['course'] = $course;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['courses'][$course]['bracket'] = $bracket;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['courses'][$course]['start_timing'] = $startTiming;
        }

        return $response;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  array                                  $response
     * @param  string                                 $date
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlTrifectaOddses(Crawler $crawler, array $response, string $date, int $stadiumId, int $raceNumber): array
    {
        $trLevel = 1;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['trifecta_oddses'] = [];
        $trifectaFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[1]/tr[%s]/td[%s]/span';

        while (true) {
            $tdLevel = $trLevel === 1 ? 3 : 2;
            $trifectaXPath = sprintf($trifectaFormat, $this->baseXPath, $this->baseLevel + 6, $trLevel, $tdLevel);
            $trifecta = $this->filterXPath($crawler, $trifectaXPath);

            if (! str_starts_with($trifecta, '¥')) {
                break;
            }

            $trifecta = str_replace('¥', '', $trifecta);
            $trifecta = str_replace(',', '', $trifecta);

            $trLevel += 1;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['trifecta_oddses'][] = (int) $trifecta;
        }

        return $response;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  array                                  $response
     * @param  string                                 $date
     * @param  int                                    $stadiumId
     * @param  int                                    $raceNumber
     * @return array
     */
    protected function crawlTrioOddses(Crawler $crawler, array $response, string $date, int $stadiumId, int $raceNumber): array
    {
        $trLevel = 1;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['trio_oddses'] = [];
        $trifectaFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[2]/tr[%s]/td[%s]/span';

        while (true) {
            $tdLevel = $trLevel === 1 ? 3 : 2;
            $trifectaXPath = sprintf($trifectaFormat, $this->baseXPath, $this->baseLevel + 6, $trLevel, $tdLevel);
            $trifecta = $this->filterXPath($crawler, $trifectaXPath);

            if (! str_starts_with($trifecta, '¥')) {
                break;
            }

            $trifecta = str_replace('¥', '', $trifecta);
            $trifecta = str_replace(',', '', $trifecta);

            $trLevel += 1;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['trio_oddses'][] = (int) $trifecta;
        }

        return $response;
    }
}
