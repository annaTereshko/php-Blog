<?php
#************************************************************************************************#


				#**************************************#
				#********** CONTINUE SESSION **********#
				#**************************************#
				
				
				session_name("blog");
				session_start(); //wenn $_SESSION['usr_id'] nicht existiert wird ein leeres Session gestartet
			
				if( !isset( $_SESSION['usr_id'] ) ) { //immer prüfen, sonst wird eine leere Session angelegt
					// Session löschen
					session_destroy();

					
				}		

#***************************************************************************************************#


				#***********************************#
				#********** CONGIGURATION **********#
				#***********************************#
					
				require_once("include/config.inc.php"); //Sucht ob die Datei schon mal eingebunden ist (in der eingebundene Datei z.B wird config.inc.php schon mal eingebunden), wenn ja,  dann wird die Anweisung ignorirt
				require_once("include/form.inc.php");
				require_once("include/dateTime.inc.php");
				require_once("include/db.inc.php");

#***************************************************************************************************#


				#*****************************************#
				#********** INITIALZE VARIABLES **********#
				#*****************************************#
				$loginMessage 	= NULL;
				$email 		= NULL;
                                $deletedEntry   = [];
                                

				
#***************************************************************************************************#
				#***********************************#
				#********** DB CONNECTION **********#
				#***********************************#
				
				$pdo = dbConnect ("blog");


#***************************************************************************************************#


				#**********************************#
				#********** PROCESS FORM **********#
				#**********************************#
				
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				
				if( isset($_POST['formsentLogin']) ){
					
if(DEBUG)		echo "<p class='debug hint'>Formular 'Login' wird verarbeitet...</p>";

					// Schritt 2 FORM: Werte Auslesen, entschärfen, DEBUG-Ausgabe
					$email = cleanString($_POST['email'] );
					$password = cleanString($_POST['password'] );
					
if(DEBUG)		echo "<p class='debug'>\$email: $email</p>";					
if(DEBUG)		echo "<p class='debug'>\$password: $password</p>";

					// Schritt 3 FORM: Werte validieren
					
					$errorEmail = checkEmail($email);
					$errorPassword = checkInputString($password, 4);
					
					#********** FINAL FORM VALIDATION **********#
					if($errorEmail OR $errorPassword){
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Das Formular enthält noch Fehler!</p>\r\n";
						$loginMessage = "<p class='error'>Logindaten sind ungültig!</p>";
					
					}else{
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Formular ist korrekt ausgefüllt. Daten werden nun geprüft...</p>";	
						
						// Schritt 4 FORM: Daten weiterverarbeiten
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#

if(DEBUG)		echo "<p class='debug hint'>Formular 'Login' wird nun abgeschickt...</p>";
						
						//Schritt 1 DB:  DB-Verbindung herstellen
						//bereits geschehen
	
if(DEBUG) 				echo "<p class='debug'>Prüfen, ob Email-Adresse in DB existiert...</p>\r\n";
						
						$sql = "SELECT usr_id, usr_password FROM users 
									WHERE usr_email=:ph_usr_email";
									
						$params = array("ph_usr_email" => $email);
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						$statement = $pdo->prepare($sql);
						
						// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
						$statement->execute($params);
if(DEBUG)			if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";	
						
						// Schritt 4 DB: Daten weiterverarbeiten
						$row = $statement->fetch(PDO::FETCH_ASSOC);
						
						// Prüfen, ob ein Datensatz zurückgeliefert wurde	

						if( !$row )	{					
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'>Email '$email' existiert nicht in der DB!</p>\r\n";
							$loginMessage = "<p class='error'>Logindaten sind ungültig!</p>";
							$email= "";
						} else{
							// Erfolgsfall
							
if(DEBUG)				echo "<p class='debug ok'>Email '$email' wurde in der DB gefunden.</p>\r\n";

							#********** VALIDATE PASSWORD **********#
							
							if( !password_verify($password, $row['usr_password']) ){ 
								
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'>Passwort aus DB stimmt NICHT mit Passwort aus Formular überein!</p>\r\n";
								$loginMessage = "<p class='error'>Logindaten sind ungültig!</p>";								
							} else{
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'>Passwort aus DB stimmt mit Passwort aus Formular überein.</p>\r\n";
								
								#********** SESSION STARTEN UND USER-ID IN SESSION SCHREIBEN **********#
								
								session_name("blog");
								session_start();
								
								$_SESSION['usr_id'] = $row['usr_id'];
								

								
								#********** WEITERLEITUNG AUF INTERNE SEITE **********#
								
								header("Location: dashboard.php");	
								
							
								
							}//VALIDATE PASSWORD END
							
						}//PRÜFUNG ZURÜCKGELIEFERTES DATENSATZES ENDE

					} // FINAL FORM VALIDATION END
					
				} // PROCESS FORM END
				
				
