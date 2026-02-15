<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215155103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adress (id INT AUTO_INCREMENT NOT NULL, house_number VARCHAR(50) DEFAULT NULL, street_name VARCHAR(255) DEFAULT NULL, postcode VARCHAR(15) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE boat (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, max_user INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_active TINYINT NOT NULL, boat_length DOUBLE PRECISION NOT NULL, boat_width DOUBLE PRECISION NOT NULL, boat_draught DOUBLE PRECISION NOT NULL, cabine_number INT DEFAULT NULL, bed_number INT DEFAULT NULL, fuel VARCHAR(100) NOT NULL, power_engine VARCHAR(100) NOT NULL, type_id INT NOT NULL, model_id INT DEFAULT NULL, adress_id INT DEFAULT NULL, INDEX IDX_D86E834AC54C8C93 (type_id), INDEX IDX_D86E834A7975B7E7 (model_id), INDEX IDX_D86E834A8486F9AC (adress_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE boat_formula (boat_id INT NOT NULL, formula_id INT NOT NULL, INDEX IDX_99A1CD04A1E84A29 (boat_id), INDEX IDX_99A1CD04A50A6386 (formula_id), PRIMARY KEY (boat_id, formula_id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE formula (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, img_path VARCHAR(255) NOT NULL, boat_id INT DEFAULT NULL, INDEX IDX_6A2CA10CA1E84A29 (boat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE model (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE rental (id INT AUTO_INCREMENT NOT NULL, rental_start DATETIME NOT NULL, rental_end DATETIME NOT NULL, rental_price INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, user_id INT DEFAULT NULL, boat_id INT DEFAULT NULL, INDEX IDX_1619C27DA76ED395 (user_id), INDEX IDX_1619C27DA1E84A29 (boat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE rental_formula (rental_id INT NOT NULL, formula_id INT NOT NULL, INDEX IDX_B4D7826EA7CF2329 (rental_id), INDEX IDX_B4D7826EA50A6386 (formula_id), PRIMARY KEY (rental_id, formula_id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, phone_number VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_active TINYINT NOT NULL, adress_id INT DEFAULT NULL, INDEX IDX_8D93D6498486F9AC (adress_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE boat ADD CONSTRAINT FK_D86E834AC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE boat ADD CONSTRAINT FK_D86E834A7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('ALTER TABLE boat ADD CONSTRAINT FK_D86E834A8486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)');
        $this->addSql('ALTER TABLE boat_formula ADD CONSTRAINT FK_99A1CD04A1E84A29 FOREIGN KEY (boat_id) REFERENCES boat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boat_formula ADD CONSTRAINT FK_99A1CD04A50A6386 FOREIGN KEY (formula_id) REFERENCES formula (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA1E84A29 FOREIGN KEY (boat_id) REFERENCES boat (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DA1E84A29 FOREIGN KEY (boat_id) REFERENCES boat (id)');
        $this->addSql('ALTER TABLE rental_formula ADD CONSTRAINT FK_B4D7826EA7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rental_formula ADD CONSTRAINT FK_B4D7826EA50A6386 FOREIGN KEY (formula_id) REFERENCES formula (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boat DROP FOREIGN KEY FK_D86E834AC54C8C93');
        $this->addSql('ALTER TABLE boat DROP FOREIGN KEY FK_D86E834A7975B7E7');
        $this->addSql('ALTER TABLE boat DROP FOREIGN KEY FK_D86E834A8486F9AC');
        $this->addSql('ALTER TABLE boat_formula DROP FOREIGN KEY FK_99A1CD04A1E84A29');
        $this->addSql('ALTER TABLE boat_formula DROP FOREIGN KEY FK_99A1CD04A50A6386');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA1E84A29');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DA76ED395');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DA1E84A29');
        $this->addSql('ALTER TABLE rental_formula DROP FOREIGN KEY FK_B4D7826EA7CF2329');
        $this->addSql('ALTER TABLE rental_formula DROP FOREIGN KEY FK_B4D7826EA50A6386');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498486F9AC');
        $this->addSql('DROP TABLE adress');
        $this->addSql('DROP TABLE boat');
        $this->addSql('DROP TABLE boat_formula');
        $this->addSql('DROP TABLE formula');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE rental');
        $this->addSql('DROP TABLE rental_formula');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
    }
}
