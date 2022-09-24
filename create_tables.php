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
    `cod_employees` INT NOT NULL ,
    `cod_projects` INT NOT NULL , 
    `hours` INT NOT NULL , 
    `date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`), 
    INDEX (`cod_employees`), 
    INDEX (`cod_projects`)) 
    ENGINE = InnoDB";

mysqli_query(CommonDB::$connection, $q);


$q = "ALTER TABLE `register` ADD FOREIGN KEY (`cod_employee`) REFERENCES `employees`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
mysqli_query(CommonDB::$connection, $q);

$q = "ALTER TABLE `register` ADD FOREIGN KEY (`cod_project`) REFERENCES `projects`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
mysqli_query(CommonDB::$connection, $q);

if (intval(mysqli_num_rows(mysqli_query(CommonDB::$connection, "SELECT id FROM projects"))) > 0) {
    mysqli_query(CommonDB::$connection, "INSERT INTO projects (name) VALUES ('Mars Rover'),('Manhattan')");
}

if (intval(mysqli_num_rows(mysqli_query(CommonDB::$connection, "SELECT id FROM employees"))) > 0) {
    mysqli_query(CommonDB::$connection, "INSERT INTO employees (name) VALUES ('Mario'),('Giovanni'),('Lucia')");
}

if (intval(mysqli_num_rows(mysqli_query(CommonDB::$connection, "SELECT id FROM projects"))) > 0) {
    mysqli_query(CommonDB::$connection, "INSERT INTO register (cod_employees, cod_projects, hours) VALUES (1,1,5),(2,2,3),(1,1,3),(1,3,3),(2,1,2),(2,2,4)");
}
