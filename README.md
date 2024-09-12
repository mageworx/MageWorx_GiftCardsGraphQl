# MageWorx_GiftCardsGraphQl

GraphQL API module for Mageworx [Magento 2 Gift Cards](https://www.mageworx.com/magento-2-gift-cards.html) extension.

## Installation

**1) Installation using composer (from packagist)**
- Execute the following command: `composer require mageworx/module-giftcards-graphql`

**2) Copy-to-paste method**
- Download this module and upload it to the `app/code/MageWorx/GiftCardsGraphQl` directory *(create "GiftCardsGraphQl" first if missing)*

## How to use

**1.** The **mwGiftCardInfo** query returns the information about the Gift Cards.

Query attribute is defined below:

```
code: String! Gift Card code
```

By default you can use the following attributes:

```
status: String @doc(description: "Status")
balance: MwGiftCardBalance @doc(description: "Current Balance")
valid_till: String @doc(description: "Valid till")
```

MwGiftCardBalance object:

```
value: Float @doc(description: "Balance Value")
currency_code: String @doc(description: "A three-letter currency code, such as USD or EUR")
label: String @doc(description: "Balance Label")
```

**Request:**

```
{
    mwGiftCardInfo(code: "N8Q48-UWIII-8YGZ7") {
        status
        valid_till
        balance {
            value
            currency_code
            label
        }
    }
}
```

**Response:**

```json
{
    "data": {
        "mwGiftCardInfo": {
            "status": "Active",
            "valid_till": "Unlimited",
            "balance": {
                "value": 13.4,
                "currency_code": "USD",
                "label": "$13.40"
            }
        }
    }
}
```

**2.** The **applyMwGiftCardToCart** mutation allows you to add any Gift Card to the cart.

Syntax:
```
mutation: {applyMwGiftCardToCart(input: ApplyMwGiftCardToCartInput): ApplyMwGiftCardToCartOutput}
```

The ApplyMwGiftCardToCartInput object must contain the following attributes:
```
cart_id: String! @doc(description:"The unique ID that identifies the customer's cart")
gift_card_code: String! @doc(description: "Gift Card code")
```

The ApplyMwGiftCardToCartOutput object contains the Cart object.
```
cart: Cart! @doc(description: "Describes the contents of the specified shopping cart")
```

Cart object:
```
applied_mw_gift_cards: [AppliedMwGiftCards] @doc(description:"An array of applied Gift Cards")
```

AppliedMwGiftCards object
```
code: String @doc(description: "Gift Card code")
remaining: MwGiftCardBalance @doc(description: "Remaining balance")
applied: MwGiftCardBalance @doc(description: "Applied balance to the current cart")
```

MwGiftCardBalance object
```
value: Float @doc(description: "Balance Value")
currency_code: String @doc(description: "A three-letter currency code, such as USD or EUR")
label: String @doc(description: "Balance Label")
```

**Request:**

```
mutation {
    applyMwGiftCardToCart(
        input: {
            cart_id: "W0UKfmRGFp62p3H47MFfVupsnq1GFUxv"
            gift_card_code: "N8Q48-UWIII-8YGZ7"
        }
    ) {
        cart {
            applied_mw_gift_cards {
                code
                remaining {
                    value
                    currency_code
                    label
                }
                applied {
                    value
                    currency_code
                    label
                }
            }
        }
    }
}
```

**Response:**

```json
{
    "data": {
        "applyMwGiftCardToCart": {
            "cart": {
                "applied_mw_gift_cards": [
                    {
                        "code": "N8Q48-UWIII-8YGZ7",
                        "remaining": {
                            "value": 0,
                            "currency_code": "USD",
                            "label": "$0.00"
                        },
                        "applied": {
                            "value": 13.4,
                            "currency_code": "USD",
                            "label": "$13.40"
                        }
                    }
                ]
            }
        }
    }
}
```

**3.** The **removeMwGiftCardFromCart** mutation allows you to remove Gift Card in the cart.

Syntax:
```
mutation: {removeMwGiftCardFromCart(input: RemoveMwGiftCardToCartInput): RemoveMwGiftCardFromCartOutput}
```

The RemoveMwGiftCardToCartInput object must contain the following attributes:
```
cart_id: String! @doc(description:"The unique ID that identifies the customer's cart")
gift_card_code: String! @doc(description: "Gift Card code")
```

The RemoveMwGiftCardFromCartOutput object contains the Cart object.
```
cart: Cart! @doc(description: "Describes the contents of the specified shopping cart")
```

Cart object:
```
applied_mw_gift_cards: [AppliedMwGiftCards] @doc(description:"An array of applied Gift Cards")
```

AppliedMwGiftCards object
```
code: String @doc(description: "Gift Card code")
remaining: MwGiftCardBalance @doc(description: "Remaining balance")
applied: MwGiftCardBalance @doc(description: "Applied balance to the current cart") 
```

