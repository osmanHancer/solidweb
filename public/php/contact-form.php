<?php


ini_set('allow_url_fopen', true);

session_cache_limiter('nocache');
header('Expires: ' . gmdate('r', 0));

header('Content-type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'php-mailer/src/PHPMailer.php';
require 'php-mailer/src/SMTP.php';
require 'php-mailer/src/Exception.php';


    $mail = new PHPMailer(true);
    //
    try {
        //SMTP Sunucu Ayarları
        $mail->SMTPDebug = 0; // DEBUG Kapalı: 0, DEBUG Açık: 2
        $mail->isSMTP();
        $mail->Host       = 'smtp.yandex.com'; // Email sunucu adresi.
        $mail->SMTPAuth   = true; // SMTP kullanici dogrulama kullan
        $mail->Username   = 'noreply@solidelectron.com'; // SMTP sunucuda tanimli email adresi
        $mail->Password   = 'AQeSuf6nEXEtE'; // SMTP email sifresi
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL icin `PHPMailer::ENCRYPTION_SMTPS` kullanin. SSL olmadan 587 portundan gönderim icin `PHPMailer::ENCRYPTION_STARTTLS` kullanin
        $mail->Port       = 465; // Eger yukaridaki deger `PHPMailer::ENCRYPTION_SMTPS` ise portu 465 olarak guncelleyin. Yoksa 587 olarak birakin
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
Gönderen: $_POST[name]
Mail: $_POST[email]
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
            $mail->addAddress($_POST["email"],"solidelectron.com giden  ". $_POST["name"]); // Alıcı bilgileri
            $mail->addReplyTo("info@solidelectron.com", "Solid Support"); // Alıcı'nın emaili yanıtladığında farklı adrese göndermesini istiyorsaniz aktif edin
            $mail->send();
             $mail->clearReplyTos();
             $mail->clearAddresses();
             $mail->addAddress("info@solidelectron.com", "Solid Support"); // Alıcı bilgileri
            $mail->addReplyTo($_POST["email"], $_POST["name"]); // Alıcı'nın emaili yanıtladığında farklı adrese göndermesini istiyorsaniz aktif edin
            $mail->send();
            echo "success";
        }
    } catch (Exception $e) {
        echo "Ops! Email iletilemedi. Hata: {$mail->ErrorInfo}";
    }

// Step 1 - Enter your email address below.
