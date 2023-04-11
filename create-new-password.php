<!DOCTYPE html>
<html>
<head>
    <title>Forgot your password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <main>
        <?php
        $selector = $_GET["selector"];
        $validator = $_GET["validator"];

        if (empty($selector) || empty($validator)) {
            echo "Could not validate your request!";
        } else {
            if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false ) {
                ?>

                <form action="reset_password_script.php" method="post">
		    <p>Create a new password</p>
                    <input type ="hidden" name="selector" value="<?php echo $selector ?>">
                    <input type ="hidden" name="validator" value="<?php echo $validator ?>">
                    <input type = "password" name="pwd" placeholder="Enter a new password">
                    <input type = "password" name="pwd-repeat" placeholder="Repeat new password">
                    <button type= "submit" name="reset-password-submit">Reset password</button>
                </form>
                <?php
            }
        }
        ?>
    </main>
    
</body>
</html>
