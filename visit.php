<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $number = isset($_POST['number']) ? htmlspecialchars($_POST['number']) : '';
        $visitDate = isset($_POST['visitDateTime']) ? htmlspecialchars($_POST['visitDateTime']) : '';
        $message = isset($_POST['reason']) ?  html_entity_decode($_POST['reason'], ENT_QUOTES, 'UTF-8') : '';
        $duration = isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : '';
    
        if (empty($name) || empty($email) || empty($number) || empty($visitDate) || empty($duration)) {
            echo json_encode(["status" => "error", "message" => "Please fill out all required fields."]);
            exit();
        }
        // Convert to a readable format
        $visitDateFormatted = date("F j, Y \a\\t g:i A", strtotime($visitDate));

    
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'danieltesla746@gmail.com';
            $mail->Password = 'brotflirznvijgnl';
                $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
    
            // Recipients
            $mail->setFrom('noreply@wellnesscommunityacademy.com', 'Wellness Community Academy');
            $mail->addAddress('seshun65@gmail.com');
            $mail->addBCC('saintdannyyy@gmail.com');
    
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Virtual Meeting Request';
            $mail->Body = "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f4;
                            color: #333;
                            line-height: 1.6;
                            padding: 0;
                            margin: 0;
                        }
                        .container {
                            background-color: #ffffff;
                            padding: 20px;
                            border-radius: 10px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                            max-width: 600px;
                            margin: 20px auto;
                        }
                        h1 {
                            color: #0056b3;
                        }
                        .content {
                            margin-top: 20px;
                            padding: 10px 0;
                        }
                        .content b {
                            color: #333;
                        }
                        .footer {
                            margin-top: 20px;
                            padding-top: 10px;
                            border-top: 1px solid #ddd;
                            font-size: 12px;
                            color: #777;
                            text-align: center;
                        }
                        a {
                            color: #0056b3;
                            text-decoration: none;
                        }
                        a:hover {
                            text-decoration: underline;
                        }
                        .btn {
                            background-color: #0056b3;
                            color: #ffffff;
                            padding: 10px 20px;
                            text-align: center;
                            display: inline-block;
                            border-radius: 5px;
                            text-decoration: none;
                            margin-top: 20px;
                        }
                        .btn:hover {
                            background-color: #004494;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h1>New Appointment Request</h1>
                        <p>Hello there, <b>Wellness Community Academy</b>👋,</p>
                        <p>$name wants to meet you on $visitDateFormatted for $duration minutes. Here are the details:</p>
                        <div class='content'>
                            <p><b>Name:</b> $name</p>
                            <p><b>Email:</b> $email</p>
                            <p><b>Visit Date:</b> $visitDateFormatted</p>
                            <p><b>Message:</b> $message</p>
                            <p><b>Duration:</b> $duration minutes</p>
                            <p><b>Contact:</b> You can contact $name on <a href='tel:$number'>$number</a> or via <a href='mailto:$email'>$email</a>.</p>
                        </div>
                        <a href='https://wellnesscommunityacademy.com/admin' class='btn'>View Other Requests</a>
                        <div class='footer'>
                            <p>&copy; 2024 Wellness Community Academy. All Rights Reserved.</p>
                            <p><a href='https://wellnesscommunityacademy.com'>Visit our website</a></p>
                        </div>
                    </div>
                </body>
                </html>";

            if ($mail->send()) {
                include_once('conn/conn.php');    
                $stmt = $mysqli->prepare("INSERT INTO booked_appointments (name, email, number, visit_date, message, duration) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssissi", $name, $email, $number, $visitDate, $message, $duration);
    
                if ($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Appointment submitted successfully!"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
                }
    
                $stmt->close();
                $mysqli->close();
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to send email."]);
            }
    
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Failed to send email. Error: " . $e->getMessage()]);
        }
    }
?>    