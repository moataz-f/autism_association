<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260124134051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activite (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, type VARCHAR(50) NOT NULL, lieu VARCHAR(100) DEFAULT NULL, capacite_max INT DEFAULT NULL, actif TINYINT(1) NOT NULL, personnel_id INT DEFAULT NULL, INDEX IDX_B87555151C109075 (personnel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE beneficiaire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, date_naissance DATE NOT NULL, genre VARCHAR(10) NOT NULL, adresse LONGTEXT DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, niveau_autisme VARCHAR(50) NOT NULL, diagnostic LONGTEXT DEFAULT NULL, date_inscription DATETIME NOT NULL, actif TINYINT(1) NOT NULL, famille_id INT NOT NULL, INDEX IDX_B140D80297A77B84 (famille_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE beneficiaire_activite (beneficiaire_id INT NOT NULL, activite_id INT NOT NULL, INDEX IDX_F826E0F35AF81F68 (beneficiaire_id), INDEX IDX_F826E0F39B0F88B1 (activite_id), PRIMARY KEY(beneficiaire_id, activite_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE famille (id INT AUTO_INCREMENT NOT NULL, nom_responsable VARCHAR(100) NOT NULL, prenom_responsable VARCHAR(100) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(180) DEFAULT NULL, adresse LONGTEXT DEFAULT NULL, date_inscription DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE personnel (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, role VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(180) NOT NULL, date_embauche DATE NOT NULL, actif TINYINT(1) NOT NULL, specialisation LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rapport (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, contenu LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, beneficiaire_id INT NOT NULL, created_by_id INT DEFAULT NULL, INDEX IDX_BE34A09C5AF81F68 (beneficiaire_id), INDEX IDX_BE34A09CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
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
        $this->addSql('ALTER TABLE beneficiaire DROP FOREIGN KEY FK_B140D80297A77B84');
        $this->addSql('ALTER TABLE beneficiaire_activite DROP FOREIGN KEY FK_F826E0F35AF81F68');
        $this->addSql('ALTER TABLE beneficiaire_activite DROP FOREIGN KEY FK_F826E0F39B0F88B1');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C5AF81F68');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09CB03A8386');
        $this->addSql('DROP TABLE activite');
        $this->addSql('DROP TABLE beneficiaire');
        $this->addSql('DROP TABLE beneficiaire_activite');
        $this->addSql('DROP TABLE famille');
        $this->addSql('DROP TABLE personnel');
        $this->addSql('DROP TABLE rapport');
        $this->addSql('DROP TABLE `user`');
    }
}
