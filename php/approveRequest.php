<?php
    date_default_timezone_set('Asia/Manila');
    $today = date('Y-m-d H:i:s');
    include('connection.php');
    $con = connect();

    if($con->connect_error){
            echo $con->connect_error;
        }else{
            $requestId = $_POST['requestId'];
            $query = "UPDATE `users` SET `approval_status`='Approved',`updated_at`='$today' WHERE id ='$requestId'";
            $con->query($query) or die($con->error);
            echo 'success';
        }
?>