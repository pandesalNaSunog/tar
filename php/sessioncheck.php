<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        session_start();
        if(!isset($_SESSION['user_id'])){
            echo 'index.html';
        }else{
            echo 'success';
        }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>