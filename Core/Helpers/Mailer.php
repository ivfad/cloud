<?php

namespace Core\Helpers;

use Config\MailerConfig;
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
    private mixed $config;

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
            if (!isset($this->config)) {
                $this->add_config();
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

            return;
        } catch (ContainerExceptionInterface $e) {
            throw new MailerException("Mailer creation exception: " . $e->getMessage());
        }
    }

    /**
     * Preloads mailer config and saves it in $config paramter.
     * @return void
     * @throws ContainerExceptionInterface
     */
    private function add_config(): void
    {
        require_once BASE_PATH . 'Config/MailerConfig.php';

        App::bind(MailerConfig::class, function () {
            return new MailerConfig();
        });

        $this->config = App::get(MailerConfig::class);
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
            $this->mailer->Host = $this->config->host;
            $this->mailer->SMTPAuth = true;
            $this->mailer->SMTPDebug = 2;
            $this->mailer->Username = $this->config->username;
            $this->mailer->Password = $this->config->password;
            $this->mailer->SMTPSecure = $this->config->smtpSecure;
            $this->mailer->Port = $this->config->port;
            $this->mailer->setFrom($this->config->sendFromEmail, $this->config->sendFromName);
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
