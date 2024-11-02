<?php

declare(strict_types=1);

namespace Boatrace\Sakura;

use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class ProgramCrawler extends BaseCrawler implements CrawlerInterface
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

        $crawlerFormat = '%s/owpc/pc/race/racelist?hd=%s&jcd=%02d&rno=%d';
        $crawlerUrl = sprintf($crawlerFormat, $this->baseUrl, $boatraceDate, $stadiumId, $raceNumber);
        $crawler = $this->httpBrowser->request('GET', $crawlerUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (! is_null($this->filterXPath($crawler, $levelXPath))) {
            $this->baseLevel = 1;
        }

        $titleFormat = '%s/div[1]/div/div[2]/h2';
        $subtitleDistanceFormat = '%s/div[2]/div[%s]/h3';
        $deadlineFormat = '%s/div[2]/div[2]/table/tbody/tr[1]/td[%s]';

        $titleXPath = sprintf($titleFormat, $this->baseXPath);
        $subtitleDistanceXPath = sprintf($subtitleDistanceFormat, $this->baseXPath, $this->baseLevel + 3);
        $deadlineXPath = sprintf($deadlineFormat, $this->baseXPath, $raceNumber + 1);

        $title = $this->filterXPath($crawler, $titleXPath);
        $subtitleDistance = $this->filterXPath($crawler, $subtitleDistanceXPath);
        $deadline = $this->filterXPath($crawler, $deadlineXPath);

        $closedAt = is_null($deadline) ? null : Converter::convertToDateTime($date . ' ' . $deadline . ':00');

        [$subtitle, $distance] = $this->explodeSubtitleDistance($subtitleDistance);

        $response['stadiums'][$stadiumId]['races'][$raceNumber]['date'] = $date;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['stadium_id'] = $stadiumId;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['race_number'] = $raceNumber;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['title'] = $title;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['subtitle'] = $subtitle;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['distance'] = $distance;
        $response['stadiums'][$stadiumId]['races'][$raceNumber]['closed_at'] = $closedAt;

        $response = $this->crawlRacers($crawler, $response, $date, $stadiumId, $raceNumber);

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
    protected function crawlRacers(Crawler $crawler, array $response, string $date, int $stadiumId, int $raceNumber): array
    {
        $nameFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[3]/div[2]/a';
        $numberClassNameFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[3]/div[1]';
        $branchNameBirthplaceNameAgeWeightFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[3]/div[3]';
        $flyingCountLateCountStartTimingFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[4]';
        $national123PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[5]';
        $local123PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[6]';
        $motorNumberMotor23PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[7]';
        $boatNumberBoat23PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[8]';

        foreach (range(1, 6) as $bracket) {
            $nameXPath = sprintf($nameFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);
            $numberClassNameXPath = sprintf($numberClassNameFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);
            $branchNameBirthplaceNameAgeWeightXPath = sprintf($branchNameBirthplaceNameAgeWeightFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);
            $flyingCountLateCountStartTimingXPath = sprintf($flyingCountLateCountStartTimingFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);
            $national123PercentXPath = sprintf($national123PercentFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);
            $local123PercentXPath = sprintf($local123PercentFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);
            $motorNumberMotor23PercentXPath = sprintf($motorNumberMotor23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);
            $boatNumberMotor23PercentXPath = sprintf($boatNumberBoat23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $bracket);

            $name = $this->filterXPath($crawler, $nameXPath);
            $numberClassName = $this->filterXPath($crawler, $numberClassNameXPath);
            $branchNameBirthplaceNameAgeWeight = $this->filterXPath($crawler, $branchNameBirthplaceNameAgeWeightXPath);
            $flyingCountLateCountStartTiming = $this->filterXPath($crawler, $flyingCountLateCountStartTimingXPath);
            $national123Percent = $this->filterXPath($crawler, $national123PercentXPath);
            $local123Percent = $this->filterXPath($crawler, $local123PercentXPath);
            $motorNumberMotor23Percent = $this->filterXPath($crawler, $motorNumberMotor23PercentXPath);
            $boatNumberBoat23Percent = $this->filterXPath($crawler, $boatNumberMotor23PercentXPath);

            $name = Converter::convertToName($name);

            [$number, $classId] = $this->explodeNumberClassName($numberClassName);
            [$branchId, $birthplaceId, $age, $weight] = $this->explodeBranchNameBirthplaceNameAgeWeight($branchNameBirthplaceNameAgeWeight);
            [$flyingCount, $lateCount, $startTiming] = $this->explodeFlyingCountLateCountStartTiming($flyingCountLateCountStartTiming);
            [$national1Percent, $national2Percent, $national3Percent] = $this->explodeNational123Percent($national123Percent);
            [$local1Percent, $local2Percent, $local3Percent] = $this->explodeLocal123Percent($local123Percent);
            [$motorNumber, $motor2Percent, $motor3Percent] = $this->explodeMotorNumberMotor23Percent($motorNumberMotor23Percent);
            [$boatNumber, $boat2Percent, $boat3Percent] = $this->explodeBoatNumberBoat23Percent($boatNumberBoat23Percent);

            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['bracket'] = $bracket;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['name'] = $name;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['number'] = $number;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['class_id'] = $classId;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['branch_id'] = $branchId;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['birthplace_id'] = $birthplaceId;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['age'] = $age;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['weight'] = $weight;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['flying_count'] = $flyingCount;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['late_count'] = $lateCount;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['start_timing'] = $startTiming;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['national_1_percent'] = $national1Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['national_2_percent'] = $national2Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['national_3_percent'] = $national3Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['local_1_percent'] = $local1Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['local_2_percent'] = $local2Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['local_3_percent'] = $local3Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['motor_number'] = $motorNumber;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['motor_2_percent'] = $motor2Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['motor_3_percent'] = $motor3Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['boat_number'] = $boatNumber;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['boat_2_percent'] = $boat2Percent;
            $response['stadiums'][$stadiumId]['races'][$raceNumber]['racers'][$bracket]['boat_3_percent'] = $boat3Percent;
        }

        return $response;
    }

    /**
     * @param  string|null  $subtitleDistance
     * @return array
     */
    protected function explodeSubtitleDistance(string $subtitleDistance = null): array
    {
        if (is_null($subtitleDistance)) {
            return array_fill(0, 2, null);
        }

        $values = array_filter(explode(' ', trim($subtitleDistance)));
        $distance = Converter::convertToInt(array_pop($values));
        $subtitle = Converter::convertToString(implode($values));

        return [$subtitle, $distance];
    }

    /**
     * @param  string|null  $numberClassName
     * @return array
     */
    protected function explodeNumberClassName(string $numberClassName = null): array
    {
        if (is_null($numberClassName)) {
            return array_fill(0, 2, null);
        }

        [$number, $className] = explode('/', trim($numberClassName));

        $number = Converter::convertToInt($number);
        $classId = Converter::convertToClassId($className);

        return [$number, $classId];
    }

    /**
     * @param  string|null  $branchNameBirthplaceNameAgeWeight
     * @return array
     */
    protected function explodeBranchNameBirthplaceNameAgeWeight(string $branchNameBirthplaceNameAgeWeight = null): array
    {
        if (is_null($branchNameBirthplaceNameAgeWeight)) {
            return array_fill(0, 4, null);
        }

        [$branchNameBirthplaceName, $ageWeight] = explode(' ', trim($branchNameBirthplaceNameAgeWeight));
        [$branchName, $birthplaceName] = explode('/', trim($branchNameBirthplaceName));
        [$age, $weight] = explode('/', trim($ageWeight));

        $branchId = Converter::convertToPrefectureId($branchName);
        $birthplaceId = Converter::convertToPrefectureId($birthplaceName);
        $age = Converter::convertToInt($age);
        $weight = Converter::convertToFloat($weight);

        return [$branchId, $birthplaceId, $age, $weight];
    }

    /**
     * @param  string|null  $flyingCountLateCountStartTiming
     * @return array
     */
    protected function explodeFlyingCountLateCountStartTiming(string $flyingCountLateCountStartTiming = null): array
    {
        if (is_null($flyingCountLateCountStartTiming)) {
            return array_fill(0, 3, null);
        }

        [$flyingCount, $lateCount, $startTiming] = explode(' ', trim($flyingCountLateCountStartTiming));

        $flyingCount = Converter::convertToFlying($flyingCount);
        $lateCount = Converter::convertToLate($lateCount);
        $startTiming = Converter::convertToStartTiming($startTiming);

        return [$flyingCount, $lateCount, $startTiming];
    }

    /**
     * @param  string|null  $national123Percent
     * @return array
     */
    protected function explodeNational123Percent(string $national123Percent = null): array
    {
        if (is_null($national123Percent)) {
            return array_fill(0, 3, null);
        }

        [$national1Percent, $national2Percent, $national3Percent] = explode(' ', trim($national123Percent));

        $national1Percent = Converter::convertToFloat($national1Percent);
        $national2Percent = Converter::convertToFloat($national2Percent);
        $national3Percent = Converter::convertToFloat($national3Percent);

        return [$national1Percent, $national2Percent, $national3Percent];
    }

    /**
     * @param  string|null  $local123Percent
     * @return array
     */
    protected function explodeLocal123Percent(string $local123Percent = null): array
    {
        if (is_null($local123Percent)) {
            return array_fill(0, 3, null);
        }

        [$local1Percent, $local2Percent, $local3Percent] = explode(' ', trim($local123Percent));

        $local1Percent = Converter::convertToFloat($local1Percent);
        $local2Percent = Converter::convertToFloat($local2Percent);
        $local3Percent = Converter::convertToFloat($local3Percent);

        return [$local1Percent, $local2Percent, $local3Percent];
    }

    /**
     * @param  string|null  $motorNumberMotor23Percent
     * @return array
     */
    protected function explodeMotorNumberMotor23Percent(string $motorNumberMotor23Percent = null): array
    {
        if (is_null($motorNumberMotor23Percent)) {
            return array_fill(0, 3, null);
        }

        [$motorNumber, $motor2Percent, $motor3Percent] = explode(' ', trim($motorNumberMotor23Percent));

        $motorNumber = Converter::convertToInt($motorNumber);
        $motor2Percent = Converter::convertToFloat($motor2Percent);
        $motor3Percent = Converter::convertToFloat($motor3Percent);

        return [$motorNumber, $motor2Percent, $motor3Percent];
    }

    /**
     * @param  string|null  $boatNumberBoat23Percent
     * @return array
     */
    protected function explodeBoatNumberBoat23Percent(string $boatNumberBoat23Percent = null): array
    {
        if (is_null($boatNumberBoat23Percent)) {
            return array_fill(0, 3, null);
        }

        [$boatNumber, $boat2Percent, $boat3Percent] = explode(' ', trim($boatNumberBoat23Percent));

        $boatNumber = Converter::convertToInt($boatNumber);
        $boat2Percent = Converter::convertToFloat($boat2Percent);
        $boat3Percent = Converter::convertToFloat($boat3Percent);

        return [$boatNumber, $boat2Percent, $boat3Percent];
    }
}
