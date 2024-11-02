<?php

declare(strict_types=1);

namespace Boatrace\Sakura\Tests;

use Boatrace\Sakura\StadiumCrawler;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
class StadiumCrawlerTest extends PHPUnitTestCase
{
    /**
     * @var \Boatrace\Sakura\StadiumCrawler
     */
    protected $crawler;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->crawler = new StadiumCrawler(new HttpBrowser);
    }

    /**
     * @return void
     */
    public function testCrawlStadium(): void
    {
        $this->assertSame([
            4 => '平和島',
            5 => '多摩川',
            6 => '浜名湖',
            10 => '三国',
            15 => '丸亀',
            18 => '徳山',
            23 => '唐津',
            24 => '大村',
        ], $this->crawler->crawl('2017-03-31'));
    }

    /**
     * @return void
     */
    public function testCrawlStadiumName(): void
    {
        $expected = ['平和島', '多摩川', '浜名湖', '三国', '丸亀', '徳山', '唐津', '大村'];
        $actual = $this->crawler->crawlStadiumName('2017-03-31');
        $this->assertSame($expected, $actual);
    }
}