MwGiftCardBalance object
```
value: Float @doc(description: "Balance Value")
currency_code: String @doc(description: "A three-letter currency code, such as USD or EUR")
label: String @doc(description: "Balance Label")
```

**Request:**

```
mutation {
    removeMwGiftCardFromCart(
        input: {
            cart_id: "W0UKfmRGFp62p3H47MFfVupsnq1GFUxv"
            gift_card_code: "N8Q48-UWIII-8YGZ7"
        }
    ) {
        cart {
            applied_mw_gift_cards {
                code
                remaining {
                    value
                    currency_code
                    label
                }
                applied {
                    value
                    currency_code
                    label
                }
            }
        }
    }
}
```

**Response:**

```json
{
    "data": {
        "removeMwGiftCardFromCart": {
            "cart": {
                "applied_mw_gift_cards": null
            }
        }
    }
}
```

**4.** **Gift Card product**

This request allows you to retrieve the information about Mageworx Gift card product.

MageWorxGiftCards implements ProductInterface. The MageWorxGiftCards object contains the following attributes:
```
mageworx_gc_type: MageWorxGiftCardTypeEnum @doc(description: "Either EMAIL, PRINT, or OFFLINE") 
mageworx_gc_additional_price: [MageWorxGiftCardAdditionalPrice] @doc(description: "Additional Prices")
mageworx_gc_customer_groups: [MageWorxGiftCardCustomerGroup] @doc(description: "Available for Customer Groups") 
mageworx_gc_allow_open_amount: Boolean @doc(description: "Allow Open Amount")
mageworx_gc_open_amount_min: Float @doc(description: "Open Amount Min Value") 
mageworx_gc_open_amount_max: Float @doc(description: "Open Amount Max Value") 
amount_display_mode: Int @doc(description: "Amount Display Mode") amount_placeholder: String @doc(description: "Custom Amount Placeholder") from_name_placeholder: String @doc(description: "From Name Placeholder") to_name_placeholder: String @doc(description: "To Name Placeholder") to_email_placeholder: String @doc(description: "To Email Placeholder") message_placeholder: String @doc(description: "Message Placeholder") 
```

MageWorxGiftCardAdditionalPrice attributes
```
value: Float @doc(description: "Additional Price")
label: String @doc(description: "Additional Price Label")
```

MageWorxGiftCardCustomerGroup attribute
```
id: Int @doc(description: "Customer Group ID")
```

**Request:**

```
{
  products(filter: { sku: { eq: "MWgift" } }) {
    items {
      name
      ... on MageWorxGiftCards {
      mageworx_gc_type
      mageworx_gc_additional_price 
      {
        value
        label
      }
        mageworx_gc_customer_groups
      {
        id
      }
      mageworx_gc_allow_open_amount
      mageworx_gc_open_amount_min
      mageworx_gc_open_amount_max
      amount_display_mode
      amount_placeholder
      from_name_placeholder
      to_name_placeholder
      to_email_placeholder
      message_placeholder
      
    }
    }
  }
}
```

**Response:**

```json
{
  "data": {
    "products": {
      "items": [
        {
          "name": "MWgift",
          "mageworx_gc_type": "EMAIL",
          "mageworx_gc_additional_price": [
            {
              "value": 30,
              "label": "$30.00"
            }
          ],
          "mageworx_gc_customer_groups": [
            {
              "id": 0
            },
            {
              "id": 1
            }
          ],
          "mageworx_gc_allow_open_amount": false,
          "mageworx_gc_open_amount_min": 20,
          "mageworx_gc_open_amount_max": 30,
          "amount_display_mode": 0,
          "amount_placeholder": "Test amount",
          "from_name_placeholder": "Test \"From Name\" Placeholder",
          "to_name_placeholder": "Test \"To Name\" Placeholder",
          "to_email_placeholder": "Test \"To Email\" Placeholder",
          "message_placeholder": "Test message"
        }
      ]
    }
  }
}
```

**5.** **MageWorxGiftCardsCartItem implementation**

The MageWorxGiftCardsCartItem object implements CartItemInterface. The MageWorxGiftCardsCartItem object contains the following attributes:

```
mail_from: String @doc(description: "From Name")
mail_to: String @doc(description: "To Name") 
mail_to_email: String @doc(description: "To E-mail") 
mail_message: String @doc(description: "Message") 
mail_delivery_date: String @doc(description: "Delivery Date") 
customizable_options: [SelectedCustomizableOption] 
```

**Request:**

```
{
  cart(cart_id: "kPi7RAFpz6qNJMEYDmjwXenMWvj5NqSz") {
      items {
        id
        product {
            name
            sku
        }
        quantity
        prices {
            price {
                value
                currency
            }
        }
        ... on MageWorxGiftCardsCartItem {
        mail_from,
        mail_to,
        mail_to_email,
        mail_message,
        mail_delivery_date
        }
    }    
  }
}
```

**Response:**

