<?php
    
    $con = new mysqli ("localhost","u568496919_tar","TarPassword11","u568496919_tar_db");
    $query = "UPDATE users SET status = 'idle'";
    $con->query($query) or die($con->error);
    echo 'ok';
?>