<?php

namespace App;

header('Content-type: text/html; charset=utf-8');

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail 
{
public static function send($to, $subject, $text, $html)
	{
		date_default_timezone_set('Europe/Warsaw');

		$mail = new PHPMailer(true);
		
		try {
			$mail->isSMTP();
			//$mail->SMTPDebug = 0;
			$mail->Host = 'smtp.gmail.com'; // Gmail SMTP host
			$mail->SMTPAuth = true; // Enable SMTP authentication

			$mail->Username = Config::EMAIL_FROM; // Gmail username (e-mail)
			$mail->Password = Config::EMAIL_PASSWORD; // Gmail password
			$mail->SMTPSecure = 'ssl'; 
			$mail->Port = 465; // Gmail SMTP port

			$mail->CharSet = "UTF-8";

			$mail->setFrom(Config::EMAIL_FROM, 'Personal Budget');
			$mail->addAddress($to);

			$mail->isHTML(true); // Format: HTML
			$mail->Subject = $subject;
			$mail->Body    = $html;
			$mail->AltBody = $text;
			
			$mail->send();
			
		} catch (Exception $e) {
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		}
		//echo 'Message has been sent (OK)';
	}
}