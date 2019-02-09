<?php

require_once dirname(__FILE__).'./PHPMailer/Exception.php';
require_once dirname(__FILE__).'./PHPMailer/PHPMailer.php';
require_once dirname(__FILE__).'./PHPMailer/SMTP.php';

define('FYRE_MAIL_CONFIG_FILE',dirname(__FILE__).'./../config/mail.config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * in each case , options is an array containing 
 * the following 
*/

function fyre_send_plain_mail($message,$options,&$error) {
	
	$options['message_type'] = 'html';
	return fyre_send_mail($message,$options,$error);
}

function fyre_send_html_mail($message,$options,&$error) {

	$options['message_type'] = 'text';
	return fyre_send_mail($message,$options,$error);
}

function fyre_send_mail($message,$options,&$error) {

	$mailer = fyre_create_phpmailer();

	$message_type = 'text';
	if(isset($options['message_type']))
		$message_type = $options['message_type'];

	$mailer->isHTML(fyre_message_type_is_html($message_type));

	if(fyre_is_option_set('from',$options)) {
		$from = $options['from'];
		if(is_string($from))
			$mailer->setFrom($from);
		else if(is_array($from))
			$mailer->setFrom($from[0],$from[1]);
	}

	if(fyre_is_option_set('to',$options)) {

		$to = $options['to'];
		if(is_string($to))
			$mailer->addAddress($to);
		else if(is_array($to))
			$mailer->addAddress($to[0],$to[1]);
	}

	if(fyre_is_option_set('subject',$options))
		$mailer->Subject =  $options['subject'];

	
	$mailer->Body = $message;

	if(!$mailer->send()) {
		$error = $mailer->ErrorInfo;
		return false;
	}
	return true;
}

function fyre_message_type_is_html($message_type) {

	return strcmp($message_type,'html') == 0;
}

function fyre_is_option_set($name,$options) {

	return isset($options[$name]);
}

function fyre_create_phpmailer() {

	$mailer = new PHPMailer(false);
	$phpmailer_config = require_once FYRE_MAIL_CONFIG_FILE;

	if(fyre_is_option_set('smtp_hostname',$phpmailer_config))
		$mailer->Host = $phpmailer_config['smtp_hostname'];

	if(fyre_is_option_set('smtp_port',$phpmailer_config))
		$mailer->Port = $phpmailer_config['smtp_port'];

	if(fyre_is_option_set('smtp_username',$phpmailer_config)) {

		$mailer->SMTPAuth = true;
		$mailer->Username = $phpmailer_config['smtp_username'];
	}

	if(fyre_is_option_set('smtp_password',$phpmailer_config)) {

		$mailer->SMTPAuth = true;
		$mailer->Password = $phpmailer_config['smtp_password'];
	}

	if(fyre_is_option_set('smtp_secure',$phpmailer_config)) {

		$mailer->SMTPSecure = $phpmailer_config['smtp_secure'];
	}

	if(fyre_is_option_set('method',$phpmailer_config)) {
		$method = $phpmailer_config['method'];
		if(strcmp($method,'smtp')==0)
			$mailer->isSMTP();
		else if(strcmp($method,'mail')==0)
			$mail->isMail();
		else if(strcmp($method,'sendmail')==0)
			$mail->isSendMail();
		else
			$mail->isSMTP(); // default
	}

	return $mailer;
}
