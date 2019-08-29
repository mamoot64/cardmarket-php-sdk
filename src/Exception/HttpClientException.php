<?php
use Mamoot\Cardmarket\CardMarketException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpClientException
 *
 * https://api.cardmarket.com/ws/documentation/API_2.0:Response_Codes
 */
final class HttpClientException extends \RuntimeException implements CardMarketException
{
    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @var array
     */
    private $responseBody = [];

    /**
     * @var int
     */
    private $responseCode;

    public function __construct(string $message, int $code, ResponseInterface $response)
    {
        parent::__construct($message, $code);

        $this->response = $response;
        $this->responseCode = $response->getStatusCode();
        $body = $response->getBody()->__toString();

        if (0 !== strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            $this->responseBody['message'] = $body;
        } else {
            $this->responseBody = json_decode($body, true);
        }
    }

    public static function badRequest(ResponseInterface $response)
    {
        $body = $response->getBody()->__toString();

        if (0 !== strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            $validationMessage = $body;
        } else {
            $jsonDecoded = json_decode($body, true);
            $validationMessage = $jsonDecoded['message'] ?? $body;
        }

        $message = sprintf("The parameters passed to the API were invalid. Check your inputs!\n\n%s", $validationMessage);

        return new self($message, 400, $response);
    }

    public static function unauthorized(ResponseInterface $response)
    {
        return new self('Authentication or authorization fails during your request, e.g. your Authorization (signature) is not correct.', 401, $response);
    }

    public static function forbidden(ResponseInterface $response)
    {
        return new self('You try to access a forbidden resource. Check your Authorization Header.', 403, $response);
    }

    public static function notFound(ResponseInterface $response)
    {
        return new self('The endpoint you have tried to access does not exist. Check if the domain matches the domain you have configure on Mailgun.', 404, $response);
    }

    public static function tooManyRequests(ResponseInterface $response)
    {
        return new self('You rich your maximum calls per day.', 429, $response);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getResponseBody(): array
    {
        return $this->responseBody;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }
}
