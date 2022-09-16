<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();
        if($con->connect_error){
                echo $con->connect_error;
            }else{
                $requestId = $_POST['searchOwnerInput'];
                $query = "SELECT count(id) AS total FROM users WHERE first_name LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved' OR last_name LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved' OR contact_number LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved' OR email LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved'";
                $result = mysqli_query($con,$query);
                $values = mysqli_fetch_assoc($result);
                $num_rows = $values['total'];
                if($num_rows == '0'){
                    echo 'no data';
                }else{
                    $searchquery = "SELECT * FROM users WHERE first_name LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved' OR last_name LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved' OR contact_number LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved' OR email LIKE '%$requestId%' AND user_type='Owner' AND approval_status='Approved'";
                    $result = mysqli_query($con,$searchquery);
                    $productArray = array();
                    if($result){
                        while($row = mysqli_fetch_assoc($result)){
                            $id = $row['id'];
                            $firstName = $row['first_name'];
                            $lastName = $row['last_name'];
                            $email = $row['email'];
                            $contactNumber = $row['contact_number'];
                            $updatedAt = $row['updated_at'];
                            $productArray[] = array(
                                'id' => $id,
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'email' => $email,
                                'contact_number' => $contactNumber,
                                'updated_at' => $updatedAt,
                            );
                        }
                        echo json_encode($productArray);
                    }
                }
            }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>