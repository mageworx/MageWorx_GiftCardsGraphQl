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
use MageWorx\GiftCards\Helper\Product as Helper;

class ConfigAttributes implements ResolverInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var array
     */
    protected $attributesWithHelperMethods = [
        'amount_display_mode'   => 'getAmountDisplayMode',
        'amount_placeholder'    => 'getAmountPlaceholder',
        'from_name_placeholder' => 'getFromNamePlaceholder',
        'to_name_placeholder'   => 'getToNamePlaceholder',
        'to_email_placeholder'  => 'getToEmailPlaceholder',
        'message_placeholder'   => 'getMessagePlaceholder'
    ];

    /**
     * ConfigAttributes constructor.
     *
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
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

        if ($product->getTypeId() === \MageWorx\GiftCards\Model\Product\Type\GiftCards::TYPE_CODE) {
            if (in_array($field->getName(), array_keys($this->attributesWithHelperMethods))) {
                $method = $this->attributesWithHelperMethods[$field->getName()];
                $data   = $this->helper->{$method}();
            }
        }

        return $data;
    }
}
