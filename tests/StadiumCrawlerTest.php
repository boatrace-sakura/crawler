<?php

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
    public function testCrawlStadiumId(): void
    {
        $expected = [4, 5, 6, 10, 15, 18, 23, 24];
        $actual = $this->crawler->crawlStadiumId('2017-03-31');
        $this->assertSame($expected, $actual);
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
