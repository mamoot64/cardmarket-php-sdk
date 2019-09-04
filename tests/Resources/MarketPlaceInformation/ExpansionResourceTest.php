<?php

namespace Mamoot\CardMarket\Tests\Resources;

use Mamoot\CardMarket\Resources\MarketPlaceInformation\ExpansionsResource;
use Mamoot\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExpansionResourceTest extends ResourceTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRetrieveExpansionsListByGame()
    {
        $this->setupHttpClientCreatorMock();
        $expansionResource = new ExpansionsResource($this->httpClientCreatorMock);

        foreach($this->gameExpansionListProvider() as $provider) {

            $gameId = $provider[0];
            $countExpansions = $provider[1];
            $countApiRequestsUsed = $provider[2];
            $expansionId = $provider[3];

            $response = $expansionResource->getExpansionsListByGame($gameId);

            $this->assertArrayHasKey('expansion', $response);
            $this->assertArrayHasKey('links', $response);
            $this->assertArrayHasKey('api', $response);

            $this->assertSame(5000, (int)$response['api']['request-limit-max']);
            $this->assertSame($countApiRequestsUsed, (int)$response['api']['request-limit-count']);
            $this->assertEquals($countExpansions, count($response['expansion']));

            $this->assertSame($gameId, $response['expansion'][0]['idGame']);
            $this->assertSame($expansionId, $response['expansion'][0]['idExpansion']);
        }
    }

    public function testRetrieveSingleCardsListByExpansion()
    {
        $mockResponse = new MockResponse(file_get_contents(sprintf(__DIR__ . "/../MockResponse/expansion_singles_%s_%s.json", 6, 1525)), [
            'response_headers' => [
            'X-Request-Limit-Max' => 5000,
            'X-Request-Limit-Count' => 4589,
            ]]
        );

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $expansionResource = new ExpansionsResource($this->httpClientCreatorMock);

        $response = $expansionResource->getCardsListByExpansion(1525);

        $this->assertArrayHasKey('expansion', $response);
        $this->assertArrayHasKey('single', $response);
        $this->assertArrayHasKey('links', $response);
        $this->assertArrayHasKey('api', $response);

        $this->assertEquals(64, count($response['single']));
        $this->assertSame("Jungle", $response['expansion']['enName']);
        $this->assertEquals(6, (int) $response['expansion']['idGame']);
    }

    private function gameExpansionListProvider(): array
    {
        return [
          [1, 1, 10, 1469],
          [6, 2, 1000, 1604],
        ];
    }

    protected function getMockResponses(): array
    {
        return [
          $this->createMockResponse(1, 10),
          $this->createMockResponse(6, 1000),
        ];
    }

    private function createMockResponse(int $idGame, int $nbUsed): MockResponse
    {
        return new MockResponse(file_get_contents(sprintf(__DIR__ . "/../MockResponse/expansion_%d.json", $idGame)), [
          'response_headers' => [
            'X-Request-Limit-Max' => 5000,
            'X-Request-Limit-Count' => $nbUsed,
          ]]);
    }

}