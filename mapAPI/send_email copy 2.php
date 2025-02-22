<?php

// Import the Postmark Client Class:
require_once('./vendor/autoload.php');
use Postmark\PostmarkClient;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    $client = new PostmarkClient("33ce7434-c479-4d4b-8ddb-ef8b8af22f02");
    $fromEmail = "josip.maric@tri-m.hr";
    $toEmail = "josip.maric@tri-m.hr";
    $ccEmail = "sandi.ivanusec@tri-m.hr";
    $subject = $subject;
    $htmlBody = "<strong>Name:</strong> $name<br><strong>Email:</strong> $email<br><strong>Subject:</strong> $subject<br><strong>Message:</strong><br>$message";
    $textBody = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";
    $tag = "contact-form";
    $trackOpens = true;
    $trackLinks = "None";
    $messageStream = "outbound";

    try {
        // Send an email:
        $sendResult = $client->sendEmail(
            $fromEmail,
            $toEmail,
            $subject,
            $htmlBody,
            $textBody,
            $tag,
            $trackOpens,
            NULL, // Reply To
            $ccEmail, // CC
            NULL, // BCC
            NULL, // Header array
            NULL, // Attachment array
            $trackLinks,
            NULL, // Metadata array
            $messageStream
        );

        echo "Email sent successfully.";
    } catch (Exception $e) {
        echo "Failed to send email: " . $e->getMessage();
    }
}
?>
