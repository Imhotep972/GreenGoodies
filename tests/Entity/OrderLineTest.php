<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use App\Entity\OrderLine;

class OrderLineTest extends TestCase
{

    // pas de test , pas de construct

    public function testQuantity()
    {
        $orderline = new OrderLine();

        $orderline->setQuantity(12);
        $this->assertEquals(12,$orderline->getQuantity(),'GetQuantity > 0');

        // test quantity = 0
        $this->expectException(\InvalidArgumentException::class);
        $orderline->setQuantity(0);
        $this->assertEquals(0,$orderline->getQuantity(),'GetQuantity = 0');

        // test quantity = -12
        $this->expectException(\InvalidArgumentException::class);
        $orderline->setQuantity(-12);
        $this->assertEquals(-12,$orderline->getQuantity(),'GetQuantity < 0');

    }

    public function testPrice()
    {
        $orderline = new OrderLine();

        $orderline->setPrice(1200);
        $this->assertEquals(1200,$orderline->getPrice(),'GetPrice > 0');

        // test price = 0
        $orderline->setPrice(0);
        $this->assertEquals(0,$orderline->getPrice(),'GetPrice = 0');

        // test price = -1200
        $this->expectException(\InvalidArgumentException::class);
        $orderline->setQuantity(-1200);
        $this->assertEquals(-1200,$orderline->getQuantity(),'GetPrice < 0');

    }

    public function testOrders()
    {
        $orderline = new OrderLine();
        $order = new Order();

        $orderline->setOrders($order);
        $this->assertSame($order,$orderline->getOrders(),'GetOrders');

    }

    public function testProduct()
    {
        $orderline = new OrderLine();
        $product = new Product();

        $orderline->setProduct($product);
        $this->assertSame($product,$orderline->getProduct(),'GetProduct');

    }
}

    