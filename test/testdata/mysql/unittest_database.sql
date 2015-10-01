-- create the database
CREATE DATABASE unittest /*!40100 DEFAULT CHARACTER SET utf8 */;

-- create the user
CREATE USER 'unittest'@'localhost' IDENTIFIED BY 'test123123';

-- give all permissions for user on unittest database
GRANT ALL ON unittest.* TO 'unittest'@'localhost';

-- create the first test table will be used in the orm tests as well
CREATE TABLE `test_table1` (`id` INT(10) PRIMARY KEY AUTO_INCREMENT, `aString` VARCHAR(255), `aDate` DATETIME);

-- insert the first basic testdata
INSERT INTO `test_table1` (`aString`,`aDate`) VALUES ('a', NOW()), ('b', NOW()), ('c', NOW());