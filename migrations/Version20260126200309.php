<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126200309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE donation_campaign (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, montant_objectif NUMERIC(10, 2) NOT NULL, montant_collecte NUMERIC(10, 2) NOT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, actif TINYINT(1) NOT NULL, principale TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE gallery_image (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, image_path VARCHAR(255) NOT NULL, ordre INT NOT NULL, actif TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page_content (id INT AUTO_INCREMENT NOT NULL, section_key VARCHAR(50) NOT NULL, titre VARCHAR(255) NOT NULL, sous_titre LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, video_path VARCHAR(255) DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, button_text VARCHAR(255) DEFAULT NULL, button_link VARCHAR(255) DEFAULT NULL, extra_data JSON DEFAULT NULL, actif TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_4A5DB3C514C78FB (section_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
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
        $this->addSql('DROP TABLE donation_campaign');
        $this->addSql('DROP TABLE gallery_image');
        $this->addSql('DROP TABLE page_content');
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B87555151C109075');
        $this->addSql('ALTER TABLE beneficiaire DROP FOREIGN KEY FK_B140D80297A77B84');
        $this->addSql('ALTER TABLE beneficiaire_activite DROP FOREIGN KEY FK_F826E0F35AF81F68');
        $this->addSql('ALTER TABLE beneficiaire_activite DROP FOREIGN KEY FK_F826E0F39B0F88B1');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C5AF81F68');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09CB03A8386');
    }
}
