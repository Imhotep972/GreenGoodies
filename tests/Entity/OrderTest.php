<?php

namespace app\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\User;
use App\Enum\OrderStatut;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class UOrderTest extends TestCase
{

    public function testInitNewOrder()
    {
        $order = new Order();

        // test sur la date de creation
        $this->assertNotNull($order->getCreatedAt(),'GetCreatedAt');
        $this->assertInstanceOf(DateTimeImmutable::class,$order->getCreatedAt(),'GetCreatedAt DateTimeImmutable');

        // test sur le statut de order
        $this->assertSame(OrderStatut::Pending,$order->getStatus(),'GetStatut');
        
        // test sur l'initialisation de orderlines
        $this->assertInstanceOf(Collection::class,$order->getOrderLines(),'GetOrderLines Collection');
        $this->assertInstanceOf(ArrayCollection::class,$order->getOrderLines(),'GetOrderLines ArrayCollection');
        $this->assertCount(0,$order->getOrderLines(),'GetOrderLines Count');

    }

    public function testCreatedAt()
    {
        $order = new Order();

        $createdAt = new DateTimeImmutable();

        $order->setCreatedAt($createdAt);
        $this->assertSame($createdAt,$order->getCreatedAt(),'getCreatedAt');

    }

    public function testStatus()
    {
        $order = new Order();

        $status = OrderStatut::Paid;
        $order->setStatus($status);
        $this->assertSame(OrderStatut::Paid,$order->getStatus(),'GetStatus Paid');
        $status = OrderStatut::Deleted;
        $order->setStatus($status);
        $this->assertSame(OrderStatut::Deleted,$order->getStatus(),'GetStatus Deleted');
        $status = OrderStatut::Unpaid;
        $order->setStatus($status);
        $this->assertSame(OrderStatut::Unpaid,$order->getStatus(),'GetStatus Unpaid');
        $status = OrderStatut::Cancelled;
        $order->setStatus($status);
        $this->assertSame(OrderStatut::Cancelled,$order->getStatus(),'GetStatus Cancelled');
        $status = OrderStatut::Delivered;
        $order->setStatus($status);
        $this->assertSame(OrderStatut::Delivered,$order->getStatus(),'GetStatus Delivered');

    }

    public function testAmount()
    {
        $order = new  Order();

        // test amoutn >=0
        $amount = 1052;
        $order->setAmount($amount);
        $this->assertSame(1052,$order->getAmount(),'GetAmount 1052');

        $amount = 0;
        $order->setAmount($amount);
        $this->assertSame(0,$order->getAmount(),'GetAmount 0');

        $amount = -1052;
        $this->expectException(\InvalidArgumentException::class);
        $order->setAmount($amount);
       
    }

    public function testReference()
    {
        $order = new Order();

        $reference = "FA202600520";
        $order->setReference($reference);
        $this->assertSame("FA202600520",$order->getReference(),'getReference');

        $reference = '';
        $this->expectException(\InvalidArgumentException::class);
        $order->setReference($reference);

        $reference = null;
        $this->expectException(\InvalidArgumentException::class);
        $order->setReference($reference);
      
    }

    public function testAddOrderlines()
    {
        $order = new Order();
        $orderline = new OrderLine();

        // on teste si recupere bien la nouvelle ligne de commande dans orderlines
        $order->addOrderLine($orderline);
        $this->assertCount(1, $order->getOrderLines(),'GetOrderLine Count');
        // on teste si recupere bien la commande a partir de la nouvelle ligne de commande dans orderlines
        $this->assertSame($order, $orderline->getOrders(),'GetOrders');
        // test de doublons
        $order->addOrderLine($orderline);
        $this->assertCount(1, $order->getOrderLines(),'GetOrderLine Count');

    }

    public function testRemoveOrderlines()
    {
        $order = new Order();
        $orderline = new OrderLine();

        // on teste si recupere bien la nouvelle ligne de commande dans orderlines
        $order->addOrderLine($orderline);
        $this->assertCount(1, $order->getOrderLines(),'GetOrderLine Count 1');
        // on supprime orderlin
        $order->removeOrderLine($orderline);
        $this->assertCount(0, $order->getOrderLines(),'GetOrderLine Count 0');
        $this->assertNull($orderline->getOrders(),'GetOrders doit etre Null');

    }

    public function testUser()
    {
        $user = new User();
        $order = new Order();

        $order ->setUser($user);
        $this->assertSame($user,$order->getUser(),'getUser');
    }
}