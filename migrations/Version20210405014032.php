<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210405014032 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, order_cart_id INT DEFAULT NULL, INDEX IDX_BA388B7B092A811 (store_id), INDEX IDX_BA388B7ECAF64F4 (order_cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_line (id INT AUTO_INCREMENT NOT NULL, variant_id INT NOT NULL, cart_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_3EF1B4CF3B69A9AF (variant_id), INDEX IDX_3EF1B4CF1AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2D5B0234F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery_option (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE follower (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, store_id INT NOT NULL, INDEX IDX_B9D609469395C3F3 (customer_id), INDEX IDX_B9D60946B092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_472B783AB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Products (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, menu_category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TINYTEXT NOT NULL, price DOUBLE PRECISION DEFAULT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_7D053A93A76ED395 (user_id), INDEX IDX_7D053A937ABA83AE (menu_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_category (id INT AUTO_INCREMENT NOT NULL, cuisine_name VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, created_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, delivery_option_id INT DEFAULT NULL, city_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, driver_id INT DEFAULT NULL, track_number VARCHAR(255) NOT NULL, full_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, order_date DATETIME NOT NULL, status VARCHAR(10) NOT NULL, order_book_date DATETIME DEFAULT NULL, book_table TINYINT(1) DEFAULT NULL, address TINYTEXT DEFAULT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_F5299398E3A151FD (delivery_option_id), INDEX IDX_F52993988BAC62AF (city_id), INDEX IDX_F52993989395C3F3 (customer_id), INDEX IDX_F5299398C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, stars DOUBLE PRECISION NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_6970EB0FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2D737AEF8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsored_restaurant (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_4CD7405A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_history (id INT AUTO_INCREMENT NOT NULL, status_order_id INT NOT NULL, status VARCHAR(10) NOT NULL, status_date DATETIME NOT NULL, INDEX IDX_2F6A07CE1045CAE0 (status_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_menu (id INT AUTO_INCREMENT NOT NULL, menu_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, INDEX IDX_5A93A552CCD7E912 (menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transportation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, licence_plate VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9B1722DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, service_type_id INT DEFAULT NULL, section_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, contact_phone VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, full_name VARCHAR(255) NOT NULL, registration_date DATETIME NOT NULL, store_phone VARCHAR(255) DEFAULT NULL, store_name VARCHAR(255) DEFAULT NULL, post_code VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, localisation VARCHAR(255) DEFAULT NULL, profile_image VARCHAR(255) DEFAULT NULL, cover_image VARCHAR(255) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', occupied TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6498BAC62AF (city_id), INDEX IDX_8D93D649AC8DE0F (service_type_id), INDEX IDX_8D93D649D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variants (id INT AUTO_INCREMENT NOT NULL, menu_id INT DEFAULT NULL, sub_menu_id INT DEFAULT NULL, size VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, sort INT NOT NULL, INDEX IDX_F143BFADCCD7E912 (menu_id), INDEX IDX_F143BFADB30FB5E6 (sub_menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7B092A811 FOREIGN KEY (store_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7ECAF64F4 FOREIGN KEY (order_cart_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE cart_line ADD CONSTRAINT FK_3EF1B4CF3B69A9AF FOREIGN KEY (variant_id) REFERENCES variants (id)');
        $this->addSql('ALTER TABLE cart_line ADD CONSTRAINT FK_3EF1B4CF1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D609469395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D60946B092A811 FOREIGN KEY (store_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AB1E7706E FOREIGN KEY (restaurant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE Products ADD CONSTRAINT FK_7D053A93A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE Products ADD CONSTRAINT FK_7D053A937ABA83AE FOREIGN KEY (menu_category_id) REFERENCES menu_category (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398E3A151FD FOREIGN KEY (delivery_option_id) REFERENCES delivery_option (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993988BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C3423909 FOREIGN KEY (driver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE sponsored_restaurant ADD CONSTRAINT FK_4CD7405A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE status_history ADD CONSTRAINT FK_2F6A07CE1045CAE0 FOREIGN KEY (status_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE sub_menu ADD CONSTRAINT FK_5A93A552CCD7E912 FOREIGN KEY (menu_id) REFERENCES Products (id)');
        $this->addSql('ALTER TABLE transportation ADD CONSTRAINT FK_9B1722DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AC8DE0F FOREIGN KEY (service_type_id) REFERENCES service_type (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE variants ADD CONSTRAINT FK_F143BFADCCD7E912 FOREIGN KEY (menu_id) REFERENCES Products (id)');
        $this->addSql('ALTER TABLE variants ADD CONSTRAINT FK_F143BFADB30FB5E6 FOREIGN KEY (sub_menu_id) REFERENCES sub_menu (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_line DROP FOREIGN KEY FK_3EF1B4CF1AD5CDBF');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993988BAC62AF');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF8BAC62AF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498BAC62AF');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234F92F3E70');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398E3A151FD');
        $this->addSql('ALTER TABLE sub_menu DROP FOREIGN KEY FK_5A93A552CCD7E912');
        $this->addSql('ALTER TABLE variants DROP FOREIGN KEY FK_F143BFADCCD7E912');
        $this->addSql('ALTER TABLE Products DROP FOREIGN KEY FK_7D053A937ABA83AE');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7ECAF64F4');
        $this->addSql('ALTER TABLE status_history DROP FOREIGN KEY FK_2F6A07CE1045CAE0');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D823E37A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AC8DE0F');
        $this->addSql('ALTER TABLE variants DROP FOREIGN KEY FK_F143BFADB30FB5E6');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7B092A811');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D609469395C3F3');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D60946B092A811');
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AB1E7706E');
        $this->addSql('ALTER TABLE Products DROP FOREIGN KEY FK_7D053A93A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C3423909');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FA76ED395');
        $this->addSql('ALTER TABLE sponsored_restaurant DROP FOREIGN KEY FK_4CD7405A76ED395');
        $this->addSql('ALTER TABLE transportation DROP FOREIGN KEY FK_9B1722DA76ED395');
        $this->addSql('ALTER TABLE cart_line DROP FOREIGN KEY FK_3EF1B4CF3B69A9AF');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_line');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE delivery_option');
        $this->addSql('DROP TABLE follower');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE Products');
        $this->addSql('DROP TABLE menu_category');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE service_type');
        $this->addSql('DROP TABLE sponsored_restaurant');
        $this->addSql('DROP TABLE status_history');
        $this->addSql('DROP TABLE sub_menu');
        $this->addSql('DROP TABLE transportation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE variants');
    }
}
