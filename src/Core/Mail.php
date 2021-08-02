<?php
namespace App\Core;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use App\Exceptions\ConfigException;
use App\Exceptions\MailException;

class Mail {
	private array $mailConfig;

	private array $globalConfig;

	public function __construct() {
		$confDir = CONF_DIR. "/mail.yml";
		$this->mailConfig = yaml_parse_file($confDir);

		if (!$this->mailConfig) {
			throw new ConfigException("Error loading ". $confDir);
		}

		$confDir = CONF_DIR. "/config.yml";
		$this->globalConfig = yaml_parse_file($confDir);

		if (!$this->globalConfig) {
			throw new ConfigException("Error loading ". $confDir);
		}
	}

	public function send(array $from, array $tos, string $subject, string $html, string $text): void {
		$mail = new PHPMailer(true);

		try {
			//Server settings
			if ($this->globalConfig['env'] === "dev") {
				$mail->SMTPDebug = SMTP::DEBUG_SERVER;
			}

			$mail->isSMTP();
			$mail->SMTPSecure = false;
			$mail->SMTPAutoTLS = false;

			$mail->Host = $this->mailConfig['mail_host'];
			$mail->Port = $this->mailConfig['mail_port'];

			if (!empty($this->mailConfig['mail_username']) && !empty($this->mailConfig['mail_password'])) {
				$mail->SMTPAuth = true;
				$mail->Username = $this->mailConfig['mail_username'];
				$mail->Password = $this->mailConfig['mail_password'];
			}

			$mail->CharSet = "utf-8";

			//Recipients
			if (sizeof($from) == 2) {
				$mail->setFrom($from[0], $from[1]);
			} else {
				$mail->setFrom($from[0]);
			}

			foreach ($tos as $to) {
				if (sizeof($to) == 2) {
					$mail->addAddress($to[0], $to[1]);
				} else {
					$mail->addAddress($to[0]);
				}
			}

			//Content
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body = $html;
			$mail->AltBody = $text;

			$mail->send();
		} catch (Exception $e) {
			throw new MailException($mail->ErrorInfo);
		}
	}
}
