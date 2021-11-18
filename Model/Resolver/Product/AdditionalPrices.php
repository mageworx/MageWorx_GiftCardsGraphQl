<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model\Resolver\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class AdditionalPrices implements ResolverInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * AdditionalPrices constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(PriceCurrencyInterface $priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var Product $product */
        $product = $value['model'];
        $data    = null;

        if ($product->getTypeId() === \MageWorx\GiftCards\Model\Product\Type\GiftCards::TYPE_CODE
            && $product->getData('mageworx_gc_additional_price')
        ) {
            $data = [];

            foreach (explode(';', (string)$product->getData('mageworx_gc_additional_price')) as $additionalPrice) {
                $data[] = [
                    'value' => $additionalPrice,
                    'label' => $this->priceCurrency->convertAndFormat((float)$additionalPrice, false)
                ];
            }
        }

        return empty($data) ? null : $data;
    }
}
