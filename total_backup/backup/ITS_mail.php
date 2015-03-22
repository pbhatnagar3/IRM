<?php
//phpinfo();
/*
ini_set("SMTP","smtp.its.vip.gatech.edu" );
ini_set('sendmail_from', 'user@its.vip.gatech.edu'); //Suggested by "Some Guy"

$Name = "Da Duder"; //senders name
$email = "email@its.vip.gatech.edu"; //senders e-mail adress
$recipient = "krudysz@gmail.com"; //recipient
$mail_body = "The text for the mail..."; //mail body
$subject = "Subject for reviever"; //subject
$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields

mail($recipient, $subject, $mail_body, $header); //mail command :)
*/
if (isset($_REQUEST['message']))
//if "email" is filled out, send email
  {
  //send email
  $email = 'usr';
  $message = $_REQUEST['message'];
  $host = $_SERVER['SERVER_NAME'];
  $subject = $host;
  mail("krudysz@gmail.com", $subject,
  $message, "From:" . $email);
  echo "Thanks for your help.";
  }
else
//if "email" is not filled out, display the form
  {
  echo "<form method='post' action='ITS_mail.php'>
  Message:<br>
  <textarea name='message' rows='2' cols='50'>
  </textarea>
  <input type='submit' value='send'>
  </form>";
  }
?>
