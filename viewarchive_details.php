<?php


require_once "../connections/connections.php";     

 $id=$_GET['id'];
 $sql = "SELECT * FROM archive where id='$id'";
 $result = mysqli_query($con, $sql);
 $val = mysqli_fetch_assoc($result);
       
        $id=$val['id'];
        $code=$val['code'];
        $schedule=$val['schedule'];
        $owner_name=$val['owner_name'];
        $contact=$val['contact'];
        
        $email=$val['email'];
        $address=$val['address'];
        $category_id=$val['category_id'];
        $breed=$val['breed'];
        $age=$val['age'];
        $service_id=$val['service_ids'];
        $status=$val['status'];
        $date_created=$val['date_created'];
        $date_updated=$val['date_updated'];

         $sql = "INSERT INTO `appointment_list` (`id`, `code`, `schedule`, `owner_name`, `contact`, `email`, `address`, `category_id`, `breed`, `age`, `service_ids`, `status`, `date_created`, `date_updated`) VALUES ('$id','$code', '$schedule', '$owner_name', '$contact', '$email', '$address', '$category_id', '$breed', '$age', '$service_id', '$status', '$date_created', '$date_updated')";
        $result = mysqli_query($con, $sql);

$sql = "DELETE FROM `archive` WHERE id ='$id'";
    $result = mysqli_query($con, $sql);
if ($result) {
        echo '<script>alert("Data Successfully Unarchive!");';
     echo 'window.location.replace("index.php");</script>';
   
 }
else
{
    echo "ERROR: Unable to Delete new record ". mysqli_error($conn);
}
?>