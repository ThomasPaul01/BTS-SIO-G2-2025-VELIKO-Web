<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104100344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD password_change_authorized TINYINT(1) NOT NULL, CHANGE password_reset_token password_change_token VARCHAR(255) DEFAULT NULL, CHANGE password_reset_token_expiry password_change_token_expiry DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP password_change_authorized, CHANGE password_change_token password_reset_token VARCHAR(255) DEFAULT NULL, CHANGE password_change_token_expiry password_reset_token_expiry DATETIME DEFAULT NULL');
    }
}
