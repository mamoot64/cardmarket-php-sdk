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
     * @param int $start
     *
     * @return array
     * @throws \Exception
     */
    public function getStock(int $start = 1): array
    {
        return $this->get(sprintf('/stock/%d', $start));
    }

    /**
     * Retrieve the CSV content from your own stock by Game Id.
     *
     * @param int $gameId
     * @param bool $isSealed
     * @param int $idLanguage
     *
     * @return array
     * @throws \Exception
     */
    public function getStockFile(int $gameId, bool $isSealed = false, int $idLanguage = 1): array
    {
        return $this->get(sprintf('/stock/file?idGame=%d&isSealed=%s&idLanguage=%d', $gameId, $isSealed, $idLanguage));
    }

    /**
     * Increase stock for the given article.
     *
     * @param int $articleId
     * @param int $stock
     *
     * @return array
     *      The Article object into array. (https://api.cardmarket.com/ws/documentation/API_2.0:Entities:Article)
     * @throws \Exception
     */
    public function increaseStock(int $articleId, int $stock): array
    {
        return $this->put('/stock/increase', $this->createArticle($articleId, $stock));
    }

    /**
     * Decrease stock for the given article.
     *
     * @param int $articleId
     * @param int $stock
     *
     * @return array
     *      The Article object into array. (https://api.cardmarket.com/ws/documentation/API_2.0:Entities:Article)
     * @throws \Exception
     */
    public function decreaseStock(int $articleId, int $stock): array
    {
        return $this->put('/stock/decrease', $this->createArticle($articleId, $stock));
    }

    /**
     * Dedicated definition of the Article Object to deal with stock management.
     * (https://api.cardmarket.com/ws/documentation/API_2.0:Entities:Article)
     *
     * @param int $articleId
     * @param int $stock
     *
     * @return array
     */
    private function createArticle(int $articleId, int $stock): array
    {
        return [
          "idArticle" => $articleId,
          "amount" => $stock,
        ];
    }
}
