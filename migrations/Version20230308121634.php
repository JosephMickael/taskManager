<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308121634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chef_projet (id INT AUTO_INCREMENT NOT NULL, projet_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_54BBCCD2C18272 (projet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chef_projet ADD CONSTRAINT FK_54BBCCD2C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE porteur_projet ADD projet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE porteur_projet ADD CONSTRAINT FK_C31880E4C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('CREATE INDEX IDX_C31880E4C18272 ON porteur_projet (projet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chef_projet');
        $this->addSql('ALTER TABLE porteur_projet DROP FOREIGN KEY FK_C31880E4C18272');
        $this->addSql('DROP INDEX IDX_C31880E4C18272 ON porteur_projet');
        $this->addSql('ALTER TABLE porteur_projet DROP projet_id');
    }
}
