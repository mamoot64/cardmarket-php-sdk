<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Resources\MarketPlaceInformation;

use Mamoot\CardMarket\Resources\HttpCaller;

class ExpansionsResource extends HttpCaller {

  /**
   * Returns all expansions with single cards for the specified game.
   *
   * @param int $gameId
   *
   * @return array
   * @throws \Exception
   */
  public function getExpansionsListByGame(int $gameId): array
  {
    return $this->get(sprintf('/games/%d/expansions', $gameId));
  }

}