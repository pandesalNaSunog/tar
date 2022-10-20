<?php
    //if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        date_default_timezone_set('Asia/Manila');
        $today = date('Y-m-d H:i:s');
        //$con = new mysqli ("localhost","u568496919_tar","TarPassword11","u568496919_tar_db");
        $con = new mysqli ("localhost","root","","tar_db");
        $query = "SELECT * FROM users WHERE user_type = 'admin'";

        $user = $con->query($query) or die($con->error);
        if($row = $user->fetch_assoc()){
            echo 'nope';
        }else{
            $password = password_hash('password', PASSWORD_DEFAULT);
            $query = "INSERT INTO users(`first_name`,`email`,`password`,`created_at`,`updated_at`,`user_type`)VALUES('Admin','user@admin.com','$password','$today','$today','admin')";
            $con->query($query) or die($con->error);
            echo 'ok';
        }
    //}else{
        //echo header('HTTP/1.1 403 Forbidden');
    //}
?>
