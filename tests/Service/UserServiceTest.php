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
        $user->setApiEnabled(false);

        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user)
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // on effectue l'appel à la fonction toggleApiAccess
        $result = $this->service->toogleApiAccess($user);

        //  ['status'] ->  'success'
        $this->assertEquals('success', $result['statut'],'Toggle ApiAccess Inactif-> Actif Statut success');
        // ['message'] -> 'Accès API Activé'
        $this->assertEquals('Accès API activé', $result['message'],'Toggle ApiAccess Inactif-> Actif  Message Actif');
        // ApiAccess -> true
        $this->assertTrue($user->isApiEnabled(),'Toggle ApiAccess Inactif-> Actif apiEnabled => true');
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

        //  ['status'] ->  'success'
        $this->assertEquals('success', $result['statut'],'Toggle ApiAccess Actif->Inactif Statut success');
        // ['message'] -> 'Accès API Activé'
        $this->assertEquals('Accès API désactivé', $result['message'],'Toggle ApiAccess Actif->Inactif  Message Inactif');
        // ApiAccess -> false
        $this->assertFalse($user->isApiEnabled(),'Toggle ApiAccess apiEnabled => false');
    }

    public function testToggleApiAccessError(): void
    {
        // on cree un user
        $user = $this->createUser();

        // accès à l'entitymanager , on s'attend que la methode toggleApiAccess soit appelée une fois (maj de la table user)      
        // on force une erreur de la methode flush 
        $this->entityManager
             ->expects($this->once())
             ->method('flush')
             ->willThrowException(new \Exception('DB error'));

        // on effectue l'appel à la fonction toggleApiAccess
        $result = $this->service->toogleApiAccess($user);

        //  ['status'] ->  'danger'
        $this->assertEquals('danger', $result['statut'],'Toggle ApiAccess Erreur => Statut = danger');
        // ['message'] -> 'Un problème est survenu lors de l\'activation/desactivation de l\'acces API'
        $this->assertEquals('Un problème est survenu lors de l\'activation/desactivation de l\'acces API', $result['message'],'Toggle ApiAccess Message Erreur');
        // ApiAccess -> true
        $this->assertFalse($user->isApiEnabled(),'Toggle ApiAccess apiEnabled => false');
    }

    public function testDeleteAccount()
    {
        $user = $this->createUser();

        // accès à l'entitymanager , on s'attend que la methode flush soit appelée une fois (maj de la table user)
        $this->entityManager
             ->expects($this->once())
             ->method('flush');

        $result = $this->service->deleteAccount($user);
        //  ['status'] ->  'success'
        $this->assertEquals('success', $result['statut'],'DeleteAccount Statut success');
        // ['message'] -> 'Compte supprimé avec succès'
        $this->assertEquals('Compte supprimé avec succès', $result['message'],'DeleteAccount ok');
        // archive -> true
        $this->assertTrue($user->isArchive(),'DeleteAccount archive => true');
        // ApiEnabled -> false
        $this->assertFalse($user->isApiEnabled(),'DeleteAccount apienabled => false');
        // deletedAt
        $this->assertInstanceOf(DateTimeImmutable::class,$user->getDeletedAt(),'DeleteAccount deletedAt => date actuelle');
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
        //  ['status'] ->  'danger'
        $this->assertEquals('danger', $result['statut'],'DeleteAccount Statut error');
        // ['message'] -> 'Compte supprimé avec succès'
        $this->assertEquals('Un problème est survenu lors de la suppression du compte', $result['message'],'DeleteAccount error');
    }

    public function testCreateAccount()
    {
        $user = $this->createUser();

        $this->entityManager
             ->expects($this->once())
             ->method('flush');

        $result = $this->service->createAccount($user);
        //  ['status'] ->  'success'
        $this->assertEquals('success', $result['statut'],'CreateAccount Statut success');
        // ['message'] -> 'Vous etes inscrit sur le site, veuillez maintenant vous connecter'
        $this->assertEquals('Vous etes inscrit sur le site, veuillez maintenant vous connecter', $result['message'],'CreateAccount ok');
        // createdAt
        $this->assertInstanceOf(DateTimeImmutable::class,$user->getCreatedAt(),'CreateAccount createdAt => date actuelle');
        // archive -> false
        $this->assertFalse($user->isArchive(),'CreateAccount archive => false');
        // ApiEnabled -> false
        $this->assertFalse($user->isApiEnabled(),'CreateAccount apienabled => false');
        // getRole [ROLE_USER]
        $this->assertEquals(['ROLE_USER'], $user->getRoles(),'Role');  
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
        //  ['status'] ->  'danger'
        $this->assertEquals('danger', $result['statut'],'CreateAccount Statut error');
        // ['message'] -> 'Vous etes inscrit sur le site, veuillez maintenant vous connecter'
        $this->assertEquals('Une erreur a eu lieu lors de la création de votre compte', $result['message'],'CreateAccount error');
    }
}