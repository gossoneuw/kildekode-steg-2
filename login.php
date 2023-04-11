<?php


    $is_invalid = false;

    //Sjekker om form er submitted og at det er brukt post-method
    if ($_SERVER["REQUEST_METHOD"] === "POST")
    {   
        //Kobler til database
        $con = require  __DIR__ . "/database.php";

        //Validererer epost - sjekker om email inneholder gyldig adresse
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) 
        {
            die("Passord eller e-post er ugyldig");
        }
        else
        {
          $email = $_POST['email'];  
        }

        //Bruker prepared statements for å sikre mot SQL-injection
        $stmt = $con->prepare('SELECT id, name, password_hash FROM user WHERE email = ?'); 
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        //Sjekker om e-post finnes i db - henter deretter ut resultater
        if ($stmt->num_rows > 0)
        {
            $stmt->bind_result($id, $name, $password_hash);
            $stmt->fetch();

            //Verifiserer at passord stemmer
            if (password_verify($_POST["password"], $password_hash)) 
            {
                //Oppretter session - den bør logges(logg hashet versjon?)
                session_start();
                session_regenerate_id();
                $_SESSION["user_id"] = $id;

                //Sjekker om bruker er foreleser eller student
                $isTeacher = $con->query("SELECT foreleserid FROM foreleserv2 WHERE foreleserid = $id");

                if ($isTeacher->num_rows == 0) 
                {
                    header("Location: index.php");
                } 
                else 
                {
                    header("Location: foreleservisning.php");
                }
                exit;
            }
        }
    // om email eller passord er invalid
    $is_invalid = true;
    }
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <title>Login</title>
    </head>
    <body>
        <a href="index.php"><img src="home.png" alt="Home Button" style="width:64px;height:56px;"></a>
        <h1>Login</a></h1>

        <?php if ($is_invalid):?>
            <p style="color: indianred; font-size:80%;">invalid login</p>
        <?php endif; ?>

        <form method="post">
            <label for="email">email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">

            <label for="password">password</label>
            <input type="password" name="password" id="password">
        
            <button>Log in</button>
        </form>

        <p>New user? - <a href="user_select.html">create account</a></p>
    </body>
</html>
