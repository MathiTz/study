<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918004102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE working_hours DROP INDEX UNIQ_D72CDC3D9D86650F, ADD INDEX IDX_D72CDC3D9D86650F (user_id_id)');
        $this->addSql('ALTER TABLE working_hours CHANGE user_id_id user_id_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE working_hours DROP INDEX IDX_D72CDC3D9D86650F, ADD UNIQUE INDEX UNIQ_D72CDC3D9D86650F (user_id_id)');
        $this->addSql('ALTER TABLE working_hours CHANGE user_id_id user_id_id INT NOT NULL');
    }
}
