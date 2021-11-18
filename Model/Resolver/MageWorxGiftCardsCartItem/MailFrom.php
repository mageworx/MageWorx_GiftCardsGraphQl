<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model\Resolver\MageWorxGiftCardsCartItem;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item;
use MageWorx\GiftCards\Api\Data\GiftCardsInterface;

class MailFrom implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!(($value['model'] ?? null) instanceof CartItemInterface)) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var Item $cartItem */
        $cartItem = $value['model'];

        return $cartItem->getOptionByCode(GiftCardsInterface::MAIL_FROM)
                        ->getValue();
    }
}
