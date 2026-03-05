<?php 
require $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/Mailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/Mailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/Mailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

  $mail = new PHPMailer(true);
      $mail-> isSMTP();
      $mail-> Host = 'smtp.gmail.com';
      $mail-> SMTPAuth = true;
      $mail-> Username = 'cdonhsshsacc@gmail.com';
      $mail-> Password = 'aqfxwgrkthqmkvbs';
      $mail-> SMTPSecure = 'ssl';
      $mail-> Port = 465;


?>