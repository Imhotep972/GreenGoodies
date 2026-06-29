<?php
namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testInitNewUser()
    {
        $user = new User();

        // test si le compte n'est pas archivé à la creation
        $this->assertEquals(false, $user->isArchive(),'Archive'); 
        // test si l'acces API est bien desactivé à la création
        $this->assertEquals(false, $user->isApiEnabled(),'ApiEnabled'); 
        // test si l'utilisateur a pour role ROLE_USER à la création
        $this->assertEquals(['ROLE_USER'], $user->getRoles(),'Role');  
        // test si orders est une Collection
        $this->assertInstanceOf(Collection::class, $user->getOrders(),'Collection');          
        // test si orders est une arrayCollection
        $this->assertInstanceOf(ArrayCollection::class, $user->getOrders(),'ArrayCollection');        
        // test si orders n'est pas null
        $this->assertNotNull($user->getOrders());
        // test si orders est vide
        $this->assertCount(0,$user->getOrders());

    }

    public function testEmailGetterSetter()
    {
        $user = new User();

        $email = 'jean.sairien@gmail.com';
        $user->setEmail($email);
        $this->assertSame($email,$user->getEmail(),'GetEmail');
        $this->assertSame($email,$user->getUserIdentifier(),'GetUserIdentifier');
        $this->assertSame($email,$user->getUserName(),'GetUserName');

    }

    public function testNomPrenometterSetter()
    {
        $user = new User();

        $nom = 'Sairien';
        $prenom = 'Jean';
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $this->assertSame($nom,$user->getNom(),'GetNom');
        $this->assertSame($prenom,$user->getPrenom(),'GetPrenom');

    }

    public function testIsArchive()
    {
        $user = new User();

        $user->setArchive(true);
        $this->assertSame(true,$user->isArchive(),'isArchive');
        $user->setArchive(false);
        $this->assertSame(false,$user->isArchive(),'isArchive');

    }  

    public function testApiEnabled()
    {
        $user = new User();

        $user->setApiEnabled(true);
        $this->assertSame(true,$user->isApiEnabled(),'isApiEnabled');
        $user->setApiEnabled(false);
        $this->assertSame(false,$user->isApiEnabled(),'isApiEnabled');

    }  

    public function testDeletedAtCreatedAt()
    {
        $user = new User();

        $createdAt = new DateTimeImmutable('now');
        $deletedAt = new DateTimeImmutable('now');
        $user->setCreatedAt($createdAt);
        $user->setDeletedAt($deletedAt);
        $this->assertSame($createdAt,$user->getCreatedAt(),'GetCreatedAt');
        $this->assertSame($deletedAt,$user->getDeletedAt(),'GetDeletedAt');

    }  

    public function testRole()
    {
        $user = new User();

        $this->assertEquals(['ROLE_USER'], $user->getRoles(),'GetRole USER'); 
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles(),'GetRole ADMIN'); 
        $this->assertContains('ROLE_USER', $user->getRoles(),'GetRole USER'); 

        $user->setRoles(['ROLE_ADMIN']);
        $role = $user->getRoles();
        $this->assertSame(array_unique($role),$role,'GetRole Unique');

    }

    public function testPasswordGetterSetter()
    {
        $user = new User();
        $user->setPassword("12345678");

        $this->assertSame("12345678", $user->getPassword(),'getPassword');
    }

    public function testSerializeHashesPassword()
    {
        $user = new User();
        $user->setPassword("12345678");

        $data = $user->__serialize();

        $this->assertNotSame("12345678", $data["\0App\Entity\User\0password"],'Serialization Password pas en clair');
        $this->assertSame(hash('crc32c', "12345678"), $data["\0App\Entity\User\0password"],'Serialization Password crc32c');
    }

    public function testAddOrder()
    {
        $user = new User();
        $order = new Order();

        // initialisation User donc orders est vide
        $this->assertCount(0,$user->getOrders(),'GetOrder Init a vide');
        
        // test de l'ajout d'un order
        $user->addOrder($order);
        // orders contient 1 order
        $this->assertCount(1,$user->getOrders(),'GetOrder apres addOrder');        
        // on doit recuperer l'user a partir de l'order
        $this->assertSame($user,$order->getUser(),'GetUser');

        // test duplicate order
        $user->addOrder($order);
        // on doit toujours avoir qu'un seul order vu qu'on ajoute 2 fois le meme
        $this->assertCount(1,$user->getOrders(),'GetOrder apres 2 addOrder');        
        
    }

    public function testRemoveOrder()
    {
        $user = new User();
        $order = new Order();

        // test de l'ajout d'un order
        $user->addOrder($order);
        // orders contient 1 order
        $this->assertCount(1,$user->getOrders(),'GetOrder apres addOrder');        

        // on supprime l'order
        $user->removeOrder($order);
         // orders contient 0 order
        $this->assertCount(0,$user->getOrders(),'GetOrder apres addOrder');        
        // order ne doit plus avoir d'user
        $this->assertNull($order->getUser(),'GetUser');

    }
}