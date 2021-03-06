<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This adds a 'position' column to the course/session learning materials tables.
 */
class Version20170210175148 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE course_learning_material ADD position INT NOT NULL');
        $this->addSql('ALTER TABLE session_learning_material ADD position INT NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE course_learning_material DROP position');
        $this->addSql('ALTER TABLE session_learning_material DROP position');
    }
}
