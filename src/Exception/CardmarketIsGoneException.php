<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Exception;

use Mamoot\CardMarket\CardMarketException;

/**
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class CardmarketIsGoneException extends \RuntimeException implements CardMarketException
{

  public function __construct(
    string $message = "",
    int $code = 0,
    \Throwable $previous = NULL
  ) {
    parent::__construct("Carmarket API seems to be not available.", $code, $previous);
  }

}
