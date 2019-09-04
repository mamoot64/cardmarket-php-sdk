<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Resources\StockManagement;

use Mamoot\CardMarket\Resources\HttpCaller;

/**
 * Class StockInShoppingCartsResource
 *
 * @package Mamoot\CardMarket\Resources\StockManagement
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class StockResource extends HttpCaller
{
    /**
     * Retrieves all articles in the authenticated user's stock.
     *
     * @return array
     * @throws \Exception
     */
    public function getStock(): array
    {
        return $this->get('/stock');
    }
}
