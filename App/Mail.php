<?php

namespace App;

header('Content-type: text/html; charset=utf-8');

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail 
{
public static function send($to, $subject, $text, $html)
	{
		date_default_timezone_set('Europe/Warsaw');

		$mail = new PHPMailer(true);

		 $mail->isSMTP();
		 $mail->SMTPDebug = 0;
		 $mail->Host = 'smtp.gmail.com'; // Gmail SMTP host
		 $mail->SMTPAuth = true; // Enable SMTP authentication
		 
		 $mail->Username = "your.personal.budget@gmail.com"; // Gmail username (e-mail)
		 $mail->Password = "gzdatxnojexkadjp"; // Gmail password
		 $mail->SMTPSecure = 'ssl'; 
		 $mail->Port = 465; // Gmail SMTP port

		 $mail->CharSet = "UTF-8";
		 $mail->setLanguage('pl', '/phpmailer/language');

		 $mail->setFrom(Config::EMAIL_FROM, 'Personal Budget');
		 $mail->addAddress($to);

		 $mail->isHTML(true); // Format: HTML
		 $mail->Subject = $subject;
			$mail->Body    = $html;
			$mail->AltBody = $text;

		 if(!$mail->Send()) {
			echo 'Some error...';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			exit;
		}

		//echo 'Message has been sent (OK)';
	}
}