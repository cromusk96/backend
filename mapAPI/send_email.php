<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                                 
        $mail->isSMTP();                                      
        $mail->Host       = 'tri-m.app';               
        $mail->SMTPAuth   = true;                             
        $mail->Username   = 'campsabout@tri-m.app';               
        $mail->Password   = '-MCj4HbF]Gw;';                         
        $mail->SMTPSecure = 'tls';                            
        $mail->Port       = 587;                              

        //Recipients
        $mail->setFrom('campsabout@tri-m.app', $name);
        $mail->addAddress('josip.maric@tri-m.hr', 'Josip Maric');
        $mail->addAddress('sandi.ivanusec@tri-m.hr', 'Sandi Ivanusec');     
        $mail->addReplyTo($email, $name);

        //Content
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = "<strong>Name:</strong> $name<br><strong>Email:</strong> $email<br><strong>Subject:</strong> $subject<br><strong>Message:</strong><br>$message";
        $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";

        $mail->send();
        echo 'Email sent successfully';
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
