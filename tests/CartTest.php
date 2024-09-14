<?php

require_once 'CartPricingOffer.php'; // Gerekli dosyayı dahil edin

use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private $catalogue;
    private $offerStrategy;
    private $deliveryStrategy;

    protected function setUp(): void
    {
        // Ürün kataloğu
        $this->catalogue = new ProductCatalog([
            'B01' => ['name' => 'Blue Widget', 'price' => 7.95],
            'G01' => ['name' => 'Green Widget', 'price' => 24.95],
            'R01' => ['name' => 'Red Widget', 'price' => 32.95]
        ]);

        // İndirim ve teslimat stratejilerini başlat
        $this->offerStrategy = new BuyOneGetHalfOff(['R01' => 32.95]);  // Red widget için indirim
        $this->deliveryStrategy = new StandardDelivery(90, 50, 4.95, 2.95);  // Teslimat ücretleri
    }

    // Test case 1: B01 + G01 = $37.85
    public function testCase1()
    {
        $cart = new Cart($this->catalogue, $this->offerStrategy, $this->deliveryStrategy);
        $cart->add('B01');
        $cart->add('G01');
        
        $total = $cart->total();

        $this->assertEquals(37.85, $total, "Total for B01 + G01 should be $37.85");
    }

    // Test case 2: R01 + R01 = $54.37 (second red widget at half price)
    public function testCase2()
    {
        $cart = new Cart($this->catalogue, $this->offerStrategy, $this->deliveryStrategy);
        $cart->add('R01');
        $cart->add('R01');
        
        $total = $cart->total();

        $this->assertEquals(54.37, $total, "Total for R01 + R01 should be $54.37 with half-price on the second item");
    }

    // Test case 3: R01 + G01 = $60.85 (no discount applies, standard delivery)
    public function testCase3()
    {
        $cart = new Cart($this->catalogue, $this->offerStrategy, $this->deliveryStrategy);
        $cart->add('R01');
        $cart->add('G01');
        
        $total = $cart->total();

        $this->assertEquals(60.85, $total, "Total for R01 + G01 should be $60.85 with no discount applied");
    }

    // Test case 4: B01 + B01 + R01 + R01 + R01 = $98.27
    public function testCase4()
    {
        $cart = new Cart($this->catalogue, $this->offerStrategy, $this->deliveryStrategy);
        $cart->add('B01');
        $cart->add('B01');
        $cart->add('R01');
        $cart->add('R01');
        $cart->add('R01');
        
        $total = $cart->total();

        $this->assertEquals(98.27, $total, "Total for B01 + B01 + R01 + R01 + R01 should be $98.27");
    }
}

?>