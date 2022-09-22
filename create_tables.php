<?php

$q = "CREATE TABLE IF NOT EXISTS `projects` ( 
        `id` INT NOT NULL AUTO_INCREMENT ,
        `name` VARCHAR(100) NOT NULL ,
        PRIMARY KEY (`id`)) 
        ENGINE = InnoDB;";

mysqli_query(CommonDB::$connection, $q);


$q = "CREATE TABLE IF NOT EXISTS `employees` ( 
        `id` INT NOT NULL AUTO_INCREMENT ,
        `name` VARCHAR(100) NOT NULL ,
        PRIMARY KEY (`id`)) 
        ENGINE = InnoDB;";

mysqli_query(CommonDB::$connection, $q);


$q = "CREATE TABLE `register` ( 
    `id` INT NOT NULL AUTO_INCREMENT ,
    `cod_employee` INT NOT NULL ,
    `cod_project` INT NOT NULL , 
    `hours` INT NOT NULL , 
    `date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`), 
    INDEX (`cod_employee`), 
    INDEX (`cod_project`)) 
    ENGINE = InnoDB";

mysqli_query(CommonDB::$connection, $q);


$q = "ALTER TABLE `register` ADD FOREIGN KEY (`cod_employee`) REFERENCES `employees`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
mysqli_query(CommonDB::$connection, $q);

$q = "ALTER TABLE `register` ADD FOREIGN KEY (`cod_project`) REFERENCES `projects`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
mysqli_query(CommonDB::$connection, $q);