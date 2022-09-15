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
            $query = "SELECT count(id) AS total FROM users WHERE id='$requestId'";
            $result = mysqli_query($con,$query);
            $values = mysqli_fetch_assoc($result);
            $num_rows = $values['total'];
        
            if($num_rows == '1'){
                $query = "UPDATE `users` SET `first_name`='$firstName', `last_name`='$lastName' WHERE id='$requestId'";
                $con->query($query) or die($con->error);
                echo 'success';
            }else{
                echo 'no data';
            }
        }
}else{
	echo header('HTTP/1.1 403 Forbidden');
}
?>