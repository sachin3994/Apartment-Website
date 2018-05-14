<?php
$to='sachin.sachin.thakur807@gmail.com';
$subject = 'Apartment Query/Feedback Received';
$email=$_POST['email'];
$phone = $_POST['phone'];
$name = $_POST['name'];
// The message
$message = $_POST['message'];
$message = $message.' sent by '.$email.' Contact: '.$phone;
$message = wordwrap($message, 70, "\r\n");

// Send
mail($to,$subject, $message);
//mail($to, $subject, $message, $headers);
echo "Success";
?>