<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Exception;

use Mamoot\CardMarket\CardMarketException;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class HttpClientException
 *
 * https://api.cardmarket.com/ws/documentation/API_2.0:Response_Codes
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 *
 */
final class HttpClientException extends \RuntimeException implements CardMarketException
{

    /**
     * @var \Symfony\Contracts\HttpClient\ResponseInterface
     */
    private $response;

    public function __construct(string $message, int $code, ResponseInterface $response)
    {
        $this->response = $response;
        parent::__construct($message, $code);
    }

    public static function badRequest(ResponseInterface $response)
    {
        $body = $response->getContent(false);

        if ($response->getHeaders(false)['content-type'][0] !== 'application/json') {
            $message = $body;
        } else {
            $body = json_decode($body, true);
            $message = isset($body['message']) ? $body['message'] : 'Unknown';
        }

        $message = sprintf("The parameters passed to the API were invalid. Check your inputs!\n\n%s", $message);

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
        return new self('The endpoint you have tried to access does not exist.', 404, $response);
    }

    public static function tooManyRequests(ResponseInterface $response)
    {
        return new self('You have reached your maximum calls per day.', 429, $response);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
