<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\GiftCards\Helper\Price as HelperPrice;
use MageWorx\GiftCards\Model\GiftCardsRepository;

class GiftCardInfo implements ResolverInterface
{
    /**
     * @var GiftCardsRepository
     */
    protected $giftCardsRepository;

    /**
     * @var HelperPrice
     */
    protected $helperPrice;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * GiftCardInfo constructor.
     *
     * @param GiftCardsRepository $giftCardsRepository
     * @param HelperPrice $helperPrice
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        GiftCardsRepository $giftCardsRepository,
        HelperPrice $helperPrice,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->giftCardsRepository = $giftCardsRepository;
        $this->helperPrice         = $helperPrice;
        $this->priceCurrency       = $priceCurrency;
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
        if (empty($args['code'])) {
            throw new GraphQlInputException(__('Required parameter "code" is missing'));
        }

        $code = $args['code'];

        try {
            $card = $this->giftCardsRepository->getByCode($code);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(
                __('Could not find a Gift Card with code "%1"', $code)
            );
        }

        $store       = $context->getExtensionAttributes()->getStore();
        $cardBalance = $this->helperPrice->convertCardCurrencyToStoreCurrency(
            $card->getCardBalance(),
            $store,
            $card->getCardCurrency()
        );

        return [
            'status'     => __($card->getCardStatusLabel()),
            'valid_till' => $card->getExpireDate() ? $card->getExpireDate() : __('Unlimited'),
            'balance'    => [
                'value'         => $this->priceCurrency->roundPrice($cardBalance),
                'currency_code' => $store->getCurrentCurrencyCode(),
                'label'         => $this->priceCurrency->format(
                    $cardBalance,
                    false,
                    PriceCurrencyInterface::DEFAULT_PRECISION,
                    $store
                )
            ],
        ];
    }
}
