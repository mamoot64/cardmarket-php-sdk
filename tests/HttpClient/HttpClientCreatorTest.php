<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Tests\HttpClient;

use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientCreatorTest extends TestCase
{

  protected function setUp(): void
  {
    parent::setUp();
  }

  public function testCreateValidHttpClient()
  {
    $this->assertInstanceOf(HttpClientInterface::class, self::createHttpClient());
  }

  /**
   * @expectedException \Mamoot\CardMarket\Exception\HttpClientNotConfiguredException
   * @expectedExceptionMessage You need to provide "access_secret", "access_token", "app_secret" and "app_token" to create a correct HttpClient.
   */
  public function testCreateInValidHttpClient()
  {
    self::createHttpClient(FALSE);
  }

  private function createHttpClient(bool $configured = TRUE)
  {
    $httpClientCreator = new HttpClientCreator();

    if ($configured) {
      $httpClientCreator
        ->setAccessSecret('access_secret')
        ->setAccessToken('access_token')
        ->setApplicationSecret('app_secret')
        ->setApplicationToken('app_token');
    }

    return $httpClientCreator->createHttpClient();
  }

}