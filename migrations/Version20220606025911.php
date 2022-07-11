<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220606025911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dialogue_step (id INT AUTO_INCREMENT NOT NULL, root_step TINYINT(1) NOT NULL, output VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dialogue_step_dialogue_step (dialogue_step_source INT NOT NULL, dialogue_step_target INT NOT NULL, INDEX IDX_FDCB478071EF147F (dialogue_step_source), INDEX IDX_FDCB4780680A44F0 (dialogue_step_target), PRIMARY KEY(dialogue_step_source, dialogue_step_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dialogue_step_dialogue_step ADD CONSTRAINT FK_FDCB478071EF147F FOREIGN KEY (dialogue_step_source) REFERENCES dialogue_step (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dialogue_step_dialogue_step ADD CONSTRAINT FK_FDCB4780680A44F0 FOREIGN KEY (dialogue_step_target) REFERENCES dialogue_step (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialogue_step_dialogue_step DROP FOREIGN KEY FK_FDCB478071EF147F');
        $this->addSql('ALTER TABLE dialogue_step_dialogue_step DROP FOREIGN KEY FK_FDCB4780680A44F0');
        $this->addSql('DROP TABLE dialogue_step');
        $this->addSql('DROP TABLE dialogue_step_dialogue_step');
    }
}
