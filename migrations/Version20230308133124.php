<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308133124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet ADD chefprojet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA989A13275 FOREIGN KEY (chefprojet_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_50159CA989A13275 ON projet (chefprojet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA989A13275');
        $this->addSql('DROP INDEX IDX_50159CA989A13275 ON projet');
        $this->addSql('ALTER TABLE projet DROP chefprojet_id');
    }
}
