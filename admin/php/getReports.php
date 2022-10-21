<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
        include('connection.php');
        $con = connect();
        $query = "SELECT * FROM `violations`";
        $result = mysqli_query($con,$query);
        $productArray = array();
        $viewingQuery = "UPDATE `violations` SET `viewing_status`='yes'";
        $con->query($viewingQuery) or die($con->error);
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $id = $row['id'];
                $userId = $row['user_id'];
                $violation = $row['violation'];
                $userTwoId = $row['user_two_id'];
                $createdAt = $row['created_at'];
                $updatedAt = $row['updated_at'];

                $complainantQuery = "SELECT * FROM `users` WHERE id='$userTwoId'";
                $complainant = $con->query($complainantQuery) or die($con->error);

                $complainantArray = $complainant->fetch_assoc();
                $complainantName = $complainantArray['last_name'] . ", ". $complainantArray['first_name'];

                $userQuery = "SELECT * FROM `users` WHERE id='$userId'";
                $user = $con->query($userQuery) or die($con->error);

                $userArray = $user->fetch_assoc();
                $userName = $userArray['last_name'] . ", " . $userArray['first_name'];
                $productArray[] = array(
                    'id' => $id,
                    'user_name' => $userName,
                    'violation' => $violation,
                    'complainant_name' => $complainantName,
                    'date' => date_format(date_create($createdAt), "M d, Y h:i A")

                );
            }
            echo json_encode($productArray);
        }
    }else{
        echo header('HTTP/1.1 403 Forbidden');
    }
?>