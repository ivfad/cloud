<?php

namespace Config;

class DbConfig
{
    /**
     * Username and password to connect to your DB
     */
    private string $username = 'root';
    private string $password = '';

    /**
     * Configuration of database
     */
    public function __construct(
        public string $host = 'localhost',
        public int    $port = 3306,
        public string $dbname = 'cloud-storage',
        public string $charset = 'utf8mb4')
    {
    }

    public function getPass(): string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Inits basic DB tables, if necessary
     * @return string
     */
    public function init(): string
    {
        return "CREATE TABLE IF NOT EXISTS `user` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL DEFAULT '',
            `email` varchar(255) NOT NULL,
            `admin` tinyint(1) NOT NULL DEFAULT 0,
            `age` tinyint(3) DEFAULT NULL,
            `gender` enum('M', 'F') DEFAULT NULL,
            `password` varchar(255) NOT NULL,
            PRIMARY KEY `id` (`id`),
            UNIQUE KEY `user_email_idx` (`email`) USING BTREE) 
            ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    }
}
