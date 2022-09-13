<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        function connect(){
            //return new mysqli ("localhost","u568496919_tar","TarPassword11","u568496919_tar_db");
            return new mysqli ("localhost","root","","tar_database");
        }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>
