<!DOCTYPE html>
<html>
<head>
    <title>Forgot your password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <a href="index.php"><img src="home.png" alt="Home Button" style="width:64px;height:56px;"></a>
    <h1>Password reset</h1>
    
    <form method="post" action="reset_request.php">
        <p>Please provide your email address.</p>
        <label for="email">email</label>
        <input type="text" name="email" placeholder="Enter your e-mail address...">
        
        <br>
        <button type="submit" name="reset-request-submit">Submit</button>
    </form>
    <?php
        if (isset($_GET["reset"])) {
            if ($_GET["reset"] == "success") {
                echo '<p class="signupsuccess">Success! an e-mail has been sent, remeber to check your spam folder.</p>';
            }
        }

	$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (strpos($fullUrl, "reset=empty") == true) {
            echo "<p>You did not fill in all fields!</p>";
            exit();
        }
        elseif (strpos($fullUrl, "reset=emaillength") == true) {
            echo "<p>Too many charcters used.</p>";
            exit();
        }
        elseif (strpos($fullUrl, "reset=char_error") == true) {
            echo "<p>You used invalid characters!</p>";
            exit();
        }  
        elseif (strpos($fullUrl, "reset=invalid_email") == true) {
            echo "<p>Invalid E-mail address!</p>";
            exit();
        } 
    ?>
</body>
</html>
