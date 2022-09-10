<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        session_start();
        session_unset();
        echo 'index.html';
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>