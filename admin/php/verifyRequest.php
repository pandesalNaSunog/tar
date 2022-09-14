<?php
//    include('connection.php');
//    $con = connect();
$con = new mysqli ('localhost','root','','tar_database');
    $requestId = $_POST['requestId'];
    $query = "SELECT count(id) AS total FROM users WHERE id='$requestId'";
    $result = mysqli_query($con,$query);
    $values = mysqli_fetch_assoc($result);
    $num_rows = $values['total'];
    echo $num_rows;
?>