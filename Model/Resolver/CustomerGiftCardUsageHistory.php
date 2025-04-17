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
use MageWorx\GiftCards\Model\ResourceModel\Order\CollectionFactory as GiftCardsOrderCollectionFactory;

class CustomerGiftCardUsageHistory implements ResolverInterface
{
    protected GiftCardsOrderCollectionFactory $orderCollectionFactory;
    protected CustomerRepositoryInterface     $customerRepository;
    protected HelperPrice                     $helperPrice;
    protected PriceCurrencyInterface          $priceCurrency;

    public function __construct(
        GiftCardsOrderCollectionFactory $orderCollectionFactory,
        CustomerRepositoryInterface     $customerRepository,
        HelperPrice                     $helperPrice,
        PriceCurrencyInterface          $priceCurrency
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerRepository     = $customerRepository;
        $this->helperPrice            = $helperPrice;
        $this->priceCurrency          = $priceCurrency;
    }

    public function resolve(
        $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $userId = $context->getUserId();
        if (!$userId) {
            throw new LocalizedException(__('Customer is not authenticated'));
        }

        $customer = $this->customerRepository->getById($userId);
        $email    = $customer->getEmail();
        $store    = $context->getExtensionAttributes()->getStore();

        $collection = $this->orderCollectionFactory->create();

        $collection->addFieldToFilter('discounted', ['notnull' => true])
                   ->join(
                       ['giftcards' => $collection->getTable('mageworx_giftcards_card')],
                       'giftcard_id = giftcards.card_id',
                       'mail_to_email'
                   )->addFieldToFilter('mail_to_email', $email)
                   ->setOrder('created_time', 'desc')
                   ->join(
                       ['orders' => $collection->getTable('sales_order')],
                       'main_table.order_id = orders.entity_id',
                       'increment_id'
                   );

        $result = [];

        foreach ($collection as $item) {
            $amount = $this->helperPrice->convertCardCurrencyToStoreCurrency(
                $item->getDiscounted(),
                $store,
                $item->getBaseCurrencyCode()
            );

            $result[] = [
                'code'               => $item->getCardCode(),
                'order_increment_id' => $item->getIncrementId(),
                'order_id'           => (int)$item->getOrderId(),
                'created_at'         => $item->getCreatedTime(),
                'amount'             => [
                    'value'         => $this->priceCurrency->roundPrice($amount),
                    'currency_code' => $store->getCurrentCurrencyCode(),
                    'label'         => $this->priceCurrency->format(
                        $amount,
                        false,
                        PriceCurrencyInterface::DEFAULT_PRECISION,
                        $store
                    )
                ]
            ];
        }

        return $result;
    }
}
