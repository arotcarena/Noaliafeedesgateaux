<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221217212102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cake ADD first_picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cake ADD CONSTRAINT FK_FA13015D7FDDDCB8 FOREIGN KEY (first_picture_id) REFERENCES picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA13015D7FDDDCB8 ON cake (first_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cake DROP FOREIGN KEY FK_FA13015D7FDDDCB8');
        $this->addSql('DROP INDEX UNIQ_FA13015D7FDDDCB8 ON cake');
        $this->addSql('ALTER TABLE cake DROP first_picture_id');
    }
}
