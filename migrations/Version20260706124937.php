<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260706124937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE business (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, city VARCHAR(50) NOT NULL, street VARCHAR(200) NOT NULL, house_number VARCHAR(10) NOT NULL, phone_number VARCHAR(15) NOT NULL, business_type_id INT NOT NULL, INDEX IDX_8D36E38987F37DE (business_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE business_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE business ADD CONSTRAINT FK_8D36E38987F37DE FOREIGN KEY (business_type_id) REFERENCES business_type (id)');
        $this->addSql('ALTER TABLE package CHANGE price price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE package ADD CONSTRAINT FK_DE68679512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE package ADD CONSTRAINT FK_DE686795A89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE business DROP FOREIGN KEY FK_8D36E38987F37DE');
        $this->addSql('DROP TABLE business');
        $this->addSql('DROP TABLE business_type');
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE package DROP FOREIGN KEY FK_DE68679512469DE2');
        $this->addSql('ALTER TABLE package DROP FOREIGN KEY FK_DE686795A89DB457');
        $this->addSql('ALTER TABLE package CHANGE price price NUMERIC(10, 2) NOT NULL');
    }
}
