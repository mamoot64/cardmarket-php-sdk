<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Resources\OrdersManagement;

use Mamoot\CardMarket\Resources\HttpCaller;

/**
 * Class OrdersResource
 *
 * @package Mamoot\CardMarket\Resources\OrdersResource
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class OrdersResource extends HttpCaller
{
    public const ORDER_SELLER = "seller";
    public const ORDER_BUYER = "buyer";

    public const ORDER_STATE_BOUGHT = "bought";
    public const ORDER_STATE_PAID = "paid";
    public const ORDER_STATE_SENT = "sent";
    public const ORDER_STATE_RECEIVED = "received";
    public const ORDER_STATE_LOST = "lost";
    public const ORDER_STATE_CANCELLED = "cancelled";

    /**
     * Rerieve all filtered orders.
     *
     * @param string $actor
     * @param string $state
     * @param int $start
     *
     * @return array
     */
    public function getOrders(string $actor, string $state, int $start = 1)
    {
        return $this->get(sprintf('/orders/%s/%s/%d', $actor, $state, $start));
    }

    /**
     * Returns all send orders for the current seller.
     *
     * @return array
     * @throws \Exception
     */
    public function getSentOrders(): array
    {
        return $this->get(sprintf('/orders/%s/%s', self::ORDER_SELLER, self::ORDER_STATE_SENT));
    }

    /**
     * Returns all received orders for the current seller.
     *
     * @param int $start
     *
     * @return array
     * @throws \Exception
     */
    public function getReceivedOrders(int $start = 1): array
    {
        return $this->get(sprintf('/orders/%s/%s/%d', self::ORDER_SELLER, self::ORDER_STATE_RECEIVED, $start));
    }

}
