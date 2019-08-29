<?php
declare(strict_types=1);

namespace Mamoot\CardMarket;


use Mamoot\CardMarket\HttpClient\HttpClientCreator;

class Cardmarket {

  private $httpClientCreator;

  public function __construct(HttpClientCreator $httpClientCreator)
  {
    $this->httpClientCreator = $httpClientCreator;
  }

  public function games()
  {
    return new Resources\MarketPlaceInformation\GamesResource($this->httpClientCreator);
  }

  public function expansions()
  {
    return new Resources\MarketPlaceInformation\ExpansionsResource($this->httpClientCreator);
  }

  public function cards()
  {
    return new Resources\MarketPlaceInformation\ProductsResource($this->httpClientCreator);
  }
}