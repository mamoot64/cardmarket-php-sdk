<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Tests\HttpClient;

use Mamoot\CardMarket\Authentication\AuthenticationHeaderBuilder;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use PHPUnit\Framework\TestCase;

final class AuthenticationHeaderBuilderTest extends TestCase
{

    /**
     * @var HttpClientCreator
     */
    private $httpClientCreator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientCreator = new HttpClientCreator();
        $this->httpClientCreator->setAccessSecret('access_secret')
            ->setAccessToken('access_token')
            ->setApplicationSecret('app_secret')
            ->setApplicationToken('app_token');
    }

    /**
     * @dataProvider authenticationHeaderBuilderProvider
     */
    public function testAuthenticationHeaderBuilder(string $expected, string $url , string $method): void
    {
        $this->assertSame(
            $expected,
            $this->buildAuthenticationBuilder($this->httpClientCreator, $url, $method)->getAuthorisationHeaderValue()
        );
    }

    public function authenticationHeaderBuilderProvider(): array
    {
        return [
            'with query params' => [
                'OAuth realm="https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles", oauth_consumer_key="app_token", oauth_token="access_token", oauth_nonce="5d676828e6fe7", oauth_timestamp="1567057960", oauth_signature_method="HMAC-SHA1", oauth_version="1.0", start="0", maxResults="10", oauth_signature="95ULTYYDOl+t35olPzaGGymppuE="',
                HttpClientCreator::API_URL . '/users/karmacrow/articles?start=0&maxResults=10',
                'GET',
            ],
            'with query params and lower case method' => [
                'OAuth realm="https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles", oauth_consumer_key="app_token", oauth_token="access_token", oauth_nonce="5d676828e6fe7", oauth_timestamp="1567057960", oauth_signature_method="HMAC-SHA1", oauth_version="1.0", start="0", maxResults="10", oauth_signature="95ULTYYDOl+t35olPzaGGymppuE="',
                HttpClientCreator::API_URL . '/users/karmacrow/articles?start=0&maxResults=10',
                'get',
            ],
            'without query params' => [
                'OAuth realm="https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles", oauth_consumer_key="app_token", oauth_token="access_token", oauth_nonce="5d676828e6fe7", oauth_timestamp="1567057960", oauth_signature_method="HMAC-SHA1", oauth_version="1.0", oauth_signature="+EXDfr5yax3WoXLq+NNQgvxpHME="',
                HttpClientCreator::API_URL . '/users/karmacrow/articles',
                'GET',
            ],
        ];
    }

    public function testAuthenticationHeaderBuilderWithMalformedUrlShouldFail(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('String "https://///example.com" is malformed and can\'t be parsed.');

        new AuthenticationHeaderBuilder($this->httpClientCreator, 'https://///example.com');
    }

    private function buildAuthenticationBuilder(HttpClientCreator $httpClientCreator, string $url, string $method): AuthenticationHeaderBuilder
    {
        $authenticationHeaderBuilder = new AuthenticationHeaderBuilder($httpClientCreator, $url, $method);

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
