[![Downloads this Month](https://img.shields.io/packagist/dm/achse/php-shapeshift-io-api.svg)](https://packagist.org/packages/achse/php-shapeshift-io-api)
[![Latest Stable Version](https://poser.pugx.org/achse/php-shapeshift-io-api/v/stable)](https://github.com/achse/php-shapeshift-io-api/releases)
[![Build Status](https://travis-ci.org/Achse/php-shapeshift-io-api.svg?branch=master)](https://travis-ci.org/Achse/php-shapeshift-io-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Achse/php-shapeshift-io-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Achse/php-shapeshift-io-api/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/Achse/php-shapeshift-io-api/badge.svg?branch=master)](https://coveralls.io/github/Achse/php-shapeshift-io-api?branch=master)

# Installation
```
composer require achse/php-shapeshift-io-api
```

# Usage
```php
$client = new Client();

$rate = $client->getRate(Coins::BITCOIN, Coins::LITECOIN);
```
