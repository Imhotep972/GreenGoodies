<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260706125719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `users` ADD api_enabled TINYINT NOT NULL, DROP api, CHANGE archive archive TINYINT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_REFERENCE ON `orders` (reference)');
        $this->addSql('ALTER TABLE `orders` ADD archive TINYINT NOT NULL');
        $this->addSql('ALTER TABLE `products` CHANGE shortdesc short_description VARCHAR(70) NOT NULL, CHANGE description full_description VARCHAR(1500) NOT NULL');
        $this->addSql('ALTER TABLE `products` CHANGE photo picture VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `products` CHANGE picture photo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `products` CHANGE short_description shortdesc VARCHAR(70) NOT NULL, CHANGE full_description description VARCHAR(1500) NOT NULL');
        $this->addSql('ALTER TABLE `orders` DROP archive');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_REFERENCE ON `orders`');
        $this->addSql('ALTER TABLE `users` ADD api INT DEFAULT NULL, DROP api_enabled, CHANGE archive archive INT DEFAULT NULL');
    }
}
