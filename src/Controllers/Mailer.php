<?php
namespace App\Controllers;
use Core\App;
//use Core\Exceptions\ContainerExceptionInterface;
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

    public function send_email($content)
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
    public function create_mailer()
    {
        try{
            App::bind(PHPMailer::class, function() {
                return new PHPMailer();
            });
            $this->mailer = App::get(PHPMailer::class);
            return $this->mailer;
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    public function add_config()
    {
        try {
            App::bind(ConfigMailer::class, function () {
                return new ConfigMailer();
            });
            $this->config = App::get(ConfigMailer::class);
            return $this->mailer;
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    public function set_content($content)
    {
        try {
            $this->mailer->addAddress($content['address'], $content['name']);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $content['subject'];
            $this->mailer->Body = $content['body'];
            $this->mailer->AltBody = $content['altbody'];
        } catch (Exception $e) {
            echo "Сообщение не было отправлено. Ошибка: {$this->mailer->ErrorInfo}";
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    public function set_config()
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
            echo 'Сообщение успешно отправлено'; // Выведем текст об успешной отправке
        } catch (Exception $e) {
            echo "Сообщение не было отправлено. Ошибка: {$this->mailer->ErrorInfo}"; // Если возникнет ошибка при отправке, отобразим текст ошибки
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
            
        }
        }
}
