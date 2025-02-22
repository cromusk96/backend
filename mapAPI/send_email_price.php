<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $location = $_POST["location"];
    $numberOfParcels = $_POST["numberOfParcels"];
    $numberOf360Images = $_POST["numberOf360Images"];
    $pmsIntegration = $_POST["pmsIntegration"];
    $phobsIntegration = $_POST["phobsIntegration"];
    $navigation = $_POST["navigation"];
    $images360 = $_POST["images360"];
    $manualAttributeEntry = $_POST["manualAttributeEntry"];
    $translate = $_POST["translate"];
    $geoReferencing = $_POST["geoReferencing"];
    $detailedCampDrawing = $_POST["detailedCampDrawing"];
    $dronePhotos = $_POST["dronePhotos"];
    $virtualWalks = $_POST["virtualWalks"];
    $pushNotifications = $_POST["pushNotifications"];
    $hosting360Images = $_POST["hosting360Images"];
    $fullPrice = $_POST["fullPrice"];

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
        $mail->From = 'campsabout@tri-m.app';
        $mail->FromName = "Campsabout";
        $mail->addAddress($email, 'Guest');
        $mail->addAddress('sandi.ivanusec@tri-m.hr', 'Sandi Ivanusec');     // BCC Sandiju
        $mail->addReplyTo('sandi.ivanusec@tri-m.hr', 'Sandi Ivanusec');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Camp Pricing';
        $mail->Body    = '
            <!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pricing calculation</h1>
        <p>Thank you for using our pricing calculator. Here are the details of your request:</p>
        <br>
        <p>Location: <strong>' . $location . '</strong></p>
        <p>Number of Parcels: <strong>' . $numberOfParcels . '</strong></p>
        <p>Number of 360 Images: <strong>' . $numberOf360Images . '</strong></p>
        <br>
        <p>Selected Additional Features:</p>
        <ul>
            <li>Integration with PMS: <strong>' . $pmsIntegration . '</strong></li>
            <li>Integration with the Booking system (Phobs): <strong>' . $phobsIntegration . '</strong></li>
            <li>Navigation: <strong>' . $navigation . '</strong></li>
            <li>360 images: <strong>' . $images360 . '</strong></li>
            <li>Manual entry of entity attributes on map: <strong>' . $manualAttributeEntry . '</strong></li>
            <li>Translation into languages: <strong>' . $translate . '</strong></li>
            <li>Collection of points of interest/georeferencing: <strong>' . $geoReferencing . '</strong></li>
            <li>Detailed drawing of the camp: <strong>' . $detailedCampDrawing . '</strong></li>
            <li>Photographing the camp with a drone: <strong>' . $dronePhotos . '</strong></li>
            <li>Virtual walks: <strong>' . $virtualWalks . '</strong></li>
            <li>Push Notification: <strong>' . $pushNotifications . '</strong></li>
            <li>360 image hosting: <strong>' . $hosting360Images . '</strong></li>
        </ul>
        <p>Total Price: <strong>' . $fullPrice . ' EUR</strong></p>
        <br>
        <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
        <br>
        <p>Best regards,</p>
    </div>
</body>
</html>';
        $mail->AltBody = "Name: Guest\nEmail: $email\nSubject: Camp Pricing";

        $mail->send();
        echo 'Email sent successfully';
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
