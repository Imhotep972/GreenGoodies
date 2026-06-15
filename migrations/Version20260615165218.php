<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260615165218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orderlines CHANGE price price INT NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE amount amount INT NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE price price INT NOT NULL, CHANGE shortdesc shortdesc VARCHAR(70) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `orderlines` CHANGE price price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE amount amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE `products` CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE shortdesc shortdesc VARCHAR(100) NOT NULL');
    }
}
