<?php
    include('connection.php');
    $con = connect();

    if($con->connect_error){
            echo $con->connect_error;
        }else{
            $requestId = $_POST['requestId'];
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $query = "UPDATE `users` SET `first_name`='$firstName', `last_name`='$lastName' WHERE id='$requestId'";
            $con->query($query) or die($con->error);
            echo 'success';
        }
?>