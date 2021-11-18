<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;
use MageWorx\GiftCards\Model\Product\Type\GiftCards;

class GiftCardProductTypeResolver implements TypeResolverInterface
{
    const GIFTCARDS_PRODUCT = 'MageWorxGiftCards';

    /**
     * @param array $data
     * @return string
     */
    public function resolveType(array $data) : string
    {
        if (isset($data['type_id']) && $data['type_id'] == GiftCards::TYPE_CODE) {
            return self::GIFTCARDS_PRODUCT;
        }
        return '';
    }
}
