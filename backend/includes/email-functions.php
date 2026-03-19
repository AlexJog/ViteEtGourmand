<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerEmail($destinataire, $nom_destinataire, $sujet, $message_texte) {
    require __DIR__ . '/../../../vendor/autoload.php';
    
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER');
        $mail->Password = getenv('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        $expediteur = getenv('MAIL_FROM') ?: 'alexdu6610@gmail.com';
        $mail->setFrom($expediteur, 'Vite & Gourmand');
        $mail->addAddress($destinataire, $nom_destinataire);
        
        $mail->isHTML(false);
        $mail->Subject = $sujet;
        $mail->Body = $message_texte;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur email: {$mail->ErrorInfo}");
        return false;
    }
}
?>