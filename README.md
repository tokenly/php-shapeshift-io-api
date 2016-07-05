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
See https://info.shapeshift.io/api for more info about API calls and theirs parameters.

```php
$client = new Client();

$rate = $client->getRate(Coins::BITCOIN, Coins::LITECOIN);
```

* All results are arrays and `stdClass`-es.
* All errors from api that are caused by invalid requests (bad inputs, etc...) are `ApiErrorException`-s.
* Network errors are `RequestFailedException`. 

You can find some examples of usage in `tests/ClientTest.phpt`.

# Money vs. floats
All rate and amount numbers that came from shapeshift.io are represented 
in string float format, this library keeps it that way. 

However, some values like `limit` and some others came as floats from 
API. This library converts it to strings for consistency reasons.

> **Important:** NEVER represent money as float!

It's recommended to work with those values with `bcmath` functions, or 
implement some Money object as for example is: https://github.com/moneyphp/money.
