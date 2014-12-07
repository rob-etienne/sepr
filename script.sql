CREATE DATABASE sepr_project;
 
USE sepr_project;

CREATE TABLE IF NOT EXISTS `advisors` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`employee_nr` int(11) NOT NULL,
	`password_hash` varchar(225) NOT NULL,
	`active` tinyint(1) NOT NULL DEFAULT 0,
	`sign_up_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`last_sign_in_stamp` timestamp NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `employee_nr` (`employee_nr`)
);
	
CREATE TABLE IF NOT EXISTS `clients` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`first_name` varchar(50) NOT NULL,
	`last_name` varchar(50) NOT NULL,
	`email` varchar(150) NOT NULL,
	`password_hash` varchar(225) NOT NULL,
	`active` tinyint(1) NOT NULL DEFAULT 0,
	`sign_up_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`last_sign_in_stamp` timestamp NOT NULL,
	`advisor_id` int(11) NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`),
  	UNIQUE KEY `email` (`email`)
);

CREATE TABLE IF NOT EXISTS `messages` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`subject` varchar(50) NOT NULL,
	`message` varchar(150) NOT NULL,
	`attachment_url` varchar(200) DEFAULT NULL,
	`submitted_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`client_id` int(11) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `accounts` (
	`id` int(11) NOT NULL,
	`name` varchar(150) NOT NULL,
	`balance` varchar(150) NOT NULL DEFAULT 0,
	`added_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`client_id` int(11) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `transactions` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`account_id_to` int(11) NOT NULL,
	`amount` varchar(150) NOT NULL,
	`purpose` varchar(140) NOT NULL,
	`date_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `account_id_to` (`account_id_to`)
);

CREATE TABLE IF NOT EXISTS `account_transaction_matches` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`transaction_id` int(11) NOT NULL,
	`account_id_to` int(11) NOT NULL,
	`account_id_from` int(11) NOT NULL,
	PRIMARY KEY (`id`),
  	KEY `transaction_id` (`transaction_id`),
	KEY `account_id_to` (`account_id_to`),
	KEY `account_id_from` (`account_id_from`)
);
	
ALTER TABLE `sepr_project`.`clients` ADD FOREIGN KEY ( `advisor_id` ) REFERENCES  `sepr_project`.`advisors` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `sepr_project`.`messages` ADD FOREIGN KEY ( `client_id` ) REFERENCES  `sepr_project`.`clients` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `sepr_project`.`accounts` ADD FOREIGN KEY ( `client_id` ) REFERENCES  `sepr_project`.`clients` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `sepr_project`.`transactions` ADD FOREIGN KEY ( `account_id_to` ) REFERENCES  `sepr_project`.`accounts` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `sepr_project`.`account_transaction_matches` ADD FOREIGN KEY ( `transaction_id` ) REFERENCES  `sepr_project`.`transactions` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `sepr_project`.`account_transaction_matches` ADD FOREIGN KEY ( `account_id_to` ) REFERENCES  `sepr_project`.`accounts` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `sepr_project`.`account_transaction_matches` ADD FOREIGN KEY ( `account_id_from` ) REFERENCES  `sepr_project`.`accounts` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

/* Insert dummy advisors */
INSERT INTO `sepr_project`.`advisors`(`employee_nr`, `password_hash`) VALUES ('1234','5f4dcc3b5aa765d61d8327deb882cf99');
INSERT INTO `sepr_project`.`advisors`(`employee_nr`, `password_hash`) VALUES ('4321','5f4dcc3b5aa765d61d8327deb882cf99');


/* Insert dummy clients */
INSERT INTO `sepr_project`.`clients` (`first_name`, `last_name`, `email`, `password_hash`, `advisor_id`) VALUES
('Max', 'Scholz', 'max@email.com', '$2y$12$svXdtmNUEqDRf9RrXLtWEOxNTq/f4REh5fAMWefHYOOZJ8o170rye', 2);
INSERT INTO `sepr_project`.`clients` (`first_name`, `last_name`, `email`, `password_hash`, `advisor_id`) VALUES
('Tatsuya', 'Kaneko', 'tatsuya@email.com', '$2y$12$svXdtmNUEqDRf9RrXLtWEOxNTq/f4REh5fAMWefHYOOZJ8o170rye', 1);
INSERT INTO `sepr_project`.`clients` (`first_name`, `last_name`, `email`, `password_hash`, `advisor_id`) VALUES
('Jan', 'Sviland', 'jan@email.com', '$2y$12$svXdtmNUEqDRf9RrXLtWEOxNTq/f4REh5fAMWefHYOOZJ8o170rye', 2);
INSERT INTO `sepr_project`.`clients` (`first_name`, `last_name`, `email`, `password_hash`, `advisor_id`) VALUES
('Rob', 'Etienne', 'rob@email.com', '$2y$12$svXdtmNUEqDRf9RrXLtWEOxNTq/f4REh5fAMWefHYOOZJ8o170rye', 1);
INSERT INTO `sepr_project`.`clients` (`first_name`, `last_name`, `email`, `password_hash`, `advisor_id`) VALUES
('John', 'Greece Last Name', 'john@email.com', '$2y$12$svXdtmNUEqDRf9RrXLtWEOxNTq/f4REh5fAMWefHYOOZJ8o170rye', 1);

/* Insert dummy accounts */
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(1234, 'Private', '500,00', 1);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(2345, 'Business', '500,00', 1);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(3456, 'Private', '500,00', 2);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(4567, 'Study', '500,00', 2);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(5678, 'Private', '500,00', 3);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(6789, 'Hobby', '500,00', 3);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(7890, 'Private', '500,00', 4);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(8901, 'Private', '500,00', 5);
INSERT INTO `sepr_project`.`accounts` (`id`, `name`, `balance`, `client_id`) VALUES 
(9012, 'Private', '500,00', 5);