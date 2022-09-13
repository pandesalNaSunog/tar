<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        session_start();
        include('connection.php');
        $con = connect();
    
        if(isset($_POST)){
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
    
            $query = "SELECT * FROM users WHERE email = '$email' AND user_type = 'Admin'";
            $user = $con->query($query) or die($con->error);
            if($row = $user->fetch_assoc()){
                if(password_verify($password,$row['password'])){
                    $userId = $row['id'];
                    $_SESSION['user_id'] = $userId;
                    echo "main-page.html";
                }else{
                    echo "Invalid Data";
                }
            }else{
                echo "Invalid Data";
            }
        }else{
            echo "Invalid Data";
        }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
    
?>