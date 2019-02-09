<?php
require_once dirname(__FILE__).'./lib/fyre_mail.php';

$mail_options = [
	'from' => 'contact@vgsw.org',
	'to' => 'uprightech@gmail.com',
	'subject'=> 'Test Subject'
];

$message = '<p>This is a test message </p>';
$error = '';

if(fyre_send_html_mail($message,$mail_options,$error)==true) {

	echo "Message sent";
}else {
	echo "Message not sent : ${error}";
}