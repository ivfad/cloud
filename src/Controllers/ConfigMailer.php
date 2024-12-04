<?php
namespace App\Controllers;
use PHPMailer\PHPMailer\PHPMailer;

class ConfigMailer
{
    /**
     * Configuration of mailer
     */
    public function __construct(
        public string $host = 'smtp.mail.ru',
        public string $username = 'ivfad91@mail.ru', //SMTP username
        public string $password = '9bjTWGi1RHskHaZ7PsFT', //SMTP pass
        public string $smtpSecure = PHPMailer::ENCRYPTION_STARTTLS, //encryption settings
        public int $port = 587,
        public string $sendFromEmail = 'ivfad91@mail.ru',
        public string $sendFromName = 'Cloud storage',
        public string $mailSubject = 'Link to change your password from Cloud storage')
    {
    }
}
