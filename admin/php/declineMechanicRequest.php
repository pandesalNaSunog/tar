<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();
        if($con->connect_error){
                echo $con->connect_error;
            }else{
                $requestId = $_POST['requestId'];
                $reason = htmlspecialchars($_POST['reason']);
                $query = "SELECT count(id) AS total FROM users WHERE id='$requestId' AND approval_status='pending'";
                $result = mysqli_query($con,$query);
                $values = mysqli_fetch_assoc($result);
                $num_rows = $values['total'];
                if($num_rows == '1'){

                    $query = "SELECT * FROM users WHERE id = '$requestId'";
                    $user = $con->query($query) or die($con->error);
                    $userRow = $user->fetch_assoc();

                    $email = $userRow['email'];

                    sendMail($email);
                    $query = "DELETE FROM users WHERE id='$requestId'";
                    $con->query($query) or die($con->error);
                    echo 'success';
                }else{
                    echo 'data missing';
                }
            }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }

    function sendMail($email){
        
        $mail = new PHPMailer(true);

        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tapandrepair@gmail.com';
        $mail->Password = 'jamxdnzynricpvlr';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('tapandrepair@gmail.com', 'Tap And Repair');
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Declined';
        $mail->Body = 'Your account has been declined by the administrator.';
        $mail->send();
    }
?>