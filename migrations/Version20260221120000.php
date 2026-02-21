<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260221120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user and calendar tables and category column on event';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_USER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4");
        $this->addSql("CREATE TABLE calendar (id BINARY(16) NOT NULL, user_id INT NOT NULL, event_id BINARY(16) NOT NULL, INDEX IDX_CALENDAR_USER (user_id), INDEX IDX_CALENDAR_EVENT (event_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4");
        $this->addSql("ALTER TABLE event ADD category VARCHAR(255) DEFAULT 'culture' NOT NULL");
        $this->addSql("ALTER TABLE calendar ADD CONSTRAINT FK_CALENDAR_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE calendar ADD CONSTRAINT FK_CALENDAR_EVENT FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_CALENDAR_USER');
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_CALENDAR_EVENT');
        $this->addSql('ALTER TABLE event DROP COLUMN category');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('DROP TABLE user');
    }
}
