<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model\Cart\BuyRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Cart\BuyRequest\BuyRequestDataProviderInterface;
use Magento\Quote\Model\Cart\Data\CartItem;

/**
 * DataProvider for building gift card options in buy requests
 */
class GiftCardProductEnteredOptionsDataProvider implements BuyRequestDataProviderInterface
{
    private const OPTION_TYPE = 'mw_giftcard';

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute(CartItem $cartItem): array
    {
        $giftCardProductData = [];
        foreach ($cartItem->getEnteredOptions() as $option) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $optionData = \explode('/', base64_decode($option->getUid()));

            if ($this->isProviderApplicable($optionData) === false) {
                continue;
            }
            $this->validateInput($optionData);

            [$optionType, $optionId] = $optionData;
            if ($optionType == self::OPTION_TYPE) {
                $giftCardProductData[$optionId] = $option->getValue();
            }
        }

        return $giftCardProductData;
    }

    /**
     * Checks whether this provider is applicable for the current option
     *
     * @param array $optionData
     * @return bool
     */
    private function isProviderApplicable(array $optionData): bool
    {
        if ($optionData[0] !== self::OPTION_TYPE) {
            return false;
        }

        return true;
    }

    /**
     * Validates the provided options structure
     *
     * @param array $optionData
     * @throws LocalizedException
     */
    private function validateInput(array $optionData): void
    {
        if (count($optionData) !== 2) {
            throw new LocalizedException(
                __('Wrong format of the entered option data')
            );
        }
    }
}
