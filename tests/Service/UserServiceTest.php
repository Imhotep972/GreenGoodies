<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations; // pour eviter les PHPUnit Notices quand  UserPasswordHasherInterface n'est pas utilisé
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AllowMockObjectsWithoutExpectations]
class UserServiceTest extends TestCase
{
    private $entityManager;
    private $hasher;
    private $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);    // Mock EntityManager pour simuler la methode flush
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);      // Mock UserPasswordHasherInterface pour simuler le hachage du password
        $this->service = new UserService($this->entityManager, $this->hasher);      // le service UserService testé
    }

    private function createUser() : User
    {
        /** @var User $user */
        $user = new User();
        $user->setEmail('master.imhotep@gmail.com');
        $user->setPassword('12345678');
        $user->setPrenom('Master');
        $user->setNom('Imhotep');
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    public function testToggleApiAccessActivateSuccess(): void
    {
        // on cree un user
        $user = $this->createUser();
        $user->setApiEnabled(false);            // meme si apiEnabled est à false a la création

        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user)
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // on effectue l'appel à la fonction toggleApiAccess
        $result = $this->service->toogleApiAccess($user);
        $status = $result['statut'];
        $message = $result['message'];
        $user = $result['user'];

        //  ['status'] ->  'success'
        $this->assertEquals('success', $status,'Toggle ApiAccess Inactif-> Actif - Statut Success');
        // ['message'] -> 'Accès API Activé'
        $this->assertEquals('Accès API activé', $message,'Toggle ApiAccess Inactif-> Actif - Message Success');
        // ApiAccess -> true
        $this->assertTrue($user->isApiEnabled(),'Toggle ApiAccess Inactif-> Actif - apiEnabled true');
    }

    public function testToggleApiAccessDesactivateSuccess(): void
    {
        // on cree un user
        $user = $this->createUser();
        $user->setApiEnabled(true);

        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user)
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // on effectue l'appel à la fonction toggleApiAccess
        $result = $this->service->toogleApiAccess($user);
        $status = $result['statut'];
        $message = $result['message'];
        $user = $result['user'];

        //  ['status'] ->  'success'
        $this->assertEquals('success', $status,'Toggle ApiAccess Actif->Inactif - Statut Success');
        // ['message'] -> 'Accès API Activé'
        $this->assertEquals('Accès API désactivé', $message,'Toggle ApiAccess Actif->Inactif - Message Success');
        // ApiAccess -> false
        $this->assertFalse($user->isApiEnabled(),'Toggle ApiAccess Actif->Inactif - apiEnabled => false');
    }

    public function testToggleApiAccessError(): void
    {
        // on cree un user
        $user = $this->createUser();

        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user) et on simule une erreur      
        $this->entityManager
             ->expects($this->once())
             ->method('flush')
             ->willThrowException(new \Exception('DB error'));

        // on effectue l'appel à la fonction toggleApiAccess
        $result = $this->service->toogleApiAccess($user);
        $status = $result['statut'];
        $message = $result['message'];
        $user = $result['user'];

        //  ['status'] ->  'danger'
        $this->assertEquals('danger', $status,'Toggle ApiAccess Error - Statut Error');
        // ['message'] -> 'Un problème est survenu lors de l\'activation/desactivation de l\'acces API'
        $this->assertEquals('Un problème est survenu lors de l\'activation/desactivation de l\'acces API', $message,'Toggle ApiAccess Error - Message Error');
        // ApiAccess -> false
        $this->assertFalse($user->isApiEnabled(),'Toggle ApiAccess Error - apiEnabled false');
    }

    public function testDeleteAccount()
    {
        $user = $this->createUser();

        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user)
        $this->entityManager
             ->expects($this->once())
             ->method('flush');

        $result = $this->service->deleteAccount($user);
        $status = $result['statut'];
        $message = $result['message'];
        $user = $result['user'];

        //  ['status'] ->  'success'
        $this->assertEquals('success', $status,'Delete Account - Statut Success');
        // ['message'] -> 'Compte supprimé avec succès'
        $this->assertEquals('Compte supprimé avec succès', $message,'Delete Account - Message Success');
        // archive -> true
        $this->assertTrue($user->isArchive(),'Delete Account - archive true');
        // ApiEnabled -> false
        $this->assertFalse($user->isApiEnabled(),'Delete Account - apiEnabled false');
        // deletedAt
        $this->assertInstanceOf(DateTimeImmutable::class,$user->getDeletedAt(),'Delete Account - deletedAt => date actuelle');
    }

    public function testDeleteAccountError()
    {
        $user = $this->createUser();

        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user)
        // on force une erreur de la methode flush 
        $this->entityManager
             ->expects($this->once())
             ->method('flush')
             ->willThrowException(new \Exception('DB error'));

        $result = $this->service->deleteAccount($user);
        $status = $result['statut'];
        $message = $result['message'];
        $user = $result['user'];

        //  ['status'] ->  'danger'
        $this->assertEquals('danger', $status,'Delete Account Force Error - Statut Error');
        // ['message'] -> 'Un problème est survenu lors de la suppression du compte'
        $this->assertEquals('Un problème est survenu lors de la suppression du compte', $message,'Delete Account Force Error - Message Error');
    }

    public function testCreateAccount()
    {
        $user = $this->createUser();

        $this->entityManager
             ->expects($this->once())
             ->method('flush');

        $result = $this->service->createAccount($user);
        $status = $result['statut'];
        $message = $result['message'];
        $user = $result['user'];

        //  ['status'] ->  'success'
        $this->assertEquals('success', $status,'Create Account - Statut Success');
        // ['message'] -> 'Vous etes inscrit sur le site, veuillez maintenant vous connecter'
        $this->assertEquals('Vous etes inscrit sur le site, veuillez maintenant vous connecter', $message,'Create Account - Message Success');
        // createdAt
        $this->assertInstanceOf(DateTimeImmutable::class,$user->getCreatedAt(),'Create Account - createdAt DateTimeImmutable');
        // archive -> false
        $this->assertFalse($user->isArchive(),'Create Account - archive false');
        // ApiEnabled -> false
        $this->assertFalse($user->isApiEnabled(),'Create Account - apienabled false');
        // getRole [ROLE_USER]
        $this->assertEquals(['ROLE_USER'], $user->getRoles(),'Create Account - Role ROLE_USER');  
    }    
    
    public function testCreateAccountError()
    {
        $user = $this->createUser();
        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user)
        // on force une erreur de la methode flush 
        $this->entityManager
             ->expects($this->once())
             ->method('flush')
             ->willThrowException(new \Exception('DB error'));

        $result = $this->service->createAccount($user);
        $status = $result['statut'];
        $message = $result['message'];
        $user = $result['user'];

        //  ['status'] ->  'danger'
        $this->assertEquals('danger', $status,'Create Account Force Error - Statut Error');
        // ['message'] -> 'Une erreur a eu lieu lors de la création de votre compte'
        $this->assertEquals('Une erreur a eu lieu lors de la création de votre compte', $message,'Create Account Force Error - Message Error');
    }
}