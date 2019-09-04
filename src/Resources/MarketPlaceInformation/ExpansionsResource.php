<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Resources\MarketPlaceInformation;

use Mamoot\CardMarket\Resources\HttpCaller;

/**
 * Class ExpansionsResource
 *
 * @package Mamoot\CardMarket\Resources\MarketPlaceInformation
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class ExpansionsResource extends HttpCaller
{
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

    /**
     * Returns all single cards for the specified expansion.
     *
     * @param int $expansionId
     *
     * @return array
     * @throws \Exception
     */
    public function getCardsListByExpansion(int $expansionId): array
    {
        return $this->get(sprintf('/expansions/%d/singles', $expansionId));
    }
}
