-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema fototlac_db
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema fototlac_db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `fototlac_db` DEFAULT CHARACTER SET utf8 ;
USE `fototlac_db` ;

-- -----------------------------------------------------
-- Table `fototlac_db`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fototlac_db`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fototlac_db`.`order`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fototlac_db`.`order` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `state` VARCHAR(1) NOT NULL,
  `user_id` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL DEFAULT '',
  `surname` VARCHAR(45) NOT NULL DEFAULT '',
  `country` VARCHAR(45) NOT NULL DEFAULT '',
  `city` VARCHAR(45) NOT NULL DEFAULT '',
  `postal_code` VARCHAR(45) NOT NULL DEFAULT '',
  `street` VARCHAR(45) NOT NULL DEFAULT '',
  `house_number` VARCHAR(45) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_order_user_idx` (`user_id` ASC),
  CONSTRAINT `fk_order_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `fototlac_db`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fototlac_db`.`photo_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fototlac_db`.`photo_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `price_of_1x1_mm` DOUBLE NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `fototlac_db`.`photo_size`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fototlac_db`.`photo_size` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `width` INT NOT NULL,
  `height` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fototlac_db`.`photo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fototlac_db`.`photo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NOT NULL,
  `copies` INT NOT NULL,
  `order_id` INT NOT NULL,
  `size_width_in_mm` INT NOT NULL,
  `size_height_in_mm` INT NOT NULL,
  `photo_type_id` INT NOT NULL,
  `photo_size_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_photo_order1_idx` (`order_id` ASC),
  INDEX `fk_photo_photo_type1_idx` (`photo_type_id` ASC),
  INDEX `fk_photo_photo_size1_idx` (`photo_size_id` ASC),
  CONSTRAINT `fk_photo_order1`
    FOREIGN KEY (`order_id`)
    REFERENCES `fototlac_db`.`order` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_photo_photo_type1`
    FOREIGN KEY (`photo_type_id`)
    REFERENCES `fototlac_db`.`photo_type` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_photo_photo_size1`
    FOREIGN KEY (`photo_size_id`)
    REFERENCES `fototlac_db`.`photo_size` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO user (email, password) VALUES ('admin@admin.sk', '$2y$10$TxowPxeqCHlUja0cPjmYiuOLrXXKN170aFlGOlJVnlJ.U7NMzw5/W');

INSERT INTO photo_size (name, width, height) VALUES ('A3', 297, 420);
INSERT INTO photo_size (name, width, height) VALUES ('A4', 210, 297);
INSERT INTO photo_size (name, width, height) VALUES ('A5', 148, 210);

INSERT INTO photo_type (name, price_of_1x1_mm) VALUES ('Normálny', 0.00003);
INSERT INTO photo_type (name, price_of_1x1_mm) VALUES ('Prémiový', 0.00008);