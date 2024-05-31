USE `crm-beauty-parlor`;

CREATE TABLE `barber` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `name` varchar(50) NOT NULL,
                          `surname` varchar(50) NOT NULL,
                          `phone` varchar(50) DEFAULT NULL,
                          `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          `deleted_at` datetime DEFAULT NULL,
                          PRIMARY KEY (`id`)
);

CREATE TABLE `client` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `name` varchar(50) NOT NULL,
                          `surname` varchar(50) NOT NULL,
                          `phone` varchar(50) DEFAULT NULL,
                          `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
);

CREATE TABLE `appointment` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `barber_id` int NOT NULL,
                               `client_id` int NOT NULL,
                               `date` datetime NOT NULL,
                               `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                               `deleted_at` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               KEY `appointment_barber_id_fk` (`barber_id`),
                               KEY `appointment_client_id_fk` (`client_id`),
                               CONSTRAINT `appointment_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
                               CONSTRAINT `appointment_barber_id_fk` FOREIGN KEY (`barber_id`) REFERENCES `barber` (`id`)
);