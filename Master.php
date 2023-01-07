<?php

//includes/PHPMailer.php
//include required phpmailerfiles
require_once '../phpmailer/includes/PHPMailer.php';
require_once '../phpmailer/includes/SMTP.php';
require_once '../phpmailer/includes/Exception.php';
//Define name spaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('../config.php');


require '../vendor/autoload.php';

use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\Model\SendMessageRequest;

Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_message(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `message_list` set {$data} ";
		}else{
			$sql = "UPDATE `message_list` set {$data} where id = '{$id}' ";
		}
		
		$save = $this->conn->query($sql);
		if($save){
			$rid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "Your message has successfully sent.";
			else
				$resp['msg'] = "Message details has been updated successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occured.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success' && !empty($id))
		$this->settings->set_flashdata('success',$resp['msg']);
		if($resp['status'] =='success' && empty($id))
		$this->settings->set_flashdata('pop_msg',$resp['msg']);
		return json_encode($resp);
	}
	function delete_message(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `message_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Message has been deleted successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 ".($id > 0 ? " and id != '{$id}'" : ""));
		if($check->num_rows > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category name already exists.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "Category has successfully added.";
				else
					$resp['msg'] = "Category details has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set delete_flag=1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Category has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_service(){
		$_POST['category_ids'] = implode(',',$_POST['category_ids']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `service_list` set {$data} ";
		}else{
			$sql = "UPDATE `service_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `service_list` where `name` ='{$name}' and category_ids = '{$category_ids}' and delete_flag = 0 ".($id > 0 ? " and id != '{$id}' " : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Service already exists.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "Service has successfully added.";
				else
					$resp['msg'] = "Service has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
			if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_service(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `service_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Service has been deleted successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_appointment(){
		if(empty($_POST['id'])){
			$prefix="ODAS-".date("Ym");
			$code = sprintf("%'.04d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `appointment_list` where code = '{$prefix}{$code}' ")->num_rows;
				if($check <= 0){
					$_POST['code'] = $prefix.$code;
					break;
				}else{
					$code = sprintf("%'.04d",ceil($code)+1);
				}
			}
		}
		$_POST['service_ids'] = implode(",", $_POST['service_id']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `appointment_list` set {$data} ";
		}else{
			$sql = "UPDATE `appointment_list` set {$data} where id = '{$id}' ";
		}
		$slot_taken = $this->conn->query("SELECT * FROM `appointment_list` where date(schedule) = '{$schedule}' and `status` in (0,1)")->num_rows;
		if($slot_taken >= $this->settings->info('max_appointment')){
			$resp['status'] = 'failed';
			$resp['msg'] = "Sorry, the Appointment Schedule is already full.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['id'] = $rid;
				$resp['code'] = $code;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "New Appointment Details has successfully added.</b>.";
				else
					$resp['msg'] = "Appointment Details has been updated successfully.";
				
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}

		if($resp['status'] =='success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_appointment(){


		extract($_POST);
		$del = $this->conn->query("SELECT * FROM appointment_list where id='{$id}'");
		if($del){
		$val = mysqli_fetch_assoc($del);
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

$del = $this->conn->query("INSERT INTO `archive` (`id`, `code`, `schedule`, `owner_name`, `contact`, `email`, `address`, `category_id`, `breed`, `age`, `service_ids`, `status`, `date_created`, `date_updated`) VALUES ('$id','$code', '$schedule', '$owner_name', '$contact', '$email', '$address', '$category_id', '$breed', '$age', '$service_id', '$status', '$date_created', '$date_updated')");
			
$del = $this->conn->query("DELETE FROM `appointment_list` where id = '{$id}'");

			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Appointment Details has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}







	function update_appointment_status(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `appointment_list` set `status` = '{$status}' where id = '{$id}'");

		//get all info from appointmentlist
		$sql = $this->conn->query("SELECT * FROM `appointment_list` where id = '{$id}'");

//store in result

$getResult = $sql->fetch_assoc();

// $getResult = json_encode($result);
// echo "<script>console.log('{$getResult}' );</script>";
//Schema data
$code = $getResult["code"];
$schedule = $getResult["schedule"];
$customerename = $getResult["owner_name"];
$contact = $getResult["contact"];
$receiveremail = $getResult["email"];
$customer_type = $getResult["breed"];
$age = $getResult["age"];
if($getResult["status"] == 0){
	$status = "pending";
}
if($getResult["status"] == 1){
	$status = "confirmed";


//SMS Notification
// Configure client

//END OF Notification

}

if($getResult["status"] == 2){
	$status = "cancelled";
}
//end of get all info from appointment selected details

		if($del){
			try{


$config = Configuration::getDefaultConfiguration();
$config->setApiKey('Authorization', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY1NTQ2MDQ5OSwiZXhwIjo0MTAyNDQ0ODAwLCJ1aWQiOjk1MTU3LCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.8kYEkoHLAKaDqIU01UwicboSvKNRRXP79OFyQc5Px78');
$apiClient = new ApiClient($config);
$messageClient = new MessageApi($apiClient);

// Sending a SMS Message
$sendMessageRequest1 = new SendMessageRequest([
    'phoneNumber' => '$contact',
    'message' => 'Your Appointment Request is Pending Please wait For Confirmation We will send a SMS Notification if Confirmed',
    'deviceId' => 128701
]);

$sendMessages = $messageClient->sendMessages([
    $sendMessageRequest1
 
]);


				$mail = new PHPMailer();
				//Set mailer to use smtp
				$mail->isSMTP();
				//define smtp host
				$mail->Host = "smtp.gmail.com";
				//enable smtp authentication
				$mail->SMTPAuth = "true";
				//set type of encryption (ssl/tls)
				$mail->SMTPSecure = "tls";
				//set port to connect smtp
				$mail->Port = '587';
				//set gmail username
				$mail->Username = 'comiadental@gmail.com';
				//set gmail password
				$mail->Password = 'bvtvgvwhmeazyvlx';
				//set email subject
				$mail->Subject = 'Dental Appointment Status';
				//set sender email
				$mail->setFrom('comiadental@gmail.com');
				//Enable HTML
				$mail->isHTML(true);
				//Email body
				$mail->Body ="
				<p>Hi $customerename,</p><br>
				<p>Just a friendly reminder that we have an upcoming appointment.</p><br>
				<h3> $schedule </h3>
				<h3>Online Appointment </h3>
				<p>Bacoor Branch,</p>
				<p>Dasmariñas,</p>

				<p>Philippines, 4114</p><br>
				<h1>Refference Number : $code</h1>
				<h3>Status : $status</h3>
				<p>-- Nothing Follows --</p>";
				//Add recipient
				$mail->addAddress($receiveremail);
				//Finally send email
				try {
					$mail->Send();
					//Closing smtp connection
				$mail->smtpClose();
					$check = true;
				} catch (Exception $e) {
					echo "<script>console.log('{$e}' );</script>";
				}
			}catch(Exception $e){
				echo "<script>console.log('{$e}' );</script>";
			}
			
			//End of email
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Appointment Request status has successfully updated.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_appointment':
		echo $Master->save_appointment();
	break;
	case 'delete_appointment':
		echo $Master->delete_appointment();
	break;
	case 'update_appointment_status':
		echo $Master->update_appointment_status();
	break;
	case 'save_message':
		echo $Master->save_message();
	break;
	case 'delete_message':
		echo $Master->delete_message();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_service':
		echo $Master->save_service();
	break;
	case 'delete_service':
		echo $Master->delete_service();
	break;
	default:
		// echo $sysset->index();
		break;
}