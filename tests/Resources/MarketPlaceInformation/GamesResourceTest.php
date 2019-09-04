<?php

namespace Mamoot\CardMarket\Tests\Resources;

use Mamoot\CardMarket\Resources\MarketPlaceInformation\GamesResource;
use Mamoot\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class GamesResourceTest extends ResourceTestCase
{

    /**
     * @var GamesResource
     */
    private $gamesResource;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->gamesResource = new GamesResource($this->httpClientCreatorMock);
    }

    public function testRetrieveGamesList()
    {
        $response = $this->gamesResource->getGamesList();

        $this->assertArrayHasKey('game', $response);
        $this->assertArrayHasKey('links', $response);
        $this->assertArrayHasKey('api', $response);

        $this->assertSame(5000, (int) $response['api']['request-limit-max']);
        $this->assertSame(10, (int) $response['api']['request-limit-count']);
        $this->assertCount(2, $response['game']);
    }

    protected function getMockResponses(): array
    {
        $bodyValidGame = file_get_contents(__DIR__ . "/../MockResponse/game.json");

        return [
          new MockResponse($bodyValidGame,
            [
                'response_headers' => [
                  'X-Request-Limit-Max' => 5000,
                  'X-Request-Limit-Count' => 10,
                ]
            ]
          )
        ];
    }

}