``` json
{
    "data": {
        "cart": {
            "items": [
                {
                    "id": "122",
                    "product": {
                        "name": "Fusion Backpack",
                        "sku": "24-MB02"
                    },
                    "quantity": 4,
                    "prices": {
                        "price": {
                            "value": 59,
                            "currency": "USD"
                        }
                    }
                },
                {
                    "id": "123",
                    "product": {
                        "name": "Strive Shoulder Pack",
                        "sku": "24-MB04"
                    },
                    "quantity": 1,
                    "prices": {
                        "price": {
                            "value": 32,
                            "currency": "USD"
                        }
                    }
                },
                {
                    "id": "126",
                    "product": {
                        "name": "MW Gift mail",
                        "sku": "MW-Gift-mail"
                    },
                    "quantity": 4,
                    "prices": {
                        "price": {
                            "value": 5,
                            "currency": "USD"
                        }
                    },
                    "mail_from": "John",
                    "mail_to": "Doe",
                    "mail_to_email": "j.doe@mageworx.com",
                    "mail_message": "Hello, John!!!",
                    "mail_delivery_date": "Jul 8, 2020"
                }
            ]
        }
    }
}
```

**6.** The **addGiftCardProductsToCart** mutation allows you to add any Gift Card Product to the cart.

Syntax:

```
mutation: {addGiftCardProductsToCart(input: AddGiftCardProductsToCartInput): AddGiftCardProductsToCartOutput}
```

The AddGiftCardProductsToCartInput object must contain the following attributes:

```
cart_id: String!
cart_items: [GiftCardProductCartItemInput!]!
```

GiftCardProductCartItemInput object:

```
data: CartItemInput!
gift_card_product_options: GiftCardProductOptionsInput
customizable_options:[CustomizableOptionInput!]
```

GiftCardProductOptionsInput object:

```
card_amount: String! @doc(description: "Card Value")
card_amount_other: Float @doc(description: "Other Card Value")
mail_from: String @doc(description: "From Name")
mail_to: String @doc(description: "To Name")
mail_to_email: String @doc(description: "To E-mail")
mail_message: String @doc(description: "Message")
mail_delivery_date_user_value: String @doc(description: "Delivery Date")
```

The AddGiftCardProductsToCartOutput object contains the Cart object:

```
cart: Cart!
```

**Request:**

```
mutation {
    addGiftCardProductsToCart( 
        input: {
            cart_id: "kPi7RAFpz6qNJMEYDmjwXenMWvj5NqSz"
            cart_items: [
                {
                    data: {
                        quantity: 1
                        sku: "MW-Gift-mail"
                    },
                    gift_card_product_options: 
                    {
                        card_amount: "other_amount"
                        card_amount_other: 5
                        mail_from: "John"
                        mail_to: "Doe"
                        mail_to_email: "j.doe@mageworx.com"
                        mail_message: "Hello, John!!!"
                        mail_delivery_date_user_value: "07/08/2020"
                    }           
                }
            ]
      }
  ) {
    cart {
        items {
            product {
                name
                sku
            }
            quantity
            prices {
                price {
                    value
                    currency
                }
            }
            ... on MageWorxGiftCardsCartItem {
            mail_from,
            mail_to,
            mail_to_email,
            mail_message,
            mail_delivery_date
            }
        }
    }
}
}
```

**Response:**

``` json
{
    "data": {
        "addGiftCardProductsToCart": {
            "cart": {
                "items": [
                    {
                        "product": {
                            "name": "Strive Shoulder Pack",
                            "sku": "24-MB04"
                        },
                        "quantity": 1,
                        "prices": {
                            "price": {
                                "value": 32,
                                "currency": "USD"
                            }
                        }
                    },
                    {
                        "product": {
                            "name": "MW Gift mail",
                            "sku": "MW-Gift-mail"
                        },
                        "quantity": 4,
                        "prices": {
                            "price": {
                                "value": 5,
                                "currency": "USD"
                            }
                        },
                        "mail_from": "John",
                        "mail_to": "Doe",
                        "mail_to_email": "j.doe@mageworx.com",
                        "mail_message": "Hello, John!!!",
                        "mail_delivery_date": "Jul 8, 2020"
                    }
                ]
            }
        }
    }
}
```

**7.** The **addProductsToCart** mutation allows you to add any product to the cart.

Syntax:

```
mutation: {addProductsToCart(cartId: String! cartItems: [cartItemInput]!): AddProductsToCartOutput}
```

The cartItemInput object must contain the following attributes:

```
entered_options: [{uid: ID!, value: String!}]
quantity: Float!
sku: String!
```

**entered_options**

You need to add gift card product cart item attributes array here, as uid and value.

**uid**

Base64 encoded cart item attribute starting with 'mw_giftcard/' prefix. For example:

```
mw_giftcard/mail_to_email
```

Base64 encoded uid
```
Z2lmdGNhcmQvbWFpbF90b19lbWFpbA==
```