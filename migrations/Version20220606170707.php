<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220606170707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialogue_step ADD previous_id INT DEFAULT NULL, DROP root_step');
        $this->addSql('ALTER TABLE dialogue_step ADD CONSTRAINT FK_4967F25D2DE62210 FOREIGN KEY (previous_id) REFERENCES dialogue_step (id)');
        $this->addSql('CREATE INDEX IDX_4967F25D2DE62210 ON dialogue_step (previous_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialogue_step DROP FOREIGN KEY FK_4967F25D2DE62210');
        $this->addSql('DROP INDEX IDX_4967F25D2DE62210 ON dialogue_step');
        $this->addSql('ALTER TABLE dialogue_step ADD root_step TINYINT(1) NOT NULL, DROP previous_id');
    }
}
