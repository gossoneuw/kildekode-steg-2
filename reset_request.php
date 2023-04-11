<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
ini_set('display_errors', 1);
if (isset($_POST["reset-request-submit"])) {

    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);

    $url = "158.39.188.203/steg2/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);
    //link til vår nettside
    
    $expires = date("U") + 300;
    //hvor lenge token skal vare, sekunder.

    require 'database-connect.php';

    $userEmail = $_POST["email"];

    if (empty($userEmail)) {
        header("Location: /steg2/reset_password.php?reset=empty");
    } else if (strlen($userEmail) > 35) {
        header("Location: /steg2/reset_password.php?reset=emaillength");
    } else {
        if (!preg_match("/^[^\s@]+@[^\s@]+\.[^\s@]+$/", $userEmail)) {
            header("Location: /steg2/reset_password.php?reset=char_error");
        } else {
            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                header("Location: /steg2/reset_password.php?reset=invalid_email");
            }
        }
    }

    $sql = "DELETE FROM pwdreset WHERE pwdResetEmail=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error!1";
	$error = mysqli_error($conn);
	echo "There was an error! Error: ".$error;
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
    }

    $sql = "INSERT INTO pwdreset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, 
    ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error!";
        exit();
    } else {
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $to = $userEmail;

    require_once "vendor/autoload.php";
    require_once '/var/www/private/sendmail.php';
    $mail = new PHPMailer(true);

    
    $mail->isSMTP();
    $mail ->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = '465';
    $mail->isHTML(true);
    $mail->Username = $mailer_username;
    $mail->Password = $mailer_password;
    $mail->SetFrom($mailer_username);
    $mail->Subject = 'Reset your password for datasikkerhet gruppe 2';
    $mail->Body = $message .= '<p>We recieved a password reset request. The link to reset your password is provided below.</p>
    <p>If you did not make this request, you can ignore this e-mail.</p>
    <p>Here is your password reset link:<br><a href="' . $url . '">' . $url . '</a></p>';


    
    $mail->AddAddress($to);

    $mail->Send();

    header("Location: /steg2/reset_password.php?reset=success");



} else {
    header("Location: /steg2/login.php");
}
