<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
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
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>