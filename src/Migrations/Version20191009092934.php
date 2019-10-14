<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191009092934 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE camp ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD sort INT NOT NULL');
        $this->addSql('ALTER TABLE camp_translation ADD translatable_id INT DEFAULT NULL, ADD locale VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE camp_translation ADD CONSTRAINT FK_17255E232C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES camp (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_17255E232C2AC5D3 ON camp_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX camp_translation_unique_translation ON camp_translation (translatable_id, locale)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE camp DROP created_at, DROP updated_at, DROP sort');
        $this->addSql('ALTER TABLE camp_translation DROP FOREIGN KEY FK_17255E232C2AC5D3');
        $this->addSql('DROP INDEX IDX_17255E232C2AC5D3 ON camp_translation');
        $this->addSql('DROP INDEX camp_translation_unique_translation ON camp_translation');
        $this->addSql('ALTER TABLE camp_translation DROP translatable_id, DROP locale');
    }
}
