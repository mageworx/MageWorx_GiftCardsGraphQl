<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\GiftCardsGraphQl\Model\Resolver;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\GiftCards\Helper\Price as HelperPrice;
use MageWorx\GiftCards\Model\ResourceModel\GiftCards\CollectionFactory as GiftCardCollectionFactory;

class CustomerGiftCards implements ResolverInterface
{
    protected GiftCardCollectionFactory   $giftCardCollectionFactory;
    protected CustomerRepositoryInterface $customerRepository;
    protected HelperPrice                 $helperPrice;
    protected PriceCurrencyInterface      $priceCurrency;

    public function __construct(
        GiftCardCollectionFactory   $giftCardCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        HelperPrice                 $helperPrice,
        PriceCurrencyInterface      $priceCurrency
    ) {
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->customerRepository        = $customerRepository;
        $this->helperPrice               = $helperPrice;
        $this->priceCurrency             = $priceCurrency;
    }

    public function resolve(
        $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $userId = $context->getUserId();
        if (!$userId) {
            throw new LocalizedException(__('Customer is not authenticated'));
        }

        try {
            $customer = $this->customerRepository->getById($userId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new LocalizedException(__('Customer not found.'));
        }

        $store = $context->getExtensionAttributes()->getStore();

        $email = $customer->getEmail();

        $collection = $this->giftCardCollectionFactory->create()
                                                      ->addFieldToFilter('mail_to_email', $email)
                                                      ->setOrder('created_time', 'desc');

        $result = [];

        /** @var \MageWorx\GiftCards\Model\GiftCards $card */
        foreach ($collection as $card) {
            $cardBalance = $this->helperPrice->convertCardCurrencyToStoreCurrency(
                $card->getCardBalance(),
                $store,
                $card->getCardCurrency()
            );
            $result[]    = [
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

        return $result;
    }
}
