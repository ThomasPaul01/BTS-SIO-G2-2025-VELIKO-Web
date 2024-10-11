<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241011090251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON station');
        $this->addSql('ALTER TABLE station DROP id, CHANGE station_id station_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE station ADD PRIMARY KEY (station_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station ADD id INT AUTO_INCREMENT NOT NULL, CHANGE station_id station_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
