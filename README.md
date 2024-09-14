
# Acme Widget Co - Shopping Cart System

## Overview

This project implements a shopping cart system for Acme Widget Co., which sells three types of widgets: Red Widget (R01), Green Widget (G01), and Blue Widget (B01). The cart system includes product catalog management, special offers, and dynamic delivery charge calculations.

### Key Features:
1. **Product Catalogue**: The cart is initialized with a set of products and their respective prices.
2. **Special Offer**: The current offer is "buy one red widget, get the second half-price" (R01).
3. **Dynamic Delivery Charges**: 
   - Orders under $50 are charged $4.95.
   - Orders between $50 and $90 are charged $2.95.
   - Orders of $90 or more have **free delivery**.

## How It Works

### Class Architecture:
- **ProductCatalog**: Manages the list of available products and their prices.
- **OfferStrategy Interface**: Defines a strategy for applying special offers.
  - *BuyOneGetHalfOff*: Applies the "buy one red widget, get the second half-price" offer.
- **DeliveryStrategy Interface**: Defines how delivery costs are calculated.
  - *StandardDelivery*: Implements delivery charges based on the subtotal of the cart.
- **Cart**: The main class that holds the products, applies the special offer and calculates the total cost including delivery.

### Core Methods:
- `add(productCode: string)`: Adds a product to the cart.
- `total(): string`: Returns the total cost of the cart, including offers and delivery charges.

## Example Baskets

Here are some sample baskets and their expected total costs:

| Products              | Total  |
|-----------------------|--------|
| B01, G01              | $37.85 |
| R01, R01              | $54.37 |
| R01, G01              | $60.85 |
| B01, B01, R01, R01, R01 | $98.27 |

These examples help verify the correct behavior of the cart with respect to product pricing, offer applications, and delivery charges.

## Assumptions

1. The initial offer is applicable only for the Red Widget (R01) and discounts are applied automatically when two or more of these items are added to the cart.
2. Delivery charges are based on the total after applying any discounts.
3. Products not included in any current offer are charged at their regular prices.

## How to Run the Code

1. Clone the repository from GitHub.
2. Ensure you have PHP installed (version 8.x or higher is recommended).
3. To run the code:
   - Execute the PHP script directly: `php CartPricingOffer.php`
4. To run unit tests:
   - Use PHPUnit: `vendor/bin/phpunit tests/CartTest.php`

## How to Test

The test suite is implemented using PHPUnit. It contains four test cases to validate the cart’s behavior based on the example baskets mentioned above. Each test case adds specific products to the cart and asserts that the total amount matches the expected value.

Run the tests using the following command:
```
php vendor/bin/phpunit tests/CartTest.php
```

## Installation

1. Clone the repository to your local machine:
   ```
   git clone <repository_url>
   cd cart-pricing-offer
   ```
2. Install dependencies (if applicable):
   ```
   composer install
   ```
3. Run the code or tests as described above.
