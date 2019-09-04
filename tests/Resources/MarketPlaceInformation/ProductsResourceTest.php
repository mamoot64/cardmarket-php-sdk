<?php

namespace Mamoot\CardMarket\Tests\Resources;

use Mamoot\CardMarket\Resources\MarketPlaceInformation\GamesResource;
use Mamoot\CardMarket\Resources\MarketPlaceInformation\ProductsResource;
use Mamoot\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ProductsResourceTest extends ResourceTestCase
{

    /**
     * @var ProductsResource
     */
    private $productsResource;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->productsResource = new ProductsResource($this->httpClientCreatorMock);
    }

    public function testRetrieveGamesList()
    {
        $response = $this->productsResource->getCardsDetails(273799);

        $this->assertArrayHasKey('product', $response);
        $this->assertArrayHasKey('api', $response);

        $this->assertSame(5000, (int) $response['api']['request-limit-max']);
        $this->assertSame(1, (int) $response['api']['request-limit-count']);

        $propertiesToCheck = ['idProduct', 'countReprints', 'enName', 'image', 'gameName', 'idGame', 'number', 'rarity',
            'priceGuide', 'countArticles', 'countFoils'];

        foreach($propertiesToCheck as $keyName) {
            $this->assertArrayHasKey($keyName, $response['product']);
        }
    }

    protected function getMockResponses(): array
    {
        return [
          new MockResponse(file_get_contents(__DIR__ . "/../MockResponse/product_273799.json"),
            [
                'response_headers' => [
                  'X-Request-Limit-Max' => 5000,
                  'X-Request-Limit-Count' => 1,
                ]
            ]
          )
        ];
    }

}