<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221225223259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dashboard DROP FOREIGN KEY FK_5C94FFF84B09E92C');
        $this->addSql('DROP TABLE dashboard');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dashboard (id INT AUTO_INCREMENT NOT NULL, administrator_id INT NOT NULL, count_orders_not_seen INT NOT NULL, count_orders INT NOT NULL, count_visits_today INT NOT NULL, count_visits INT NOT NULL, count_cakes_spotlighted INT NOT NULL, count_cakes INT NOT NULL, UNIQUE INDEX UNIQ_5C94FFF84B09E92C (administrator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dashboard ADD CONSTRAINT FK_5C94FFF84B09E92C FOREIGN KEY (administrator_id) REFERENCES user (id)');
    }
}
