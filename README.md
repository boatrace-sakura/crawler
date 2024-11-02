# Boatrace Sakura Crawler

[![Build Status](https://github.com/boatrace-sakura/crawler/workflows/tests/badge.svg)](https://github.com/boatrace-sakura/crawler/actions?query=workflow%3Atests)
[![Coverage Status](https://coveralls.io/repos/github/boatrace-sakura/crawler/badge.svg?branch=main)](https://coveralls.io/github/boatrace-sakura/crawler?branch=main)
[![Latest Stable Version](https://poser.pugx.org/boatrace-sakura/crawler/v/stable)](https://packagist.org/packages/boatrace-sakura/crawler)
[![Latest Unstable Version](https://poser.pugx.org/boatrace-sakura/crawler/v/unstable)](https://packagist.org/packages/boatrace-sakura/crawler)
[![License](https://poser.pugx.org/boatrace-sakura/crawler/license)](https://packagist.org/packages/boatrace-sakura/crawler)

## Installation
```bash
composer require boatrace-sakura/crawler
```

## Usage
```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Boatrace\Sakura\Crawler;

var_dump(Crawler::crawlStadium('2017-03-31')); // 2017年03月31日, 開催場

var_dump(Crawler::crawlProgram('2017-03-31')); // 2017年03月31日, 出走表
var_dump(Crawler::crawlProgram('2017-03-31', 24)); // 2017年03月31日, 大村, 出走表
var_dump(Crawler::crawlProgram('2017-03-31', 24, 1)); // 2017年03月31日, 大村, 1R, 出走表

var_dump(Crawler::crawlNotice('2017-03-31')); // 2017年03月31日, 直前情報
var_dump(Crawler::crawlNotice('2017-03-31', 24)); // 2017年03月31日, 大村, 直前情報
var_dump(Crawler::crawlNotice('2017-03-31', 24, 1)); // 2017年03月31日, 大村, 1R, 直前情報

var_dump(Crawler::crawlResult('2017-03-31')); // 2017年03月31日, 結果
var_dump(Crawler::crawlResult('2017-03-31', 24)); // 2017年03月31日, 大村, 結果
var_dump(Crawler::crawlResult('2017-03-31', 24, 1)); // 2017年03月31日, 大村, 1R, 結果

var_dump(Crawler::crawlOdds('2017-03-31')); // 2017年03月31日, オッズ
var_dump(Crawler::crawlOdds('2017-03-31', 24)); // 2017年03月31日, 大村, オッズ
var_dump(Crawler::crawlOdds('2017-03-31', 24, 1)); // 2017年03月31日, 大村, 1R, オッズ
```

## License
The Boatrace Sakura Crawler is open source software licensed under the [MIT license](LICENSE).
