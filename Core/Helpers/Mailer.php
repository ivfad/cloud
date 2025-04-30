<?php

namespace Core\Helpers;

use Core\App;
use Core\Exceptions\MailerException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerExceptionInterface;

class Mailer
{
    /**
     * Mailer class.
     * Processes setting mailer-config, mail content and sending email with PHPMailer.
     */

    private mixed $mailer;

    public function __construct()
    {
    }

    /**
     * Creates mailer instance (if necessary), loads mailer-config (if necessary),
     * sets config-params and content to mailer. Sends email with PHPMailer.
     * @param $content
     * @return void
     * @throws MailerException
     */
    public function send_email($content): void
    {
        try {
            if (!isset($this->mailer)) {
                $this->create_mailer();
            }

            $this->set_config();
            $this->set_content($content);

            if (!$this->mailer->send()) {
                throw new MailerException("Mailer exception: " . $this->mailer->ErrorInfo);
            }

            return;
        } catch (ContainerExceptionInterface $e) {
            throw new MailerException($e->getMessage());
        } catch (MailerException $e) {
            throw new MailerException($e->getMessage());
        } catch (Exception $e) {
            throw new MailerException("Mailer exception: " . $this->mailer->ErrorInfo);
        }
    }

    /**
     * Creates PHPMailer instance.
     * @return void
     * @throws MailerException
     */
    private function create_mailer(): void
    {
        try {
            App::bind(PHPMailer::class, function () {
                return new PHPMailer();
            });
            $this->mailer = App::get(PHPMailer::class);
        } catch (ContainerExceptionInterface $e) {
            throw new MailerException("Mailer creation exception: " . $e->getMessage());
        }
    }

    /**
     * Sets parameters from added config to mailer.
     * @return void
     * @throws MailerException
     */
    private function set_config(): void
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = getenv('MAILER_HOST');
            $this->mailer->SMTPAuth = true;
            $this->mailer->SMTPDebug = 2;
            $this->mailer->Username = getenv('MAILER_USER');
            $this->mailer->Password = getenv('MAILER_PASSWORD');
            $this->mailer->SMTPSecure = getenv('MAILER_SMTP_SECURE');
            $this->mailer->Port = getenv('MAILER_PORT');
            echo getenv("DB_USER");
            $this->mailer->setFrom(getenv('MAILER_SEND_FROM_EMAIL'), getenv('MAILER_SEND_FROM_NAME'));
            echo getenv('SMTP_HOST');
        } catch (Exception $e) {
            throw new MailerException('MailerException: ' . $this->mailer->ErrorInfo);
        }
    }

    /**
     * Sets content of the email.
     * @param array $content
     * @return void
     * @throws MailerException
     */
    public function set_content(array $content): void
    {
        try {
            $this->mailer->addAddress($content['address'], $content['name']);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $content['subject'];
            $this->mailer->Body = $content['body'];
            $this->mailer->AltBody = $content['altbody'];
        } catch (Exception $e) {
            throw new MailerException('MailerException: ' . $this->mailer->ErrorInfo);
        }
    }
}
