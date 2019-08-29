<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Resources\MarketPlaceInformation;

use Mamoot\CardMarket\Resources\HttpCaller;

class GamesResource extends HttpCaller
{
    /**
     * Returns all games supported by MKM and you can sell and buy products for.
     *
     * @return array
     * @throws \Exception
     */
    public function getGamesList(): array
    {
        return $this->get('/games');
    }
}
