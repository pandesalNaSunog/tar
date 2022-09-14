<?php
    include('admin/php/connection.php');
    $con = connect();
    $query = "UPDATE users SET status = 'idle'";
    $con->query($query) or die($con->error);
    echo 'ok';
?>