# Cardmarket PHP SDK

:fire: **WORK IN PROGRESS**

[![Build Status](https://travis-ci.org/mamoot64/cardmarket-php-sdk.svg?branch=develop)](https://travis-ci.org/mamoot64/cardmarket-php-sdk)

Installation
------------

Use [Composer](http://getcomposer.org/) to install the package:

```
$ composer require mamoot/cardmarket-php-sdk
```

Usage and examples
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

Retrieve all cards for a given expansion.

```php
// All Pokemon cards of the Jungle expansion
$cardmarket->expansions()->getCardsListByExpansion(1525);
```

Retrieve the details and price guide of a single card   

```php
// Cards details for "Electrode (Holo) - Jungle expansion"
$cardmarket->cards()->getCardsDetails(273799);
```

### Add your custom Resources

It's not really a part of the Cardmarket SDK but if want to organize your work, it can be a good place!

````php
namespace Vendor\MyNamespace;

use Mamoot\CardMarket\Resources\HttpCaller;

class MyPokemonResource extends HttpCaller
{
    public function getOrderedExpansions(): array
    {
        // do stuff
    }
}
````

```php
use Vendor\MyNamespace\MyPokemonResource;

$cardmarket = new Cardmarket($httpCreator);
$cardmarket->registerResources('pokemon', MyPokemonResource::class);
```

Now you can manipulate your resource like the default provided :

```php
$cardmarket->pokemon()->getOrderedExpansions();
```

> You can't redefined default resources

### Execute test suite

If you want to run the tests you should run the following commands: 

```terminal
git clone git@github.com:mamoot64/cardmarket-php-sdk.git
cd cardmarket-php-sdk
composer install
composer test
```

If you want to generate coverage with HTML report, run:
```terminal
composer test-coverage-html
```