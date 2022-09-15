<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();
        if($con->connect_error){
                echo $con->connect_error;
            }else{
                $requestId = $_POST['requestId'];
                $query = "SELECT count(id) AS total FROM users WHERE id='$requestId' AND approval_status='pending'";
                $result = mysqli_query($con,$query);
                $values = mysqli_fetch_assoc($result);
                $num_rows = $values['total'];
                if($num_rows == '1'){
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
?>