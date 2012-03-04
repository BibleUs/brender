#!/usr/bin/php 
<?php 
include "connect.php";
include "functions.php";
	$job_id = $argv[1];

	$scene = job_get("scene",$job_id);
	$shot = job_get("shot",$job_id);

	$email = "oenvoyage@gmail.com";  // TODO add a call to get email from database (when user management is done)
	$headers = "From: brender-server@brender-farm.org\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	$subject = "Job number $job_id : $scene / $shot is finished";
	$body =" Nothing more to say. Everything is in the subject<br/>";
	$body .= "yours truly,<br/>";
	$body .=  "   the brender server";

	output("SENDING EMAIL $subject to $email");
	mail($email,$subject,$body,$headers);

?>
