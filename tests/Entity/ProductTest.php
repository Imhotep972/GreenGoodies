<?php

namespace App\Tests\Entity;

use App\Entity\OrderLine;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{

    public function testInitNewProduct()
    {
        $product = new Product();

        // test recuperation orderlines (doit etre vide)
        $this->assertInstanceOf(Collection::class,$product->getOrderLines(),'GetOrderLines Class Collection');
        $this->assertInstanceOf(ArrayCollection::class,$product->getOrderLines(),'GetOrderLines Class ArrayCollection');
        $this->assertCount(0,$product->getOrderLines(),'GetOrderLines doit retourner 0, orderline est vide');

    }

    public function testAddName()
    {
        $product = new Product();
        $name = 'Produit 1';
        $product->setName($name);
        $this->assertEquals('Produit 1',$product->getName(),'GetName');

        // test name est null
        $this->expectException(\TypeError::class);
        $product->setName(null);
        $this->assertEquals(null,$product->getName(),'GetName Null');

        // test name est vide
        $this->expectException(\InvalidArgumentException::class);
        $product->setName('');
        $this->assertEquals('',$product->getName(),'GetName vide');
    }

    public function testPrice()
    {
        $product = new Product();

        // test price > 0
        $this->expectException(\TypeError::class);
        $product->setPrice('1250');
        $this->assertSame(1250,$product->getPrice(),'GetPrice 1250');

        // test price = 0
        $product->setPrice(0);
        $this->assertSame(0,$product->getPrice(),'GetPrice 0');

        // test price < 0
        $this->expectException(\InvalidArgumentException::class);
        $product->setPrice(-1250);
        $this->assertSame(-12500,$product->getPrice(),'GetPrice -1250');
    }

    public function testShortDescription()
    {
        $product = new Product();
        $shortDesc = "Captis aptae tractibus captis calentes aptae quoque sed has natura.";

        $product->setShortdesc($shortDesc);
        $this->assertEquals("Captis aptae tractibus captis calentes aptae quoque sed has natura.",$product->getShortdesc(),'GetShortDesc');

        // test name est vide
        $this->expectException(\InvalidArgumentException::class);
        $product->setShortdesc('');
        $this->assertEquals('',$product->getShortdesc(),'GetShortDesc Vide');

        // test name est null
        $this->expectException(\TypeError::class);
        $product->setShortdesc(null);
        $this->assertEquals(null,$product->getShortdesc(),'GetShortDesc Null');
    }

    public function testPhoto()
    {
        $product = new Product();
        $photo = "product_1.webp";

        $product->setPhoto($photo);
        $this->assertEquals("product_1.webp",$product->getPhoto(),'GetPhoto');

        // test photo est vide
        $this->expectException(\InvalidArgumentException::class);
        $product->setPhoto('');
        $this->assertEquals('',$product->getPhoto(),'GetPhoto Vide');

        // test name est null
        $this->expectException(\TypeError::class);
        $product->setPhoto(null);
        $this->assertEquals(null,$product->getPhoto(),'GetPhoto Null');
    }

    public function testDescription()
    {
        $product = new Product();
        $description = "Quae ut honestissime enim in se quae in invehi acerbius de faceremus indigno fruantur in. Seditionum ne perlato tumor insidiarum dilato sunt diligens et eum acueret perferens cogitabatur est Eusebius.Aut metuat quo intellexisse quem iam eum aut tum enim.";

        $product->setDescription($description);
        $this->assertEquals("Quae ut honestissime enim in se quae in invehi acerbius de faceremus indigno fruantur in. Seditionum ne perlato tumor insidiarum dilato sunt diligens et eum acueret perferens cogitabatur est Eusebius.Aut metuat quo intellexisse quem iam eum aut tum enim.",$product->getDescription(),'GetDescription');

        // test name est vide
        $this->expectException(\InvalidArgumentException::class);
        $product->setDescription('');
        $this->assertEquals('',$product->getDescription(),'GetDescription Desc Vide');

        // test name est null
        $this->expectException(\TypeError::class);
        $product->setDescription(null);
        $this->assertEquals(null,$product->getDescription(),'GetDescription Desc Null');
    }

    public function testAddOrderLine()
    {
        $product = new Product();
        $orderline = new OrderLine();

        // test ajout underline
        $product->addOrderLine($orderline);
        $this->assertInstanceOf(Collection::class,$product->getOrderLines(),'GetOrderLines Class Collection');
        $this->assertInstanceOf(ArrayCollection::class,$product->getOrderLines(),'GetOrderLines Class ArrayCollection');
        $this->assertCount(1,$product->getOrderLines(),'GetOrderLines doit retourner 1');

        // test unicité orderline
        $product->addOrderLine($orderline);
        $this->assertCount(1,$product->getOrderLines(),'GetOrderLines doit retourner 1');

    }

    public function testRemoveOrderLine()
    {
        $product = new Product();
        $orderline = new OrderLine();

        // test ajout underline
        $product->addOrderLine($orderline);
        $this->assertInstanceOf(Collection::class,$product->getOrderLines(),'GetOrderLines Class Collection');
        $this->assertInstanceOf(ArrayCollection::class,$product->getOrderLines(),'GetOrderLines Class ArrayCollection');
        $this->assertCount(1,$product->getOrderLines(),'GetOrderLines doit retourner 1');

        // test unicité orderline
        $product->removeOrderLine($orderline);
        $this->assertCount(0,$product->getOrderLines(),'GetOrderLines doit retourner 0');

    }
}