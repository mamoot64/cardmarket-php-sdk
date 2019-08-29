<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Tests\HttpClient;

use Mamoot\CardMarket\Authentication\AuthenticationHeaderBuilder;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use PHPUnit\Framework\TestCase;

final class AuthenticationHeaderBuilderTest extends TestCase
{
    private $authenticationHeaderBuilderWithQueryParams;

    private $authenticationHeaderBuilderWithoutQueryParams;

    protected function setUp(): void
    {
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
        $this->assertSame('OAuth realm="https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles", oauth_consumer_key="app_token", oauth_token="access_token", oauth_nonce="5d676828e6fe7", oauth_timestamp="1567057960", oauth_signature_method="HMAC-SHA1", oauth_version="1.0", start="0", maxResults="10", oauth_signature="95ULTYYDOl+t35olPzaGGymppuE="',
      $this->authenticationHeaderBuilderWithQueryParams->getAuthorisationHeaderValue());

        $this->assertSame('OAuth realm="https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles", oauth_consumer_key="app_token", oauth_token="access_token", oauth_nonce="5d676828e6fe7", oauth_timestamp="1567057960", oauth_signature_method="HMAC-SHA1", oauth_version="1.0", oauth_signature="+EXDfr5yax3WoXLq+NNQgvxpHME="',
      $this->authenticationHeaderBuilderWithoutQueryParams->getAuthorisationHeaderValue());
    }

    private function buildAuthenticationBuilder(HttpClientCreator $httpClientCreator, string $url): AuthenticationHeaderBuilder
    {
        $authenticationHeaderBuilder = new AuthenticationHeaderBuilder($httpClientCreator, $url, 'GET');

        // Use Reflection to set immutable timestamp & nonce
        $reflection = new \ReflectionProperty(AuthenticationHeaderBuilder::class, 'timestamp');
        $reflection->setAccessible(true);
        $reflection->setValue($authenticationHeaderBuilder, '1567057960');

        $reflection = new \ReflectionProperty(AuthenticationHeaderBuilder::class, 'nonce');
        $reflection->setAccessible(true);
        $reflection->setValue($authenticationHeaderBuilder, '5d676828e6fe7');

        return $authenticationHeaderBuilder;
    }
}
