<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Tests;

use Mamoot\CardMarket\Cardmarket;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use Mamoot\CardMarket\Resources\MarketPlaceInformation\ExpansionsResource;
use Mamoot\CardMarket\Resources\MarketPlaceInformation\GamesResource;
use Mamoot\CardMarket\Resources\MarketPlaceInformation\ProductsResource;
use Mamoot\CardMarket\Resources\StockManagement\StockInShoppingCartsResource;
use Mamoot\CardMarket\Resources\StockManagement\StockResource;
use PHPUnit\Framework\TestCase;

class CardmarketTest extends TestCase
{

    private $cardmarket;

    public function setUp(): void
    {
        parent::setUp();

        $httpClientCreatorMock = $this->createMock(HttpClientCreator::class);
        $this->cardmarket = new Cardmarket($httpClientCreatorMock);
    }

    public function testCheckAccessToDefaultResources()
    {
        $this->assertInstanceOf(GamesResource::class, $this->cardmarket->games());
        $this->assertInstanceOf(ExpansionsResource::class, $this->cardmarket->expansions());
        $this->assertInstanceOf(ProductsResource::class, $this->cardmarket->cards());
        $this->assertInstanceOf(StockResource::class, $this->cardmarket->stock());
        $this->assertInstanceOf(StockInShoppingCartsResource::class, $this->cardmarket->stockInShoppingCarts());
    }

    public function testToRegisterDefaultResource()
    {
        $this->expectException(\LogicException::class);
        $this->cardmarket->registerResources("games", \stdClass::class);
    }

    public function testToRegisterNewResource()
    {
        $this->cardmarket->registerResources("new", \stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $this->cardmarket->new());
    }

    public function testThrowAnExceptionIfMethodDoesntExists()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->cardmarket->fake();
    }

}
