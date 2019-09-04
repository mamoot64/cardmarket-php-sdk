<?php
declare(strict_types=1);

namespace Mamoot\CardMarket;

use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use Mamoot\CardMarket\Resources\MarketPlaceInformation\ExpansionsResource;
use Mamoot\CardMarket\Resources\MarketPlaceInformation\GamesResource;
use Mamoot\CardMarket\Resources\MarketPlaceInformation\ProductsResource;
use Mamoot\CardMarket\Resources\StockManagement\StockInShoppingCartsResource;
use Mamoot\CardMarket\Resources\StockManagement\StockResource;
use Spatie\Macroable\Macroable;

/**
 * Class Cardmarket
 *
 * @package Mamoot\CardMarket
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
class Cardmarket
{
    use Macroable;

    /**
     * @var HttpClientCreator
     */
    private $httpClientCreator;

    public function __construct(HttpClientCreator $httpClientCreator)
    {
        $this->httpClientCreator = $httpClientCreator;
    }

    public function games(): GamesResource
    {
        return new GamesResource($this->httpClientCreator);
    }

    public function expansions(): ExpansionsResource
    {
        return new ExpansionsResource($this->httpClientCreator);
    }

    public function cards(): ProductsResource
    {
        return new ProductsResource($this->httpClientCreator);
    }

    public function stock(): StockResource
    {
        return new StockResource($this->httpClientCreator);
    }

    public function stockInShoppingCarts(): StockInShoppingCartsResource
    {
        return new StockInShoppingCartsResource($this->httpClientCreator);
    }

    /**
     * Register custom resources on Cardmarket wrapper.
     *
     * @param string $methodName
     * @param string $fqcn
     */
    public function registerResources(string $methodName, string $fqcn): void
    {
        if (in_array($methodName, $this->getDefaultResources())) {
            throw new \LogicException(sprintf("You can't override default resources (%s)", implode(', ', array_values($this->getDefaultResources()))));
        }

        $httpClientCreator = $this->httpClientCreator;

        self::macro($methodName, function () use ($httpClientCreator, $fqcn) {
            return new $fqcn($httpClientCreator);
        });
    }

    /**
     * Default methods names to access Cardmarket Resources.
     *
     * @return array
     */
    private function getDefaultResources(): array
    {
        return ["games", "expansions", "cards", "stock", "stockInShoppingCarts"];
    }
}
