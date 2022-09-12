<?php
    include('connection.php');
    $con = connect();

    if($con->connect_error){
            echo $con->connect_error;
        }else{
            $requestId = $_POST['requestId'];
            $contactNumber = $_POST['contact_number'];
            $query = "UPDATE `users` SET `contact_number`='$contactNumber' WHERE id='$requestId'";
            $con->query($query) or die($con->error);
            echo 'success';
        }
?>