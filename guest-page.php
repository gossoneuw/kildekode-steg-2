
<!DOCTYPE html>
 <!DOCTYPE html>
 <html>
 <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
	<a href="index.php"><img src="home.png" alt="Home Button" style="width:64px;height:56px;"></a>
        <title>Home</title>
</head>
 <body>

        <h1>Home</h1>
	

        <p>Logged in as Guest</p>


        <form method="post">
                <input type="number" name="pin-code" min="4" max="4" required>
                <input type="submit" name="pin-submit">
        </form>

	
	<form method="post">
	      <select name="emner" id="emner" onchange="this.form.submit();">
	      	      <option>velg emne her...</option>
	      	      <option value="1">Datasikkerhet</option>
                      <option value="2">Programmering 1</option>
                      <option value="3">Programmering 2</option>
		      <option value="14">Diskret matematikk</option>
        	      </select>
	</form>

	<p></p>
	
        <textarea readonly id="messagesBox" rows="15" cols="65">
		    
	?> </textarea>

         <div>
                 <ul>
                        <li><a href="webutvikling.php">Webutvikling</a></li>
                        <li><a href="databasesystemer.php">Databasesystemer</a></li>
                        <li><a href="informasjonssikkerhet.php">Informasjonssikkerhet</a></li>
                        <li><a href="datanettverk.php">Datanettverk</a></li>
                 </ul>
         </div>
 </body>
 </html>