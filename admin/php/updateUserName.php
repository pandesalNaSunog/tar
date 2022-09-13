<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
    include('connection.php');
    $con = connect();

    if($con->connect_error){
            echo $con->connect_error;
        }else{
            $requestId = $_POST['requestId'];
            $firstName = htmlspecialchars($_POST['first_name']);
            $lastName = htmlspecialchars($_POST['last_name']);
            $query = "UPDATE `users` SET `first_name`='$firstName', `last_name`='$lastName' WHERE id='$requestId'";
            $con->query($query) or die($con->error);
            echo 'success';
        }
}else{
	echo header('HTTP/1.1 403 Forbidden');
}
?>