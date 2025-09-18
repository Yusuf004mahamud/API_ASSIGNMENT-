<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Only run the script if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dotenv = Dotenv\Dotenv::createImmutable(DIR);
    $dotenv->load();

          // Load database credentials securely from the .env file
$servername = $_ENV['DB_HOST'];
$username   = $_ENV['DB_USER'];
$password   = $_ENV['DB_PASS'];
$port       = $_ENV['DB_PORT'];
$dbname     = $_ENV['DB_NAME'];



    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    

    // Get the email and name from the user's form submission
    $userEmail = $_POST['email'];
    $userName = $_POST['name'];

    // Use the validation logic
    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) { 
        
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['yusuf.mahamud@strathmore.edu'];
            $mail->Password   = $_ENV['ktcw zxig vxnt irip'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom('ics2.2@noreply.com', 'ICS 2.2');
            $mail->addAddress($userEmail, $userName);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to ICS 2.2! Account Verification';
            $mail->Body    = "Hello " . htmlspecialchars($userName) . ",<br><br>" .
                             "You requested an account on ICS 2.2.<br><br>" .
                             "In order to use this account you need to <a href='#'>Click Here</a> to complete the registration process.<br><br>" .
                             "Regards,<br>Systems Admin<br>ICS 2.2";
            
            $mail->send();
            echo 'Message has been sent successfully!';

            // INSERT USER INTO DATABASE
            $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $userName, $userEmail);

            if ($stmt->execute()) {
                echo "<br>User registered successfully in the database.";
            } else {
                echo "<br>Error: " . $stmt->error;
            }
            $stmt->close();
            
            
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        
    } else {
        echo "Invalid email address."; 
    }
    $conn->close(); // Close the database connection
}
?>