#***************************************************************************************************#

				#**************************************************************#
				#********** ALLE KATEGORIEN IN EINEM ARRAY SPEICHERN **********#
				#**************************************************************#

					#***********************************#
					#********** DB OPERATIONS **********#
					#***********************************#
					
if(DEBUG)				echo "<p class='debug'>Kategorien werden aus der DB geladen...</p>\r\n";						
				
					//Schritt 1 DB:  DB-Verbindung herstellen
					//bereits geschehen

					$sql = "SELECT * FROM categories";
						
					// Schritt 2 DB: SQL-Statement vorbereiten
					$statement = $pdo->prepare($sql);
						
					// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
					$statement->execute();
if(DEBUG)		if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";

					// Schritt 4 DB: Daten weiterverarbeiten
					// Bei lesendem Zugriff: Datensätze abholen
						
					$catArray = $statement->fetchAll(PDO::FETCH_ASSOC);


#***************************************************************************************************#	

		
				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#

				// Schritt 1 URL: Prüfen, ob URL-Patrameter übergeben wurde
				



				if( isset($_GET['action']) ) {
if(DEBUG)		echo "<p class='debug hint'>URL-Parameter 'action' wurde übergeben.</p>\r\n";							
					
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
					
if(DEBUG)	   echo "<p class='debug'>Blog-Einträge werden nach Kategorien gefiltert...</p>\r\n";						
					$action = cleanString($_GET['action']);
if(DEBUG)		echo "<p class='debug'>\$action: $action</p>\r\n";	
					
					
					// Schritt 3 URL: i.d.R. verzweigen

					
						foreach ($catArray AS $categories){
									
							if( $action == $categories['cat_name'] ){		
											
							$sql = "SELECT usr_firstname, usr_lastname, usr_city, blog_headline, blog_imagePath, blog_imageAlignment, blog_content, blog_date, blog_id, cat_name FROM users 
							INNER JOIN blogs USING(usr_id)
							INNER JOIN categories USING(cat_id)
							WHERE  cat_name=:ph_cat_name
							ORDER BY blog_date DESC";
							$params =array("ph_cat_name"=>$categories['cat_name']);
							
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare($sql);
															
							//Schritt 3 DB:SQL Statement ausführen und ggf. Platzhalter füllen
							$statement->execute($params);
if(DEBUG)				if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
												
							// Schritt 4 DB: Daten weiterverarbeiten
							$sortedCatArray = $statement->fetchAll(PDO::FETCH_ASSOC);	
		
		
							}

						}
						
						
						
						if( $action == "logout" ) {
if(DEBUG) 			echo "<p class='debug'>Logout wird durchgeführt...</p>\r\n";	

						// Schritt 4 URL: Daten weiterverarbeiten
						
						// Session löschen
						session_destroy();
						// Umleiten auf index.php
						header("Location: index.php");
						exit;
						
						
					}
						
						
				} // PROCESS URL PARAMETERS END
				
				else{
					
					#*******************************************************************************#
					#********** ALLE GESPEICHERTE DATEN UND EINTRÄGE AUS DER TABELLEN AUSLESEN **********#
					#*******************************************************************************#
				
if(DEBUG)		echo "<p class='debug'>Blog-Einträge werden aus der DB geladen...</p>\r\n";						
						
					//Schritt 1 DB:  DB-Verbindung herstellen
					//bereits geschehen


					$sql = "SELECT usr_firstname, usr_lastname, usr_city, blog_headline, blog_imagePath, blog_imageAlignment, blog_content, blog_date, blog_id, cat_name FROM users 
								INNER JOIN blogs USING(usr_id)
								INNER JOIN categories USING(cat_id)
								ORDER BY blog_date DESC";
													
					// Schritt 2 DB: SQL-Statement vorbereiten
					$statement = $pdo->prepare($sql);
																
					//Schritt 3 DB:SQL Statement ausführen und ggf. Platzhalter füllen
					$statement->execute();
if(DEBUG)		if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
												
					// Schritt 4 DB: Daten weiterverarbeiten
					$dataArray = $statement->fetchAll(PDO::FETCH_ASSOC);
					
					
				}
                                
                                
 if( isset( $_POST['formsent'] )&& isset( $_SESSION['usr_id'] ) ) {
if(DEBUG)		echo "<p class='debug hint'>Formular wurde abgeschickt.</p>\r\n";

									
					$deletedEntry = cleanString($_POST['deletedEntry']);
if(DEBUG)		echo "<p class='debug'>\$deletedEntry: 		$deletedEntry</p>\r\n";

  $sql = "DELETE FROM blogs WHERE blog_id=:ph_blog_id";
					
                                         $params =array("ph_blog_id"=>$deletedEntry);
					// Schritt 2 DB: SQL-Statement vorbereiten
					$statement = $pdo->prepare($sql);
																
					//Schritt 3 DB:SQL Statement ausführen und ggf. Platzhalter füllen
					$statement->execute($params);
if(DEBUG)		if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";



 }
 

