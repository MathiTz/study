<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230907231845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE working_hours DROP FOREIGN KEY working_hours_ibfk_1');
        $this->addSql('DROP TABLE working_hours');
        $this->addSql('ALTER TABLE users ADD roles JSON NOT NULL, DROP name, CHANGE password password VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE is_admin is_admin TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX email TO UNIQ_1483A5E9E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE working_hours (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, work_date DATE NOT NULL, time1 TIME DEFAULT NULL, time2 TIME DEFAULT NULL, time3 TIME DEFAULT NULL, time4 TIME DEFAULT NULL, worked_time INT DEFAULT NULL, UNIQUE INDEX cons_user_day (user_id, work_date), INDEX IDX_D72CDC3DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE working_hours ADD CONSTRAINT working_hours_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD name VARCHAR(100) NOT NULL, DROP roles, CHANGE email email VARCHAR(50) NOT NULL, CHANGE password password VARCHAR(100) NOT NULL, CHANGE is_admin is_admin TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9e7927c74 TO email');
    }
}
