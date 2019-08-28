<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Exception;

use Mamoot\CardMarket\CardMarketException;

/**
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class HttpClientNotConfiguredException extends \RuntimeException implements CardMarketException
{

  public function __construct(
    string $message = "",
    int $code = 0,
    \Throwable $previous = NULL
  ) {
    parent::__construct("You need to provide \"access_secret\", \"access_token\", \"app_secret\" and \"app_token\" to create a correct HttpClient.", $code, $previous);
  }

}
