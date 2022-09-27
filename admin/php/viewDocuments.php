<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();

        if(isset($_POST)){
            $userId = $_POST['user_id'];
            $query = "SELECT * FROM users WHERE id = '$userId'";
            $user = $con->query($query) or die($con->error);
            $userRow = $user->fetch_assoc();

            echo json_encode(array(
                'documents' => $userRow['valid_id']
            ));
        }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>