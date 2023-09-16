<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230916040053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP password');
        $this->addSql('ALTER TABLE working_hours DROP FOREIGN KEY FK_D72CDC3D9D86650F');
        $this->addSql('DROP INDEX UNIQ_D72CDC3D9D86650F ON working_hours');
        $this->addSql('ALTER TABLE working_hours CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE working_hours ADD CONSTRAINT FK_D72CDC3D9D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D72CDC3D9D86650F ON working_hours (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE working_hours DROP FOREIGN KEY FK_D72CDC3D9D86650F');
        $this->addSql('DROP INDEX UNIQ_D72CDC3D9D86650F ON working_hours');
        $this->addSql('ALTER TABLE working_hours CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE working_hours ADD CONSTRAINT FK_D72CDC3D9D86650F FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D72CDC3D9D86650F ON working_hours (user_id)');
    }
}