#***************************************************************************************************#

?>



<!doctype html>

<html>
	
	<head>
		<meta charset="utf-8">
		<title>Startseite</title>
		<link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/debug.css">
	</head>
	
	<body>

		<div class="container">
                    
                    <a class="" href="index.php">Blogs</a>
                    <a class="" href="courseseite.php">Kurse</a>
                    <a class="" href="videoseite.php">Tutorials</a>
                    
			
			<!-- ---------- LOGIN FORM ---------- -->
			
			<?php if(!isset( $_SESSION['usr_id'] )): ?>
				<form class="floatR" action="" method="POST">
					<input type="hidden" name="formsentLogin">
				
					<span class='error'><?= $loginMessage ?></span>
					<input class="" type="text" name="email" value="a@b.c" placeholder="Email">
					<input class="" type="password" name="password" placeholder="Password" value="1234">
					<input class="" type="submit" value="Login">
				</form>
				
				<!-- ---------- LOGIN FORM END---------- -->
				
				
				<!-- ---------- SITE JUMP/LOGOUT LINKS ---------- -->

				<?php else: ?>

					<a class="siteJumpLinks floatR" href="?action=logout">Logout</a><br>
					<a class="siteJumpLinks clearer floatR" href="dashboard.php">zum Dashboard >></a><br><br>
					
				<!-- ---------- SITE JUMP/LOGOUT LINKS END---------- -->
			
			<?php endif ?>
			
			
			
			<h1 class="clearer ueberschrift">PHP-Projekt Blog</h1>
			
			<div class="flex justifySpaceBetween">
			
			
				<!-- ---------- SHOW ALL BLOG-ENTRIES ---------- -->

				<?php if(!isset( $_GET['action'])): ?>
					<div class="entrysBlock">
						<?php foreach($dataArray AS $value): ?>	
						<?php $dateTime = isoToEuDateTime($value['blog_date']) ?>
						<?php $contentIntoParagraphs = nl2br($value['blog_content'], false); ?>
						<article>
							<p>Kategorie: <?php echo $value['cat_name']?></p>  
							<b><?php echo $value['blog_headline']?></b><br> 
							<p><?php echo $value['usr_firstname']?><?php echo $value['usr_lastname']?>(<?php echo $value['usr_city']?>) schrieb am <?php echo $dateTime['date']?> um <?php echo $dateTime['time']?> Uhr:</p>
							<div>
								<?php if($value['blog_imagePath']!= NULL): ?>
									<img class="<?php echo $value['blog_imageAlignment']?> entryImg" style="" src="<?php echo $value['blog_imagePath'] ?>" alt="Entry Picture" title="Entry Picture">
								<?php endif ?>
								<p><?php echo $contentIntoParagraphs ?></p>
							</div>
                                                            <form name="form" action="" method="POST">
                                                                <input type="hidden" name="formsent">
                                                                <input type="checkbox" name="deletedEntry" value="<?php echo $value['blog_id']?>" <?php if($deletedEntry==$value['blog_id'])	echo "checked" ?> >
                                                                <input class="deleteEntryButton" type="submit" value="DELETE">
                                                                
                                                            </form>                                                       
						</article>
						<hr>
					<?php endforeach ?>
					</div>
				<?php endif ?>
				
				<!-- ---------- SHOW ALL BLOG-ENTRIES END---------- -->
				
				
				
				<!-- ---------- SHOW SORTED BLOG-ENTRIES ---------- -->
				
				<?php if(isset( $_GET['action'])): ?>
				
					<div class="entrysBlock">
						<?php foreach($sortedCatArray AS $value): ?>
						<?php $dateTime = isoToEuDateTime($value['blog_date']) ?>
						<?php $contentIntoParagraphs = nl2br($value['blog_content'], false); ?>						
						<article>
							<p>Kategorie: <?php echo $value['cat_name']?></p>  
							<b><?php echo $value['blog_headline']?></b><br> 
							<p><?php echo $value['usr_firstname']?><?php echo $value['usr_lastname']?>(<?php echo $value['usr_city']?>) schrieb am <?php echo $dateTime['date']?> um <?php echo $dateTime['time']?> Uhr:</p>
							<div>
								<?php if($value['blog_imagePath']!= NULL): ?>
									<img class="<?php echo $value['blog_imageAlignment']?> entryImg" style="" src="<?php echo $value['blog_imagePath'] ?>" alt="Entry Picture" title="Entry Picture">
								<?php endif ?>
								<p>
								<?php echo $contentIntoParagraphs ?></p>
							</div>
                                                            <form name="form" action="" method="POST">
                                                                <input type="hidden" name="formsent">
                                                                <input type="checkbox" name="deletedEntry" value="<?php echo $value['blog_id']?>" <?php if($deletedEntry==$value['blog_id'])	echo "checked" ?> >
                                                                <input class="deleteEntryButton" type="submit" value="DELETE">
                                                                
                                                            </form>
						</article>
						<hr>
					<?php endforeach ?>
					</div>
					
				<?php endif ?>
				
				<!-- ---------- SHOW SORTED BLOG-ENTRIES END---------- -->
				
				
				<!-- ---------- SHOW ALL CATEGORIES  ---------- -->
				
				<div class="categoryBlock">
				
					<a href="<?php echo $_SERVER['SCRIPT_NAME']?>">All Categories</a><br>

					<?php
						foreach ($catArray AS $categories){
									
							foreach($categories AS $key=>$value){
								if($key=="cat_name"){
									echo "<a href='?action=$value'>$value</a><br>";
										
								}
							}
						}
		
					?>
				
				</div>
				
			<!-- ---------- SHOW ALL CATEGORIES  END ---------- -->
			</div>	
	</body>
	
</html>