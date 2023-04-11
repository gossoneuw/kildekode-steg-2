<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_POST["reset-password-submit"])) {
    
    $selector = $_POST["selector"];
    $validator = $_POST["validator"];
    $password = $_POST["pwd"];
    $passwordRepeat = $_POST["pwd-repeat"];

    if (empty($password) || empty($passwordRepeat)) {
        header("Location: /steg2/create-new-password.php?newpwd=empty"); //må muligens inneholde token. 
        
    } else if (strlen($password) > 30 || strlen($passwordRepeat) > 30) {
        header("Location: /steg2/create-new-password.php?newpwd=passwdlength");
        
    } else if ($password != $passwordRepeat) {
        header("Location: /steg2/create-new-password.php?newpwd=pwdnotsame"); //må muligens inneholde token.
        
    } else if (!preg_match("/^[a-zA-Z_-]*$/", $password) || !preg_match("/^[a-zA-Z_-]*$/", $passwordRepeat)) {
        header("Location: /steg2/create-new-password.php?newpwd=char_error");
        
    }

    $currentDate = date("U");

    require 'database-connect.php';

    $sql = "SELECT * FROM pwdreset WHERE pwdResetSelector=? AND pwdResetExpires >= ?"; 
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error!";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);  
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (!$row = mysqli_fetch_assoc($result)) {
            echo "You need to re-submit your reset request";
            exit();
        } else {
            
            $tokenBin = hex2bin($validator);
            $tokenCheck = password_verify($tokenBin, $row["pwdResetToken"]);

            if ($tokenCheck === false) {
		//echo "Error: " . mysqli_error($conn);
                echo "You need to re-submit your reset request2";
                exit();
            }elseif ($tokenCheck === true) {
                
                $tokenEmail = $row['pwdResetEmail'];

                $sql = "SELECT * FROM user WHERE email=?;";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "There was an error!";
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (!$row = mysqli_fetch_assoc($result)) {
                        echo "There was an error!";
                        exit();
                    } else {

                        $sql = "UPDATE user SET password_hash=? WHERE email=?";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo "There was an error!";
                            exit();
                        } else {
                            $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ss", $newPwdHash, $tokenEmail);
                            mysqli_stmt_execute($stmt);

                            $sql = "DELETE FROM pwdreset WHERE pwdResetEmail=?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                echo "There was an error!";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                                mysqli_stmt_execute($stmt);
                                header("Location: /steg2/login.php?newpwd=passwordupdated");
                            }
                        }
                }   }
            }
        }
    } 


} else {
    header("Location: steg2/login.php");
}
