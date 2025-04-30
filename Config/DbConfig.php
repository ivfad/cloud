<?php

namespace Config;

class DbConfig
{
    /**
     * Configuration of database
     */
    public function __construct(
        public string $host,
        public int    $port,
        public string $dbname,
        public string $charset,
    )
    {
    }
    
    /**
     * Inits basic DB tables, if necessary
     * @return string
     */
    public function init(): string
    {
        return implode(' ', [
            $this->initUser(),
            $this->initFolder(),
            $this->initFile(),
            $this->initAccess(),
        ]);
    }

    /**
     * Inits `user` table
     * @return string
     */
    public function initUser(): string
    {
        return "CREATE TABLE IF NOT EXISTS `user` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL DEFAULT 'User',
            `email` varchar(255) NOT NULL,
            `admin` tinyint(1) NOT NULL DEFAULT 0,
            `age` tinyint(3) DEFAULT NULL,
            `gender` enum('M', 'F') DEFAULT NULL,
            `password` varchar(255) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `user_email_idx` (`email`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

    /**
     * Inits `folder` table
     * @return string
     */
    public function initFolder(): string
    {
        return "CREATE TABLE IF NOT EXISTS `folder` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `owner_id` int(11) NOT NULL,
            `parent_id` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `folder_user_id_fk` (`owner_id`),
            KEY `folder_folder_FK` (`parent_id`),
            CONSTRAINT `folder_folder_FK` FOREIGN KEY (`parent_id`) REFERENCES `folder` (`id`) ON DELETE CASCADE,
            CONSTRAINT `folder_user_id_fk` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`))
            ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

    /**
     * Inits `file` table
     * @return string
     */
    public function initFile(): string
    {
        return "CREATE TABLE IF NOT EXISTS `file` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `filename` varchar(255) NOT NULL,
            `unique_name` varchar(255) NOT NULL,
            `owner_id` int(11) NOT NULL,
            `folder_id` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `file_folder_id_fk` (`folder_id`),
            KEY `file_user_id_fk` (`owner_id`),
            CONSTRAINT `file_folder_id_fk` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`id`) ON DELETE CASCADE,
            CONSTRAINT `file_user_id_fk` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE)
            ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

    /**
     * Inits `access` table
     * @return string
     */
    public function initAccess(): string
    {
        return "CREATE TABLE IF NOT EXISTS `access` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `file_id` int(11) DEFAULT NULL,
            `folder_id` int(11) DEFAULT NULL,
            `status` enum('Owner','User') DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `access_file_FK` (`file_id`),
            KEY `access_folder_FK` (`folder_id`),
            KEY `access_user_FK` (`user_id`),
            CONSTRAINT `access_file_FK` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE CASCADE,
            CONSTRAINT `access_folder_FK` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`id`) ON DELETE CASCADE,
            CONSTRAINT `access_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`))
            ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

}

