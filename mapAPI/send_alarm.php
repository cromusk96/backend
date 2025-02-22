<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $name = $data["name"] ?? '';
    $emails = $data["email"] ?? [];
    $subject = $data["subject"] ?? '';
    $message = $data["message"] ?? '';

    // Ensure emails is an array
    if (!is_array($emails)) {
        $emails = [$emails];
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'tri-m.app';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'campsabout@tri-m.app';
        $mail->Password   = '-MCj4HbF]Gw;';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('campsabout@tri-m.app', 'Alarm');
        $mail->addAddress('josip.maric@tri-m.hr', 'Josip Maric');
        
        // Adding multiple email recipients
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($email, 'Alarm');
            }
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = "Name: $name\nEmails: " . implode(", ", $emails) . "\nSubject: $subject\nMessage:\n$message";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Email sent successfully"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
}
?>
