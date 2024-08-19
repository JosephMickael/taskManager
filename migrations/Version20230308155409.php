<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308155409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA95176442D');
        $this->addSql('DROP INDEX IDX_50159CA95176442D ON projet');
        $this->addSql('ALTER TABLE projet DROP porteur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet ADD porteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA95176442D FOREIGN KEY (porteur_id) REFERENCES porteur_projet (id)');
        $this->addSql('CREATE INDEX IDX_50159CA95176442D ON projet (porteur_id)');
    }
}
