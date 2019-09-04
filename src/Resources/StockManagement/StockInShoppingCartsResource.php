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
final class StockInShoppingCartsResource extends HttpCaller
{
    /**
     * Returns the Article entities of the authenticated user's stock that are
     * currently in other user's shopping carts.
     *
     * @return array
     * @throws \Exception
     */
    public function getArticlesListInUsersShoppingCarts(): array
    {
        return $this->get('/stock/shoppingcart-articles');
    }
}
