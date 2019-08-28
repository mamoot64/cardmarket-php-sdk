<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Tests\HttpClient;

use Mamoot\CardMarket\Authentication\AuthenticationHeaderBuilder;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use PHPUnit\Framework\TestCase;

final class AuthenticationHeaderBuilderTest extends TestCase {

  private $authenticationHeaderBuilderWithQueryParams;

  private $authenticationHeaderBuilderWithoutQueryParams;

  protected function setUp(): void {

    parent::setUp();

    $httpClientCreator = new HttpClientCreator();
    $httpClientCreator->setAccessSecret('access_secret')
      ->setAccessToken('access_token')
      ->setApplicationSecret('app_secret')
      ->setApplicationToken('app_token');

    $this->authenticationHeaderBuilderWithQueryParams = self::buildAuthenticationBuilder(
      $httpClientCreator,
      HttpClientCreator::API_URL . '/users/karmacrow/articles?start=0&maxResults=10'
    );

    $this->authenticationHeaderBuilderWithoutQueryParams = self::buildAuthenticationBuilder(
      $httpClientCreator,
      HttpClientCreator::API_URL . '/users/karmacrow/articles'
    );

  }

  public function testAuthenticationHeaderBuilder()
  {
    $this->assertSame('OAuth maxResults="10", oauth_consumer_key="access_secret", oauth_nonce="5d64e5947f376", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1566893460", oauth_token="access_token", oauth_version="1.0", realm="https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles", start="0", oauth_signature="c2ALPPJmxlHWgU6iegGS7pSTnQA="',
      $this->authenticationHeaderBuilderWithQueryParams->getAuthorisationHeaderValue());

    $this->assertSame('OAuth oauth_consumer_key="access_secret", oauth_nonce="5d64e5947f376", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1566893460", oauth_token="access_token", oauth_version="1.0", realm="https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles", oauth_signature="2IUxWXvDv7/fDjYpKzXC5/fZQUs="',
      $this->authenticationHeaderBuilderWithoutQueryParams->getAuthorisationHeaderValue());
  }


  private function buildAuthenticationBuilder(HttpClientCreator $httpClientCreator, string $url): AuthenticationHeaderBuilder
  {
    $authenticationHeaderBuilder = new AuthenticationHeaderBuilder($httpClientCreator, $url, 'GET');

    // Use Reflection to set immutable timestamp & nonce
    $reflection = new \ReflectionProperty(AuthenticationHeaderBuilder::class, 'timestamp');
    $reflection->setAccessible(TRUE);
    $reflection->setValue($authenticationHeaderBuilder, '1566893460');

    $reflection = new \ReflectionProperty(AuthenticationHeaderBuilder::class, 'nonce');
    $reflection->setAccessible(TRUE);
    $reflection->setValue($authenticationHeaderBuilder, '5d64e5947f376');

    return $authenticationHeaderBuilder;
  }

}