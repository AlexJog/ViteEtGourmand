<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';

function sendWelcomeMail(string $to, string $subject, string $body): bool
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) getenv('SMTP_PORT');

        $from = getenv('MAIL_FROM');
        if (!$from) {
            error_log("MAIL_FROM manquant (Config Vars Heroku)");
            return false;
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($from, 'Vite & Gourmand');
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("PHPMailer error: " . $mail->ErrorInfo);
        return false;
    }
}
