<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216190356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, discount VARCHAR(10) DEFAULT NULL, brand VARCHAR(50) DEFAULT NULL, price NUMERIC(10, 3) DEFAULT NULL, original_price NUMERIC(10, 3) DEFAULT NULL, categorie VARCHAR(255) DEFAULT NULL, subcategorie VARCHAR(255) DEFAULT NULL, gamme VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders ADD customer VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE products');
        $this->addSql('ALTER TABLE orders DROP customer');
    }
}
