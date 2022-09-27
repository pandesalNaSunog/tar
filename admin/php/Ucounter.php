<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();
        $query = "SELECT count(id) AS total FROM users WHERE user_type ='User' AND approval_status = 'pending' AND verified = 'yes'";
        $result = mysqli_query($con,$query);
        $values = mysqli_fetch_assoc($result);
        $num_rows = $values['total'];
        echo $num_rows;
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>