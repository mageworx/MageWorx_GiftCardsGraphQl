<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class AppliedGiftCards implements ResolverInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \MageWorx\GiftCards\Model\Session
     */
    protected $giftCardSession;

    /**
     * AppliedGiftCards constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param \MageWorx\GiftCards\Model\Session $giftCardSession
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        \MageWorx\GiftCards\Model\Session $giftCardSession
    ) {
        $this->priceCurrency   = $priceCurrency;
        $this->giftCardSession = $giftCardSession;
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

        $store            = $context->getExtensionAttributes()->getStore();
        $appliedGiftCards = [];
        $frontOptions     = $this->giftCardSession->getFrontOptions();

        if ($frontOptions && is_array($frontOptions)) {
            foreach ($frontOptions as $key => $option) {
                $appliedGiftCards[] = [
                    'code'      => $option['code'],
                    'remaining' => [
                        'value'         => $this->priceCurrency->roundPrice($option['remaining']),
                        'currency_code' => $store->getCurrentCurrencyCode(),
                        'label'         => $this->priceCurrency->format(
                            $option['remaining'],
                            false,
                            PriceCurrencyInterface::DEFAULT_PRECISION,
                            $store
                        )
                    ],
                    'applied'   => [
                        'value'         => $this->priceCurrency->roundPrice($option['applied']),
                        'currency_code' => $store->getCurrentCurrencyCode(),
                        'label'         => $this->priceCurrency->format(
                            $option['applied'],
                            false,
                            PriceCurrencyInterface::DEFAULT_PRECISION,
                            $store
                        )
                    ]
                ];
            }
        }

        return !empty($appliedGiftCards) ? $appliedGiftCards : null;
    }
}
