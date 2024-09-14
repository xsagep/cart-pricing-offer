<?php

declare(strict_types=1);

// Interface for Offers (Strategy Pattern)
interface OfferStrategy {
    public function apply(array $products, float $subtotal): float;
}

// Interface for Delivery (Strategy Pattern)
interface DeliveryStrategy {
    public function calculateDelivery(float $subtotal): float;
}

// A specific offer: Buy one red widget, get the second half-price
class BuyOneGetHalfOff implements OfferStrategy {
    private array $eligibleProducts; // Map of product code => price

    public function __construct(array $eligibleProducts) {
        $this->eligibleProducts = $eligibleProducts;
    }

    public function apply(array $products, float $subtotal): float {
        $discount = 0;

        foreach ($this->eligibleProducts as $productCode => $price) {
            $count = array_count_values($products)[$productCode] ?? 0;
            $discount += intdiv($count, 2) * ($price / 2);
        }

        return $subtotal - $discount;
    }
}

// Delivery rule based on subtotal
class StandardDelivery implements DeliveryStrategy {
    private float $freeDeliveryThreshold;
    private float $minSpend;
    private float $underMinCost;
    private float $standardCost;

    public function __construct(
        float $freeDeliveryThreshold,
        float $minSpend,
        float $underMinCost,
        float $standardCost
    ) {
        $this->freeDeliveryThreshold = $freeDeliveryThreshold;
        $this->minSpend = $minSpend;
        $this->underMinCost = $underMinCost;
        $this->standardCost = $standardCost;
    }

    public function calculateDelivery(float $subtotal): float {
        if ($subtotal >= $this->freeDeliveryThreshold) {
            return 0.0;
        } elseif ($subtotal < $this->minSpend) {
            return $this->underMinCost;
        } else {
            return $this->standardCost;
        }
    }
}

// Product catalog
class ProductCatalog {
    private array $catalog;

    public function __construct(array $catalog) {
        $this->catalog = $catalog;
    }

    public function getPrice(string $productCode): float {
        if (!isset($this->catalog[$productCode])) {
            throw new InvalidArgumentException("Product not found: " . $productCode);
        }
        return $this->catalog[$productCode]['price'];
    }
}

// Cart class that accepts strategies for offers and delivery
class Cart {
    private array $products = [];
    private ProductCatalog $catalogue;
    private OfferStrategy $offerStrategy;
    private DeliveryStrategy $deliveryStrategy;

    public function __construct(ProductCatalog $catalogue, OfferStrategy $offerStrategy, DeliveryStrategy $deliveryStrategy) {
        $this->catalogue = $catalogue;
        $this->offerStrategy = $offerStrategy;
        $this->deliveryStrategy = $deliveryStrategy;
    }

    public function add(string $productCode): void {
        $this->catalogue->getPrice($productCode); // Check if the product exists
        $this->products[] = $productCode;
    }

    public function total(): string {
        $subtotal = 0;

        // Calculate the subtotal
        foreach ($this->products as $productCode) {
            $subtotal += $this->catalogue->getPrice($productCode);
        }

        // Apply the offer
        $subtotal = $this->offerStrategy->apply($this->products, $subtotal);

        // Calculate the delivery cost
        $deliveryCost = $this->deliveryStrategy->calculateDelivery($subtotal);

        return numberFormat($subtotal + $deliveryCost, 2);
    }
}

// numberFormat function to prevent rounding issues
function numberFormat(float $number, int $decimals = 2, string $decPoint = '.', string $thousandsSep = ','): string {
    $negation = ($number < 0) ? (-1) : 1;
    $coefficient = 10 ** $decimals;
    // abs($number) artık float dönecek
    $number = $negation * floor(abs($number) * $coefficient) / $coefficient;
    return number_format($number, $decimals, $decPoint, $thousandsSep);
}

// Product catalogue
$catalogue = new ProductCatalog([
    'R01' => ['name' => 'Red Widget', 'price' => 32.95],
    'G01' => ['name' => 'Green Widget', 'price' => 24.95],
    'B01' => ['name' => 'Blue Widget', 'price' => 7.95]
]);

// Create strategies
$offerStrategy = new BuyOneGetHalfOff(['R01' => 32.95]);
$deliveryStrategy = new StandardDelivery(90, 50, 4.95, 2.95);

// Test case 1: B01 + G01 = $37.85
$cart = new Cart($catalogue, $offerStrategy, $deliveryStrategy);
$cart->add('B01');
$cart->add('G01');
echo "Total: $" . $cart->total() . PHP_EOL; // Expected: 37.85

// Test case 2: R01 + R01 = $54.37 (with second red widget half-price)
$cart = new Cart($catalogue, $offerStrategy, $deliveryStrategy);
$cart->add('R01');
$cart->add('R01');
echo "Total: $" . $cart->total() . PHP_EOL; // Expected: 54.37

// Test case 3: R01 + G01 = $60.85 (no discount applies, standard delivery)
$cart = new Cart($catalogue, $offerStrategy, $deliveryStrategy);
$cart->add('R01');
$cart->add('G01');
echo "Total: $" . $cart->total() . PHP_EOL; // Expected: 60.85

// Test case 4: B01 + B01 + R01 + R01 + R01 = $98.27
$cart = new Cart($catalogue, $offerStrategy, $deliveryStrategy);
$cart->add('B01');
$cart->add('B01');
$cart->add('R01');
$cart->add('R01');
$cart->add('R01');
echo "Total: $" . $cart->total() . PHP_EOL; // Expected: 98.27

?>