<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\GiftCardsGraphQl\Model\Resolver;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use MageWorx\GiftCards\Api\GiftCardManagementInterface;

class ApplyGiftCardToCart implements ResolverInterface
{
    /**
     * @var GetCartForUser
     */
    protected $getCartForUser;

    /**
     * @var GiftCardManagementInterface
     */
    protected $giftCardManagement;

    /**
     * ApplyGiftCardToCart constructor.
     *
     * @param GetCartForUser $getCartForUser
     * @param GiftCardManagementInterface $giftCardManagement
     */
    public function __construct(GetCartForUser $getCartForUser, GiftCardManagementInterface $giftCardManagement)
    {
        $this->getCartForUser = $getCartForUser;
        $this->giftCardManagement = $giftCardManagement;
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
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (empty($args['input']['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }

        if (empty($args['input']['gift_card_code'])) {
            throw new GraphQlInputException(__('Required parameter "gift_card_code" is missing'));
        }

        $maskedCartId = $args['input']['cart_id'];
        $giftCardCode   = $args['input']['gift_card_code'];

        $currentUserId = $context->getUserId();
        $storeId       = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart          = $this->getCartForUser->execute($maskedCartId, $currentUserId, $storeId);
        $cartId        = (int)$cart->getId();

        try {
            $this->giftCardManagement->applyToCart($cartId, $giftCardCode);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (CouldNotSaveException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}
