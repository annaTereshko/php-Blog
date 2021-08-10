

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
                                $FrontendMessage= NULL;
       

				
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
					
if(DEBUG)	   echo "<p class='debug'>Kurse werden nach Kategorien gefiltert...</p>\r\n";						
					$action = cleanString($_GET['action']);
if(DEBUG)		echo "<p class='debug'>\$action: $action</p>\r\n";	
					
					
					// Schritt 3 URL: i.d.R. verzweigen

					
						foreach ($catArray AS $categories){
									
							if( $action == $categories['cat_name'] ){		
											
							$sql = "SELECT * FROM courses 
							INNER JOIN categories USING(cat_id)
							WHERE  cat_name=:ph_cat_name";
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
						header("Location: courseseite.php");
						exit;
						
						
					}
                                        
                                     
						
				} // PROCESS URL PARAMETERS END
				
				else{
					
					#*******************************************************************************#
					#********** ALLE GESPEICHERTE DATEN UND EINTRÄGE AUS DER TABELLEN AUSLESEN **********#
					#*******************************************************************************#
				
if(DEBUG)		echo "<p class='debug'>Kursen werden aus der DB geladen...</p>\r\n";						
						
					//Schritt 1 DB:  DB-Verbindung herstellen
					//bereits geschehen


					$sql = "SELECT * FROM courses 
						INNER JOIN categories USING(cat_id)";
													
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

  $sql = "DELETE FROM courses 
                                 WHERE course_id=:ph_course_id";
					
                                         $params =array("ph_course_id"=>$deletedEntry);
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
                                
                                
                       	<?php if(!isset( $_GET['action'])): ?>
                                  <h1 class="clearer ueberschrift">Alle Kurse</h1>
			<?php endif ?>
                                  
                        <?php if(isset( $_GET['action'])): ?>
                            <h1 class="clearer ueberschrift"><?php echo $action?> Kurs</h1>
			<?php endif ?>
			
			
		
			
			<div class="flex justifySpaceBetween">
			
			
				<!-- ---------- SHOW ALL TUTORIALS ---------- -->

				<?php if(!isset( $_GET['action'])): ?>
					<div class="entrysBlock">
						<?php foreach($dataArray AS $value): ?>	
						<?php $contentIntoParagraphs = nl2br($value['course_description'], false); ?>
						<article> 
							<h1><?php echo $value['course_headline']?></h1><br> 
							<div>
								<?php if($value['course_imagePath']!= NULL): ?>
									<img class="" style="" src="<?php echo $value['course_imagePath'] ?>" alt="Entry Picture" title="Entry Picture">
								<?php endif ?>
								<p><?php echo $contentIntoParagraphs ?></p>
                                                                <?php if($value['course_pdf']!= NULL): ?>
                                                                <embed src="<?php echo $value['course_pdf'] ?>" width="400" height="275" type="application/pdf"><br><br>
                                                                <?php endif ?>
                                                              
							</div>
                                                            <form name="form" action="" method="POST">
                                                                <input type="hidden" name="formsent">
                                                                <input type="checkbox" name="deletedEntry" value="<?php echo $value['course_id']?>" <?php if($deletedEntry==$value['course_id'])	echo "checked" ?> >
                                                                <input class="deleteEntryButton" type="submit" value="DELETE">
                                                                
                                                            </form>
						</article>
						<hr>
					<?php endforeach ?>
                                          
                                                
					</div>
				<?php endif ?>
				
				<!-- ---------- SHOW ALL TUTORIALS END---------- -->
				
				
				
				<!-- ---------- SHOW SORTED TUTORIALS ---------- -->
				
				<?php if(isset( $_GET['action'])): ?>
				
					<div class="entrysBlock">
						<?php foreach($sortedCatArray AS $value): ?>
                                                    <?php $contentIntoParagraphs = nl2br($value['course_description'], false); ?>
                                                    <article> 
                                                            <h1><?php echo $value['course_headline']?></h1><br> 
                                                            <div>
                                                                    <?php if($value['course_imagePath']!= NULL): ?>
                                                                            <img class="" style="" src="<?php echo $value['course_imagePath'] ?>" alt="Entry Picture" title="Entry Picture">
                                                                    <?php endif ?>
                                                                    <p><?php echo $contentIntoParagraphs ?></p>
                                                                <?php if($value['course_pdf']!= NULL): ?>
                                                                <embed src="<?php echo $value['course_pdf'] ?>" width="400" height="275" type="application/pdf"><br><br>
                                                                <?php endif ?>                                                                   
                                                            </div>
                                                        
                                                              <form name="form" action="" method="POST">
                                                                <input type="hidden" name="formsent">
                                                                <input type="checkbox" name="deletedEntry" value="<?php echo $value['course_id']?>" <?php if($deletedEntry==$value['course_id'])	echo "checked" ?> >
                                                                <input class="deleteEntryButton" type="submit" value="DELETE">
                                                                
                                                              </form>                                 
                                                    </article>
                                                    <hr>
                                                
                                                <?php endforeach ?>
					</div>
                                
					
				<?php endif ?>
				
				<!-- ---------- SHOW SORTED TUTORIALS END---------- -->
				
				
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