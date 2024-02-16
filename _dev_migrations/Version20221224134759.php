<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221224134759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, route VARCHAR(255) NOT NULL, visited_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cake DROP FOREIGN KEY FK_FA13015D7FDDDCB8');
        $this->addSql('ALTER TABLE cake ADD CONSTRAINT FK_FA13015D7FDDDCB8 FOREIGN KEY (first_picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE `order` CHANGE seen seen TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F899F8008B6');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F899F8008B6 FOREIGN KEY (cake_id) REFERENCES cake (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE visit');
        $this->addSql('ALTER TABLE cake DROP FOREIGN KEY FK_FA13015D7FDDDCB8');
        $this->addSql('ALTER TABLE cake ADD CONSTRAINT FK_FA13015D7FDDDCB8 FOREIGN KEY (first_picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` CHANGE seen seen TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F899F8008B6');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F899F8008B6 FOREIGN KEY (cake_id) REFERENCES cake (id) ON DELETE SET NULL');
    }
}
