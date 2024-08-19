<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308160236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_tache DROP FOREIGN KEY FK_66FFCDA5C18272');
        $this->addSql('DROP INDEX IDX_66FFCDA5C18272 ON categorie_tache');
        $this->addSql('ALTER TABLE categorie_tache DROP projet_id');
        $this->addSql('ALTER TABLE projet ADD projet_id INT DEFAULT NULL, DROP nom');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9C18272 FOREIGN KEY (projet_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_50159CA9C18272 ON projet (projet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_tache ADD projet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie_tache ADD CONSTRAINT FK_66FFCDA5C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('CREATE INDEX IDX_66FFCDA5C18272 ON categorie_tache (projet_id)');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9C18272');
        $this->addSql('DROP INDEX IDX_50159CA9C18272 ON projet');
        $this->addSql('ALTER TABLE projet ADD nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP projet_id');
    }
}
