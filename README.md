# Cardmarket PHP SDK

:fire: **WORK IN PROGRESS**

Installation
------------

Use [Composer](http://getcomposer.org/) to install the package:

```
$ composer require mamoot/cardmarket-php-sdk
```

Usage and example
-------

### Init the Cardmarket client
```php
use Mamoot\CardMarket\Cardmarket;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;

$httpCreator = new HttpClientCreator();
$httpCreator->setApplicationToken('your_application_token')
            ->setApplicationSecret('your_application_secret')
            ->setAccessToken('your_access_token')
            ->setAccessSecret('your_access_secret');

$cardmarket = new Cardmarket($httpCreator);
```

### Play with!

Retrieve all TCG Games from CardMarket   

```php
$cardmarket->games()->getGamesList());
```

Retrieve all expansions by game.   
 

```php
// All pokemon expansions
$cardmarket->expansions()->getExpansionsListByGame(6);

// All Magic The Gathering expansions
$cardmarket->expansions()->getExpansionsListByGame(1);
```

### Execute test suite

If you want to run the tests you should run the following commands: 

```terminal
git clone git@github.com:mamoot64/cardmarket-php-sdk.git
cd cardmarket-php-sdk
composer update
composer run test
```

If you want to generate coverage with HTML report, run:
```terminal
composer run test-coverage-html
```