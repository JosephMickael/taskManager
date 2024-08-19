<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308132205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE porteur_projet DROP FOREIGN KEY FK_C31880E4C18272');
        $this->addSql('DROP INDEX IDX_C31880E4C18272 ON porteur_projet');
        $this->addSql('ALTER TABLE porteur_projet DROP projet_id');
        $this->addSql('ALTER TABLE projet ADD porteur_id INT DEFAULT NULL, CHANGE titre nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA95176442D FOREIGN KEY (porteur_id) REFERENCES porteur_projet (id)');
        $this->addSql('CREATE INDEX IDX_50159CA95176442D ON projet (porteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE porteur_projet ADD projet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE porteur_projet ADD CONSTRAINT FK_C31880E4C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('CREATE INDEX IDX_C31880E4C18272 ON porteur_projet (projet_id)');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA95176442D');
        $this->addSql('DROP INDEX IDX_50159CA95176442D ON projet');
        $this->addSql('ALTER TABLE projet DROP porteur_id, CHANGE nom titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
