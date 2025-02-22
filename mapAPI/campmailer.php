<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $qrlink = $_POST["qrlink"];

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
        $mail->setFrom('campsabout@tri-m.app', 'Campsabout');
        $mail->addAddress($email);     

        //Content
        $mail->isHTML(true);                                  
        $mail->Subject = 'Your personal link to your accommodation';
        $mail->Body    = '
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
                        .link {
                            display: inline-block;
                            padding: 10px 20px;
                            background-color: #007bff;
                            color: #ffffff;
                            text-decoration: none;
                            border-radius: 4px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Your Personal Accommodation Link</h1>
                        <p>Click the button below to access your accommodation:</p>
                        <a class="link" href="' . $qrlink . '">Access Accommodation</a>
                    </div>
                </body>
            </html>';
        $mail->AltBody = 'Your personal accommodation link: ' . $qrlink;

        $mail->send();
        echo 'Email sent successfully';
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
