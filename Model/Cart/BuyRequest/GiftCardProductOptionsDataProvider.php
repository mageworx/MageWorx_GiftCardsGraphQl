<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model\Cart\BuyRequest;

use Magento\QuoteGraphQl\Model\Cart\BuyRequest\BuyRequestDataProviderInterface;

class GiftCardProductOptionsDataProvider implements BuyRequestDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public function execute(array $cartItemData): array
    {
        return isset($cartItemData['gift_card_product_options']) ? $cartItemData['gift_card_product_options'] : [];
    }
}
