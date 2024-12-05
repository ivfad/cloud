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
        public string $username = 'test@mail.ru', //SMTP username
        public string $password = 'testpass', //SMTP pass
        public string $smtpSecure = PHPMailer::ENCRYPTION_STARTTLS, //encryption settings
        public int $port = 587,
        public string $sendFromEmail = 'sender@mail.ru',
        public string $sendFromName = 'Cloud storage',
        public string $mailSubject = 'Link to change your password from Cloud storage')
    {
    }
}
