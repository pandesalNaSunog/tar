<?php
    include('connection.php');
    $con = connect();

    if($con->connect_error){
            echo $con->connect_error;
        }else{
            $requestId = $_POST['requestId'];
            $email = $_POST['email'];
            $query = "UPDATE `users` SET `email`='$email' WHERE id='$requestId'";
            $con->query($query) or die($con->error);
            echo 'success';
        }
?>