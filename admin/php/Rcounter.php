<?php
    //if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();
        $query = "SELECT count(id) AS total FROM violations WHERE viewing_status='no'";
        $result = mysqli_query($con,$query);
        $values = mysqli_fetch_assoc($result);
        $num_rows = $values['total'];
        echo $num_rows;
    // }else{
    //     echo header('HTTP/1.1 403 Forbidden');
    // }
?>