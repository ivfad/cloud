<?php
namespace App\Controllers;
use Core\App;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Psr\Container\ContainerExceptionInterface;

class Mailer
{
    private mixed $mailer;
    private mixed $config;

    public function __construct()
    {
    }

    /**
     * @param $content
     * @return void
     */
    public function send_email($content): void
    {
        try {
            if(!isset($this->mailer)) {
                $this->create_mailer();
            }
            if(!isset($this->config)) {
                $this->add_config();
            }
            $this->set_config();
            $this->set_content($content);
            $this->mailer->send();
        } catch (Exception $e) {
            echo "Mailer exception:  {$this->mailer->ErrorInfo}";
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    /**
     * @throws \Core\Exceptions\ContainerException
     * @throws \Core\Exceptions\ContainerNotFoundException
     */
    private function create_mailer(): void
    {
        try{
            App::bind(PHPMailer::class, function() {
                return new PHPMailer();
            });
            $this->mailer = App::get(PHPMailer::class);
            return;
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    private function add_config(): void
    {
        try {
            App::bind(ConfigMailer::class, function () {
                return new ConfigMailer();
            });
            $this->config = App::get(ConfigMailer::class);
            return;
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    public function set_content($content): void
    {
        try {
            $this->mailer->addAddress($content['address'], $content['name']);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $content['subject'];
            $this->mailer->Body = $content['body'];
            $this->mailer->AltBody = $content['altbody'];
        } catch (Exception $e) {
            echo "Problems with mail message content. Error: {$this->mailer->ErrorInfo}";
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    public function set_config(): void
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
            echo "Mailer config was not set. Error: {$this->mailer->ErrorInfo}";
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
            
        }
    }
}
