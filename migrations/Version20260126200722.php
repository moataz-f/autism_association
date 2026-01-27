<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126200722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite ADD CONSTRAINT FK_B87555151C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id)');
        $this->addSql('ALTER TABLE beneficiaire ADD CONSTRAINT FK_B140D80297A77B84 FOREIGN KEY (famille_id) REFERENCES famille (id)');
        $this->addSql('ALTER TABLE beneficiaire_activite ADD CONSTRAINT FK_F826E0F35AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE beneficiaire_activite ADD CONSTRAINT FK_F826E0F39B0F88B1 FOREIGN KEY (activite_id) REFERENCES activite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09C5AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id)');
        $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09CB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B87555151C109075');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C5AF81F68');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09CB03A8386');
        $this->addSql('ALTER TABLE beneficiaire_activite DROP FOREIGN KEY FK_F826E0F35AF81F68');
        $this->addSql('ALTER TABLE beneficiaire_activite DROP FOREIGN KEY FK_F826E0F39B0F88B1');
        $this->addSql('ALTER TABLE beneficiaire DROP FOREIGN KEY FK_B140D80297A77B84');
    }
}
