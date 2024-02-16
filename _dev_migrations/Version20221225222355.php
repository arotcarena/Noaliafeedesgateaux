<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221225222355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dashboard ADD count_cakes_spotlighted INT NOT NULL, ADD count_cakes INT NOT NULL, DROP cakes_spotlighted, DROP cakes');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dashboard ADD cakes_spotlighted INT NOT NULL, ADD cakes INT NOT NULL, DROP count_cakes_spotlighted, DROP count_cakes');
    }
}
