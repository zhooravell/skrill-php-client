Skrill PHP client
=================
> Skrill api php wrapper.

[![License][license-image]][license-link] [![Build Status][travis-image]][travis-link] [![codecov][codecov-image]][codecov-link] [![scrutinizer][scrutinizer-image]][scrutinizer-link] [![intelligence][intelligence-image]][intelligence-link] 

![](https://www.skrill.com/fileadmin/templates/images/skrill-logo-gradient.svg)

## Installing

``` sh
$ composer require zhooravell/skrill-php-client
```

## Examples

```php
<?php

use Money\Money;
use Money\Currency;
use GuzzleHttp\Client;
use Skrill\SkrillClient;
use Skrill\ValueObject\Email;
use Skrill\Request\SaleRequest;
use Skrill\ValueObject\Password;
use Skrill\ValueObject\TransactionID;
use Skrill\Factory\RedirectUrlFactory;

$httpClient = new Client();
$email = new Email('...');
$password = new Password('...');

$client = new SkrillClient($httpClient, $email, $password);
$transactionID = new TransactionID('...');
$money = new Money(1, new Currency('USD'));
$request = new SaleRequest($transactionID, $this->parser->parse('10.5', 'EUR'));
$sid = $client->prepareSale($request);
$redirectURL = RedirectUrlFactory::fromSid($sid);

var_dump($redirectURL);
```

## Source(s)

* [Skrill](https://www.skrill.com)
* [Skrill Quick Checkout Guide](https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf)
* [Skrill Wallet Checkout Guide](https://www.skrill.com/fileadmin/content/pdf/Skrill_Wallet_Checkout_Guide.pdf)
* [Skrill Automated Payments Interface Guide](https://www.skrill.com/fileadmin/content/pdf/Skrill_Automated_Payments_Interface_Guide.pdf)

[license-link]: https://github.com/zhooravell/skrill-php-client/blob/master/LICENSE
[license-image]: https://img.shields.io/dub/l/vibe-d.svg

[travis-link]: https://travis-ci.com/zhooravell/skrill-php-client
[travis-image]: https://travis-ci.com/zhooravell/skrill-php-client.svg?branch=master

[codecov-link]: https://codecov.io/gh/zhooravell/skrill-php-client
[codecov-image]: https://codecov.io/gh/zhooravell/skrill-php-client/branch/master/graph/badge.svg

[scrutinizer-link]: https://scrutinizer-ci.com/g/zhooravell/skrill-php-client/?branch=master
[scrutinizer-image]: https://scrutinizer-ci.com/g/zhooravell/skrill-php-client/badges/quality-score.png?b=master

[intelligence-link]: https://scrutinizer-ci.com/code-intelligence
[intelligence-image]: https://scrutinizer-ci.com/g/zhooravell/skrill-php-client/badges/code-intelligence.svg?b=master
