<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();

        if($con->connect_error){
                echo $con->connect_error;
            }else{
                $requestId = $_POST['requestId'];
                $email = htmlspecialchars($_POST['email']);
                $query = "UPDATE `users` SET `email`='$email' WHERE id='$requestId'";
                $con->query($query) or die($con->error);
                echo 'success';
            }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>