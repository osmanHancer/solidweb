<?php
/*
Name: 			Contact Form - Google Recaptcha v2
Written by: 	Okler Themes - (http://www.okler.net)
Theme Version:	9.9.2
*/

namespace PortoContactForm;

ini_set('allow_url_fopen', true);

header('Expires: ' . gmdate('r', 0));

header('Content-type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'php-mailer/src/PHPMailer.php';
require 'php-mailer/src/SMTP.php';
require 'php-mailer/src/Exception.php';

if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
	$arrResult = array('response' => 'error', 'errorMessage' => 'Please click on the reCAPTCHA box.');
	exit(json_encode($arrResult));
}

// Your Google reCAPTCHA generated Secret Key here
$secret = '6LfuBc8pAAAAAFu1GlWy8OAsviZbW_-G2JtXXHy_';

if (ini_get('allow_url_fopen')) {
	//reCAPTCHA - Using file_get_contents()
	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
	$responseData = json_decode($verifyResponse);
} else if (function_exists('curl_version')) {
	// reCAPTCHA - Using CURL
	$fields = array(
		'secret'    =>  $secret,
		'response'  =>  $_POST['g-recaptcha-response'],
		'remoteip'  =>  $_SERVER['REMOTE_ADDR']
	);

	$verifyResponse = curl_init("https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($verifyResponse, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($verifyResponse, CURLOPT_TIMEOUT, 15);
	curl_setopt($verifyResponse, CURLOPT_POSTFIELDS, http_build_query($fields));
	$responseData = json_decode(curl_exec($verifyResponse));
	curl_close($verifyResponse);
} else {
	$arrResult = array('response' => 'error', 'errorMessage' => 'You need CURL or file_get_contents() activated in your server. Please contact your host to activate.');
	echo json_encode($arrResult);
}

if (!$responseData->success) {

	$arrResult = array('response' => 'error', 'errorMessage' => 'Robot verification failed, please try again');
	echo json_encode($arrResult);
	die();
}

$mail = new PHPMailer(true);
//
try {
	//SMTP Sunucu Ayarları
	$mail->SMTPDebug = 0; // DEBUG Kapalı: 0, DEBUG Açık: 2
	$mail->isSMTP();
	$mail->Host       = 'smtp.office365.com'; // Email sunucu adresi.
	$mail->SMTPAuth   = true; // SMTP kullanici dogrulama kullan
	$mail->Username   = 'noreply@solidelectron.com'; // SMTP sunucuda tanimli email adresi
	$mail->Password   = 'AQeSuf6nEXEtE'; // SMTP email sifresi

	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // SSL icin `PHPMailer::ENCRYPTION_SMTPS` kullanin. SSL olmadan 587 portundan gönderim icin `PHPMailer::ENCRYPTION_STARTTLS` kullanin
	$mail->Port       = 587; // Eger yukaridaki deger `PHPMailer::ENCRYPTION_SMTPS` ise portu 465 olarak guncelleyin. Yoksa 587 olarak birakin
	$mail->setFrom('noreply@solidelectron.com', 'solidelectron.com - ' . $_POST["name"]); // Gonderen bilgileri yukaridaki $mail->Username ile aynı deger olmali

	//Alici Ayarları
	// $mail->addAddress("info@solidelectron.com", "Solid Support"); // Alıcı bilgileri

	//$mail->addCC('CC@domainadi.com');
	// $mail->addBCC($_POST["email"], 'solidelectron.com - ' . $_POST["name"]);
	// Mail Ekleri
	//$mail->addAttachment('https://cdn.domainhizmetleri.com/var/tmp/file.tar.gz'); // Attachment ekleme



	$message = "
		<pre>
	İletişim formundan gönderilen mesaj:
	Gönderen: $_POST[name] <$_POST[email] >
	
	------------------------
	$_POST[message]
	-----------------------
	 </pre>
		";
	$mail->isHTML(true); // Gönderimi HTML türde olsun istiyorsaniz TRUE ayarlayin. Düz yazı (Plain Text) icin FALSE kullanin
	$mail->CharSet = 'utf-8';
	$mail->Subject = $_POST["subject"];
	$mail->Body    = $message;

	if (empty($mail->Subject))
		echo "Ops! Email iletilemedi.Konuyu Hata: {$mail->ErrorInfo}";
	else {
		$mail->addAddress($_POST["email"], "solidelectron.com giden  " . $_POST["name"]); // Alıcı bilgileri
		$mail->addReplyTo("info@solidelectron.com", "Solid Support"); // Alıcı'nın emaili yanıtladığında farklı adrese göndermesini istiyorsaniz aktif edin
		$mail->send();
		$mail->clearReplyTos();
		$mail->clearAddresses();
		$mail->addAddress("info@solidelectron.com", "Solid Support"); // Alıcı bilgileri
		$mail->addReplyTo($_POST["email"], $_POST["name"]); // Alıcı'nın emaili yanıtladığında farklı adrese göndermesini istiyorsaniz aktif edin
		$mail->send();
		$arrResult = array('response' => 'success');
		echo json_encode($arrResult);
	}
} catch (Exception $e) {
	$arrResult = array('response' => 'error', 'errorMessage' => 'SMTP Error', 'e' => $e->getMessage());
	echo json_encode($arrResult);
}
