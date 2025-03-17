<?php

namespace Config;

use PHPMailer\PHPMailer\PHPMailer;

class MailerConfig
{
    /**
     * Configuration of mailer
     * Insert your host, username, password, port and sendFromEmail parameters
     */
    public function __construct(
        public string $host = 'smtp.gmail.com',
        public string $username = 'example-email@gmail.com', //SMTP username
        public string $password = 'examplePassword', //SMTP email password
        public string $smtpSecure = PHPMailer::ENCRYPTION_STARTTLS, //encryption settings
        public int    $port = 587,
        public string $sendFromEmail = 'example-email@gmail.com',
        public string $sendFromName = 'Cloud storage',
        public string $mailSubject = 'Link to change your password from Cloud storage')
    {
    }
}
