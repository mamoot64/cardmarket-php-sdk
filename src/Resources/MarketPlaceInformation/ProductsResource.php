<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Resources\MarketPlaceInformation;

use Mamoot\CardMarket\Resources\HttpCaller;

class ProductsResource extends HttpCaller
{

  /**
   * Returns a product specified by its ID.
   *
   * @param int $cardId
   *
   * @return array
   * @throws \Exception
   */
  public function getCardsDetails(int $cardId): array
  {
    return $this->get(sprintf('/products/%d', $cardId));
  }

}