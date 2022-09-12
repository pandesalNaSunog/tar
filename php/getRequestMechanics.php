<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();
        $query = "SELECT * FROM `users` where user_type = 'Mechanic' AND approval_status='pending'";
        $result = mysqli_query($con,$query);
        $productArray = array();
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $id = $row['id'];
                $firstName = $row['first_name'];
                $lastName = $row['last_name'];
                $email = $row['email'];
                $contactNumber = $row['contact_number'];
                $createdAt = $row['created_at'];
                $productArray[] = array(
                    'id' => $id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'contact_number' => $contactNumber,
                    'created_at' => $createdAt,
                );
            }
            echo json_encode($productArray);
        }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>