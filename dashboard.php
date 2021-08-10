<?php
#************************************************************************************************#


				#**************************************#
				#********** CONTINUE SESSION **********#
				#**************************************#
				
				
				session_name("blog");
				session_start();
				

#***************************************************************************************************#

				#********************************************#
				#********** PAGE ACCESS PROTECTION **********#
				#********************************************#
				
				if( !isset( $_SESSION['usr_id'] ) ) {
                                // Session löschen
				session_destroy();
				// Umleiten auf index.php
				header("Location: index.php");
				exit;
				}		

#***************************************************************************************************#

				#***********************************#
				#********** CONGIGURATION **********#
				#***********************************#
					
				require_once("include/config.inc.php"); //Sucht ob die Datei schon mal eingebunden ist (in der eingebundene Datei z.B wird config.inc.php schon mal eingebunden), wenn ja,  dann wird die Anweisung ignorirt
				require_once("include/form.inc.php");
				require_once("include/dateTime.inc.php");
				require_once("include/db.inc.php");
				require_once("include/functions.inc.php");
                                require 'class/Vorschaubild.php'; 

#***************************************************************************************************#


				#*****************************************#
				#********** INITIALZE VARIABLES **********#
				#*****************************************#
				
			
				$title 				= NULL;
				$alignPicture  			= NULL;
				$blogEntry                      = NULL;
				$newCategory			= NULL;
				$FrontendMessage 		= NULL;
				$entryPicture			= NULL;
				$errorImageUpload 		= NULL;
				$uploadSuccessMessage           = NULL;
                                $videoSource                    = NULL;
                                $courseDescription              = NULL;
                                $courseTitle                    = NULL;
                                $videoTitle                     = NULL;
                                $courseAlignPicture             = NULL;

                               
                                        
                                
                                
                            
				
#***************************************************************************************************#


				#***********************************#
				#********** DB CONNECTION **********#
				#***********************************#

				$pdo = dbConnect("blog");
				
			
				
#***************************************************************************************************#			


				#********************************************#
				#********** FETCH USERDATA FROM DB **********#
				#********************************************#

if(DEBUG) 	echo "<p class='debug'>Line <b>". __LINE__ ."</b> Userdaten werden aus DB ausgelesen...</p>\r\n"; 

				// Schritt 1 DB: DB-Verbindung herstellen
				//bereits geschehen
				
				$sql 		= "SELECT * FROM users
                                                    WHERE usr_id = :ph_usr_id";
				
				$params 	= array( "ph_usr_id" => $_SESSION['usr_id'] );

				// Schritt 2 DB: SQL-Statement vorbereiten
				$statement = $pdo->prepare($sql);
				
				// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
				$statement->execute($params);
if(DEBUG)	if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";


				// Schritt 4 DB: Daten weiterverarbeiten
				// Bei lesendem Zugriff: Datensätze abholen
				$row = $statement->fetch(PDO::FETCH_ASSOC);
			
				
				// Daten aus Tabelle 'users':
				$firstname 				= $row['usr_firstname'];
				$lastname 				= $row['usr_lastname'];
				$email 					= $row['usr_email'];
				$city 					= $row['usr_city'];
				

#***************************************************************************************************#


				#**************************************************************#
				#********** ALLE KATEGORIEN IN EINEM ARRAY SPEICHERN **********#
				#**************************************************************#
				
if(DEBUG)				echo "<p class='debug'>Kategorien werden aus der DB geladen...</p>\r\n";	

						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
				
						//Schritt 1 DB:  DB-Verbindung herstellen
						// bereits geschehen

						$sql = "SELECT * FROM categories";
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						$statement = $pdo->prepare($sql);
						
						// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
						$statement->execute();
if(DEBUG)			if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";

						// Schritt 4 DB: Daten weiterverarbeiten
						// Bei lesendem Zugriff: Datensätze abholen
						
						$catArray = $statement->fetchAll(PDO::FETCH_ASSOC);

	 

#***************************************************************************************************#	
				
				#******************************************#
				#********** PROCESS FORM CATEGORY **********#
				#******************************************#
				
				if ( isset($_POST['formsentNewCategoryEntry']) ){
					
				
if(DEBUG)		echo "<p class='debug hint'>Formular 'New Category' wird verarbeitet...</p>";	
					
					// Schritt 2 FORM: Werte Auslesen, entschärfen, DEBUG-Ausgabe
					$newCategory = CleanCategoryString($_POST['new-category']);
					
if(DEBUG)		echo "<p class='debug'>\$newCategory: $newCategory</p>";					


					// Schritt 3 FORM: Werte validieren
					
					$errorNewCategory = checkInputString($newCategory);

					
					#********** FINAL FORM VALIDATION **********#
					if($errorNewCategory){
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Die eingegebene Kategorie ist zu kurz!</p>\r\n";
						
						
					$FrontendMessage = "<p class='error'>Die eingegebene Kategorie ist zu kurz!</p>";						


					}elseif(categoryExistanceCheck($catArray, $newCategory)===true){
						//Kategorie exestiert schon
if(DEBUG)			echo "<p class='debug err'>Die angelegte Kategorie exestiert schon!</p>\r\n";
						$FrontendMessage = "<p class='error'>Die angelegte Kategorie existiert schon!</p>";						
					

					} else{
					// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Eine neue Kategorie wurde erfolgreich angelegt. Daten werden nun geprüft...</p>";	
						
						
						// Schritt 4 FORM: Daten weiterverarbeiten
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#

if(DEBUG)                       echo "<p class='debug hint'>Formular 'New Category' wird nun abgeschickt.</p>";	
						
						//Schritt 1 DB:  DB-Verbindung herstellen
						// bereits geschehen
						
						
						$sql = "INSERT INTO categories (cat_name)
							VALUES (:ph_cat_name)";
									
						$params = array("ph_cat_name" => $newCategory);
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						
						$statement = $pdo->prepare($sql);
						
						// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
						
						$statement->execute($params);
if(DEBUG)			if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";


						// Schritt 4 DB: Daten weiterverarbeiten
						// Bei schreibendem Zugriff: Schreiberfolg prüfen
						
						$rowCount =$statement->rowCount();
if(DEBUG)					echo "<p class='debug'>\$rowCount: $rowCount</p>\r\n";

						if(!$rowCount){
							//Fehlerfall
if(DEBUG)				echo "<p class='debug hint'>Es wurde keine Kategorie in der DB gespeichert.</p>\r\n";	
							$FrontendMessage = "<p class='info'>Es wurde keine neue Kategorie gespeichert! Bitte versuchen Sie es später noch einmal</p>";
							
						} else {
							//Erfolgsfall
							$lastInsertId = $pdo->lastInsertId();
							
if(DEBUG)				echo "<p class='debug ok'>Neue Kategorie ist in der DB erfolgreich angelegt.</p>\r\n";
							$FrontendMessage = "<p class='success'>Die neue Kategorie mit dem Namen <b>'$newCategory'</b> wurde erfolgreich gespiechert</p>";							
							
							$catArrayLength = count($catArray);
							
							
							$catArray[$catArrayLength]['cat_id']= $lastInsertId;
							$catArray[$catArrayLength]['cat_name']= $newCategory;
							
							$newCategory = "";
							
						}//SCHREIBERFOLGPRÜFUNG ENDE	
						
					} // FINAL FORM VALIDATION END

				}// PROCESS FORM NEW CATEGORY END				
		
				
		
				
#***************************************************************************************************#


				#*********************************************#
				#********** PROCESS FORM BLOG ENTRY **********#
				#*********************************************#



				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				
				if( isset($_POST['formsentNewBlogEntry']) ){
					
if(DEBUG)		echo "<p class='debug hint'>Formular 'Neuer Eintrag' wird verarbeitet...</p>";					
					
					// Schritt 2 FORM: Werte Auslesen, entschärfen, DEBUG-Ausgabe
					$category     = CleanString($_POST['category']);
					$title        = CleanString($_POST['title']);
					$alignPicture = CleanAlignmentString($_POST['align-picture']);
					$blogEntry    = CleanString($_POST['blog-entry']);
					
if(DEBUG)		echo "<p class='debug'>\$category: $category</p>";					
if(DEBUG)		echo "<p class='debug'>\$title: $title</p>";				
if(DEBUG)		echo "<p class='debug'>\$alignPicture: $alignPicture</p>";				
if(DEBUG)		echo "<p class='debug'>\$blogEntry: $blogEntry</p>";

					// Schritt 3 FORM: Werte validieren
					
					$errorTitle = checkInputString($title, 1, 200);
					$errorBlogEntry = checkInputString($blogEntry, 1, 10000);
					
					#********** FINAL FORM VALIDATION I**********#
					
					if($errorTitle OR $errorBlogEntry ){
						// Fehlerfall 
if(DEBUG)			echo "<p class='debug err'>Das Formular 'Neuen Blog-Eintrag verfassen' enthält noch Fehler! Pflichtfelder sind leer oder zu kurz!</p>\r\n";
						$FrontendMessage = "<p class='error'>Pflichtfelder dürfen nicht leer oder zu kurz sein!</p>";		
					
					} else{
					// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Das Formular 'Neuen Blog-Eintrag verfassen' ist korrekt ausgefüllt. Daten werden nun geprüft...</p>";							
						
						
if(DEBUG)			echo "<p class='debug ok'>Es wird geprüft ob ein Bildupload vorliegt...</p>\r\n";	
					
						#********** FILE UPLOAD **********#
						
						//Prüfen, ob ein Bildupload vorliegt
						
						if($_FILES['entry-picture']['tmp_name']){
                                   

                                                
if(DEBUG)				echo "<p class='debug hint'>Bildupload ist aktiv...</p>\r\n";	

							//Funktion zum Prüfen des Bilduploads aufrufen
							$imageUploadReturnArray = imageUpload($_FILES['entry-picture']);
                                                        
                                          
							//Prüfen, ob es einen Bilduploadfehler gab
							if($imageUploadReturnArray['imageError'] ){
								//Fehlerfall
if(DEBUG)					echo "<p class='debug err'>FEHLER: $imageUploadReturnArray[imageError]!</p>\r\n";
								$errorImageUpload = $imageUploadReturnArray['imageError'];
							
							} else{
								//Erfolgfall
								
if(DEBUG)					echo "<p class='debug ok'>Das Bild wurde erfolgreich auf den Server geladen.</p>\r\n";

								//Neuen Bildpfad speichern
								$entryPicture = $imageUploadReturnArray['imagePath'];
                                                                $pictureDisplay	= "block"; 
								
							

							}   
                                                        

						} // FILE UPLOAD END
						
						#********** FINAL FORM VALIDATION II **********#
					
						if( $errorImageUpload ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'>Es ist ein Fehler bei dem Image-Upload aufgetreten!</p>\r\n";
												
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'>Formular ist fehlerfrei und wird nun verarbeitet...</p>\r\n";

							$uploadSuccessMessage = "<p class='success'>Das Bild wurde erfolgreich gespeichert!</p>";	
                                                        
                                                                                                               
					
							#*********************************#
							
							// Schritt 4 FORM: Daten weiterverarbeiten
							
							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#
										
							//cat-id aus der Tabelle categories auslesen
										
							//Schritt 1 DB:  DB-Verbindung herstellen
							// bereits geschehen
							
							$sql = "SELECT cat_id FROM categories WHERE cat_name=:ph_cat_name";
										
							$params = array("ph_cat_name"=>$category);
										
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare($sql);
										
							// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
							$statement->execute($params);
if(DEBUG)				if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
						
							// Schritt 4 DB: Daten weiterverarbeiten
							// Bei lesendem Zugriff: Datensätze abholen
							
							$catID = $statement->fetchColumn();
			
if(DEBUG)				echo "<p class='debug'>\$catID: $catID</p>";				
						
						
							//Einen neuen Blog-Eintrag in der blogs Tabelle einfügen
										
							//Schritt 1 DB:  DB-Verbindung herstellen
							// bereits geschehen

if(DEBUG)				echo "<p class='debug hint'>Formular 'Neuer Eintrag' wird nun abgeschickt.</p>";	
									
							$sql = "INSERT INTO blogs (blog_headline, blog_imagePath, blog_imageAlignment, blog_content, cat_id, usr_id)
										VALUES (:ph_blog_headline, :ph_blog_imagePath, :ph_blog_imageAlignment, :ph_blog_content, :ph_cat_id, :ph_usr_id)";
													
							$params = array("ph_blog_headline" => $title,
												 "ph_blog_imagePath" => $entryPicture,
												 "ph_blog_imageAlignment" => $alignPicture,
												 "ph_blog_content" => $blogEntry,
												 "ph_cat_id" => $catID,
												 "ph_usr_id" => $_SESSION['usr_id']
												);
										
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare($sql);
							
							// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
							$statement->execute($params);
if(DEBUG)				if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
									
							// Schritt 4 DB: Daten weiterverarbeiten
							// Bei schreibendem Zugriff: Schreiberfolg prüfen
										
							$rowCount =$statement->rowCount();
if(DEBUG)				echo "<p class='debug'>\$rowCount: $rowCount</p>\r\n";

							if(!$rowCount){
								//Fehlerfall
if(DEBUG)					echo "<p class='debug hint'>Ein neuer Blog-Eintrag wurde NICHT gespeichert</p>\r\n";	
								$FrontendMessage = "<p class='info'>Ein neuer Blog-Eintrag wurde NICHT gepeichert1 Bitte versuchen Sie es später noch mal!</p>";
											
							} else {
								//Erfolgsfall
								$lastInsertId = $pdo->lastInsertId();
if(DEBUG)					echo "<p class='debug ok'>Ein neuer Blog-Eintrag ist in der DB unter ID '$lastInsertId' erfolgreich gespeichert.</p>\r\n";
								$FrontendMessage = "<p class='success'>Der Beitrag wurde erfolgreich gespeichert</p>";
											
								$category     = "";
								$title 		  = "";
								$alignPicture = "";
								$blogEntry 	  = "";
											
											
									
									} // BLOG-ENTRY INSERT END
									
						}	//FINAL FORM VALIDATION II END	
								
						
					} // FINAL FORM VALIDATION I END
					
					
				} // PROCESS FORM BLOG ENTRY END
                                
                                
#***************************************************************************************************#

				#**********************************************#
				#********** PROCESS FORM VIDEO ENTRY **********#
				#**********************************************#

                           

				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				
				if( isset($_POST['formsentNewVideoEntry']) ){
     
					
//if(DEBUG)		echo "<p class='debug hint'>Formular 'Neuer Eintrag' wird verarbeitet...</p>";					
					
					// Schritt 2 FORM: Werte Auslesen, entschärfen, DEBUG-Ausgabe
					$videoCategory  = CleanString($_POST['video-category']); 
					$videoTitle     = CleanString($_POST['video-title']);
					$videoSource    = CleanString($_POST['video-source']);
					
if(DEBUG)		echo "<p class='debug'>\$videoCategory: $videoCategory</p>";					
if(DEBUG)		echo "<p class='debug'>\$videoTitle: $videoTitle </p>";								
if(DEBUG)		echo "<p class='debug'>\$videoSource: $videoSource </p>";

					// Schritt 3 FORM: Werte validieren
					
					$errorVideoTitle = checkInputString($videoTitle);
					$errorVideoSource = checkInputString($videoSource);
                                        
                                        //Prüfen ob das eingegebene Video in der Datenbank schon exestiert
                                        $sql = "SELECT v_path FROM videos";
                                        
										
					// Schritt 2 DB: SQL-Statement vorbereiten
					$statement = $pdo->prepare($sql);
										
					// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
					$statement->execute();
                                        
                                        $videoPathArray = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        $errorVideoPathExists = videoExistanceCheck($videoPathArray, $videoSource );

					
					#********** FINAL FORM VALIDATION I**********#
					
					if($errorVideoTitle OR $errorVideoSource ){
						// Fehlerfall 
						$FrontendMessage = "<p class='error'>Pflichtfelder dürfen nicht leer sein!</p>";	
                                      }elseif($errorVideoPathExists){
                                            
                                                $FrontendMessage = "<p class='error'>Eingegebenes Video exestiert schon!</p>";	
					
                                        } else{
					// Erfolgsfall	
										
							// Schritt 4 FORM: Daten weiterverarbeiten
							
							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#
										
							//cat-id aus der Tabelle categories auslesen
										
							//Schritt 1 DB:  DB-Verbindung herstellen
							// bereits geschehen
							
							$sql = "SELECT cat_id FROM categories WHERE cat_name=:ph_video_name";
										
							$params = array("ph_video_name"=>$videoCategory);
										
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare($sql);
										
							// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
							$statement->execute($params);
if(DEBUG)				if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
						
							// Schritt 4 DB: Daten weiterverarbeiten
							// Bei lesendem Zugriff: Datensätze abholen
							
							$videoCatID = $statement->fetchColumn();
			
if(DEBUG)				echo "<p class='debug'>\$videoCatID: $videoCatID</p>";				
						
						
							//Einen neuen Blog-Eintrag in der blogs Tabelle einfügen
										
							//Schritt 1 DB:  DB-Verbindung herstellen
							// bereits geschehen

if(DEBUG)				echo "<p class='debug hint'>Formular 'Neuer Video Eintrag' wird nun abgeschickt.</p>";	
									
							$sql = "INSERT INTO videos (v_headline, v_path, cat_id)
								VALUES (:ph_v_headline, :ph_v_path, :ph_cat_id)";
													
							$params = array("ph_v_headline" => $videoTitle,
                                                                        "ph_v_path" => $videoSource,
                                                                        "ph_cat_id" => $videoCatID);
										
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare($sql);
							
							// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
							$statement->execute($params);
if(DEBUG)				if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
									
							// Schritt 4 DB: Daten weiterverarbeiten
							// Bei schreibendem Zugriff: Schreiberfolg prüfen
										
							$rowCount =$statement->rowCount();
if(DEBUG)				echo "<p class='debug'>\$rowCount: $rowCount</p>\r\n";

							if(!$rowCount){
								//Fehlerfall
if(DEBUG)					echo "<p class='debug hint'>Ein neuer Video-Eintrag wurde NICHT gespeichert</p>\r\n";	
								$FrontendMessage = "<p class='info'>Ein neuer Video-Eintrag wurde NICHT gepeichert1 Bitte versuchen Sie es später noch mal!</p>";
											
							} else {
								//Erfolgsfall
								$lastInsertId = $pdo->lastInsertId();
if(DEBUG)					echo "<p class='debug ok'>Ein neuer Video-Eintrag ist in der DB unter ID '$lastInsertId' erfolgreich gespeichert.</p>\r\n";
								$FrontendMessage = "<p class='success'>Das Video wurde erfolgreich gespeichert</p>";
											
								$videoCategory     = "";
								$videoTitle        = "";
								$videoSource       = "";
										
											
									
									} // VIDEO INSERT END
								
					} // FINAL FORM VALIDATION END
					
					
				} // PROCESS FORM END
                                
#***************************************************************************************************#                                

				#*********************************************#
				#********** PROCESS FORM COURSE ENTRY **********#
				#*********************************************#



				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				
				if( isset($_POST['formsentNewCourseEntry']) ){				
					
					// Schritt 2 FORM: Werte Auslesen, entschärfen, DEBUG-Ausgabe
					$courseCategory       = CleanString($_POST['course-category']);
					$courseTitle          = CleanString($_POST['course-title']);
					$courseDescription    = CleanString($_POST['course-description']);
					
if(DEBUG)		echo "<p class='debug'>\$courseCategory: $courseCategory</p>";					
if(DEBUG)		echo "<p class='debug'>\$courseTitle: $courseTitle</p>";							
if(DEBUG)		echo "<p class='debug'>\$courseDescription: $courseDescription</p>";

					// Schritt 3 FORM: Werte validieren
					
					$errorCourseTitle = checkInputString($courseTitle, 1, 200);
					$errorCourseDescription = checkInputString($courseDescription, 1, 10000);
					
					#********** FINAL FORM VALIDATION I**********#
					
					if($errorCourseTitle OR $errorCourseDescription ){
						// Fehlerfall 
						$FrontendMessage = "<p class='error'>Pflichtfelder dürfen nicht leer sein!</p>";		
					
					} else{
					// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Das Formular 'Neuen Kurs anlegen' ist korrekt ausgefüllt. Daten werden nun geprüft...</p>";							
						
						
if(DEBUG)			echo "<p class='debug ok'>Es wird geprüft ob ein Bildupload vorliegt...</p>\r\n";	
					
						#********** FILE UPLOAD **********#
						
						//Prüfen, ob ein Bildupload vorliegt
						
						if($_FILES['course-picture']['tmp_name']){
                                                    
                                                 
                                                    $folder_path = 'uploaded_images/';

                                                    $filename = basename($_FILES['course-picture']['name']);
                                                    $pictureFileName = $folder_path . $filename;
//                                                    
//                                                    $entryPicture = new Vorschaubild($pictureFileName);
//                                                    $entryPicture -> erstelleVorschaubild(400, 300);
//                                                    $entryPicture ->  speichereVorschaubild('verkleinert.jpg');
                                                 
                                                //$_FILES['entry-picture'] = '';

                                                }


//if(DEBUG)				echo "<p class='debug hint'>Bildupload ist aktiv...</p>\r\n";	
//
//							//Funktion zum Prüfen des Bilduploads aufrufen
//							$imageUploadReturnArray = imageUpload($_FILES['course-picture']);
//                                                                                   
//						
//							//Prüfen, ob es einen Bilduploadfehler gab
//							if($imageUploadReturnArray['imageError'] ){
//								//Fehlerfall
//if(DEBUG)					echo "<p class='debug err'>FEHLER: $imageUploadReturnArray[imageError]!</p>\r\n";
//								$errorImageUpload = $imageUploadReturnArray['imageError'];
//							
//							} else{
//								//Erfolgfall
//								
//if(DEBUG)					echo "<p class='debug ok'>Das Kurs-Bild wurde erfolgreich auf den Server geladen.</p>\r\n";
//
//								//Neuen Bildpfad speichern
//								$entryPicture = $imageUploadReturnArray['imagePath'];
//								$pictureDisplay	= "block";
//							
//
//							}

//						} // FILE UPLOAD END
						
						#********** FINAL FORM VALIDATION II **********#
					
						if( $errorImageUpload ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'>Es ist ein Fehler bei dem Image-Upload aufgetreten!</p>\r\n";
												
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'>Formular ist fehlerfrei und wird nun verarbeitet...</p>\r\n";

							$uploadSuccessMessage = "<p class='success'>Das Bild wurde erfolgreich gespeichert!</p>";	
					
							#*********************************#
                                                        
                                                        
                                                        #********** FILE UPLOAD **********#
						
						//Prüfen, ob ein Bildupload vorliegt
						
						if($_FILES['course-pdf']['tmp_name']){

                                                    $folder_path = 'upload_pdf/';

                                                    $filename = basename($_FILES['course-pdf']['name']);
                                                    $newname = $folder_path . $filename;

                                                }
					
							#*********************************#
							
							// Schritt 4 FORM: Daten weiterverarbeiten
							
							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#
										
							//cat-id aus der Tabelle categories auslesen
										
							//Schritt 1 DB:  DB-Verbindung herstellen
							// bereits geschehen
							
							$sql = "SELECT cat_id FROM categories WHERE cat_name=:ph_course_cat_name";
										
							$params = array("ph_course_cat_name"=>$courseCategory);
										
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare($sql);
										
							// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
							$statement->execute($params);
if(DEBUG)				if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
						
							// Schritt 4 DB: Daten weiterverarbeiten
							// Bei lesendem Zugriff: Datensätze abholen
							
							$catID = $statement->fetchColumn();
			
if(DEBUG)				echo "<p class='debug'>\$catID: $catID</p>";				
						
						
							//Einen neuen Blog-Eintrag in der blogs Tabelle einfügen
										
							//Schritt 1 DB:  DB-Verbindung herstellen
							// bereits geschehen

if(DEBUG)				echo "<p class='debug hint'>Formular 'Neuer Eintrag' wird nun abgeschickt.</p>";	
									
							$sql = "INSERT INTO courses (course_headline, course_pdf, course_imagePath, course_description, cat_id)
										VALUES (:ph_course_headline, :ph_course_pdf, :ph_course_imagePath, :ph_course_description, :ph_cat_id)";
													
							$params = array("ph_course_headline" => $courseTitle,
                                                                        "ph_course_pdf" => $newname,
                                                                        "ph_course_imagePath" => $pictureFileName,       
									"ph_course_description" => $courseDescription,
									"ph_cat_id" => $catID);
										
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare($sql);
							
							// Schritt 3 DB: SQL-Statement ausführen und ggl. Platzhalter füllen
							$statement->execute($params);
if(DEBUG)				if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>\r\n";
									
							// Schritt 4 DB: Daten weiterverarbeiten
							// Bei schreibendem Zugriff: Schreiberfolg prüfen
										
							$rowCount =$statement->rowCount();
if(DEBUG)				echo "<p class='debug'>\$rowCount: $rowCount</p>\r\n";

							if(!$rowCount){
								//Fehlerfall
if(DEBUG)					echo "<p class='debug hint'>Ein neuer Blog-Eintrag wurde NICHT gespeichert</p>\r\n";	
								$FrontendMessage = "<p class='info'>Ein neuer Blog-Eintrag wurde NICHT gepeichert1 Bitte versuchen Sie es später noch mal!</p>";
											
							} else {
								//Erfolgsfall
								$lastInsertId = $pdo->lastInsertId();
if(DEBUG)					echo "<p class='debug ok'>Ein neuer Blog-Eintrag ist in der DB unter ID '$lastInsertId' erfolgreich gespeichert.</p>\r\n";
								$FrontendMessage = "<p class='success'>Der Beitrag wurde erfolgreich gespeichert</p>";
											
								$courseCategory     = "";
								$courseTitle        = "";
								$courseDescription  = "";
											
											
									
									} // BLOG-ENTRY INSERT END
									
						}	//FINAL FORM VALIDATION II END	
								
						
					} // FINAL FORM VALIDATION I END
					
					
				} // PROCESS FORM BLOG ENTRY END


				
#***************************************************************************************************#

				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				// Schritt 1 URL: Prüfen, ob URL-Patrameter übergeben wurde

				if( isset($_GET['action']) ) {
//if(DEBUG)		echo "<p class='debug hint'>URL-Parameter 'action' wurde übergeben.</p>\r\n";							
					
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
					
					$action = cleanString($_GET['action']);
if(DEBUG)		echo "<p class='debug'>\$action: $action</p>\r\n";	

					// Schritt 3 URL: i.d.R. verzweigen
				
					#********** LOGOUT **********#
					if( $action == "logout" ) {
if(DEBUG) 			echo "<p class='debug'>Logout wird durchgeführt...</p>\r\n";	

						// Schritt 4 URL: Daten weiterverarbeiten
						
						// Session löschen
						session_destroy();
						// Umleiten auf index.php
						header("Location: index.php");
						exit;
					}

				
				}// PROCESS URL PARAMETERS END
				
		
				
#***************************************************************************************************#				
				
				
?>



<!doctype html>

<html>
	
	<head>
		<meta charset="utf-8">
		<title>Dashboard</title>
		<link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/debug.css">
	</head>
	
	<body>
		<div class="container">
		
			<!-- -------- LINKS -------- -->
			
			<a class="siteJumpLinks floatR" href="?action=logout">Logout</a><br>
			<a class="siteJumpLinks clearer floatR" href="index.php"><< zum Blog</a><br>
			<a class="siteJumpLinks clearer floatR" href="videoseite.php"><< zur Tutorials</a><br>
			<a class="siteJumpLinks clearer floatR" href="courseseite.php"><< zur Kursen</a><br><br>
                        
                      
			
			<!-- -------- LINKS END-------- -->
			
			
			
			<h1 class="clearer ueberschrift">PHP-Projekt Blog - Dashboard</h1>
			
			
			
			<!-- ---------- PROFILE USER DATEN ---------- -->
			
			<p class="ueberschrift">Aktiver Benutzer: <?php echo $firstname ?> <?php echo $lastname ?></p>
			
			<!-- ---------- PROFILE USER DATEN END---------- -->
			
			
			
			
			<!-- ---------- FRONTEND FEHLERMELDUNGEN ---------- --> 
			
			<?php echo $uploadSuccessMessage ?>
			<?php echo $FrontendMessage ?>
			
			<!-- ---------- FRONTEND FEHLERMELDUNGEN END---------- --> 	 
			
  
                                                   
					<!-- ---------- FORM CATEGORY-ENTRY ---------- --> 
                                <fieldset>
                                        
					<p class="ueberschriftGrey">Neue Kategorie anlegen</p>
					<form action="" method="POST">
						<input type="hidden" name="formsentNewCategoryEntry">
						<input class="dashboardInput" type="text" name="new-category" placeholder="Name der Kategorie" value="<?php echo $newCategory ?>"><br><br>
						<input class="dashboardInput dashboardSubmitButton" type="submit" value="Neue Kategorie anlegen">
					</form>
                                        
                                        <br>
                                </fieldset>
                                        
                                        <!-- ---------- FORM CATEGORY-ENTRY END ---------- --> 
			
				<fieldset class=" dashboardForm floatL">
                                    
                                    
			
					<p class="ueberschriftGrey">Neuen Blog-Eintrag verfassen</p>
						
					<!-- ---------- FORM BLOG-ENTRY ---------- --> 	
					
					<form action="" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="formsentNewBlogEntry">
						 <legend class="ueberschriftDarkgrey">Kategorie auswählen:</legend><br>
						<select class="dashboardInput dashboardSelectBox" name="category">

						<?php
								foreach ($catArray AS $categories){
									
									foreach($categories AS $key=>$value){
										if($key=="cat_name"){
											if($value==$category){ 
												echo "\t\t\t\t<option value='$value' selected>$value</option>\r\n";
											} else{
												echo "\t\t\t\t<option value='$value'>$value</option>\r\n";
											}
										}
									}
								}
			
							?>
		
						</select><br><br>
						<input class="dashboardInput" type="text" name="title" placeholder="Überschrift" value="<?php echo $title ?>">
						<br><br>
						<legend class="ueberschriftDarkgrey">Bild hochladen:</legend>
						<span class="error"><?php echo $errorImageUpload ?></span><br>
						<div class="flex justifySpaceBetween">
							<input type="file" name="entry-picture">
							<select class="dashboardInput" name="align-picture">
								<option value="align left" 		<?php if($alignPicture=="floatL") 	echo "selected" ?>>align left</option>
								<option value="align right" 		<?php if($alignPicture=="floatR") 	echo "selected" ?>>align right</option>
							</select>
						</div>
						<br>
						<textarea class="dashboardTextInput dashboardInput" name="blog-entry" placeholder="Text..."><?php echo $blogEntry ?></textarea><br><br>
						<input class="dashboardInput dashboardSubmitButton" type="submit" value="Eintrag veröffentlichen">
					</form>
                                        
                                        
				</fieldset >
			
				<!-- ---------- FORM BLOG-ENTRY END---------- --> 	
			
			
			
				
				<fieldset  class=" dashboardForm floatR">
                                    
                                    
                                    
                                        <!-- ---------- FORM KURS-ENTRY ---------- --> 
                                 
                                        <p class="ueberschriftGrey">Neuen Kurs anlegen</p>
                                        
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            
                                            <input type="hidden" name="formsentNewCourseEntry">
                                             <legend class="ueberschriftDarkgrey">Kategorie auswählen:</legend><br>
                                            <select class="dashboardInput dashboardSelectBox" name="course-category">

						<?php
                                                    foreach ($catArray AS $categories){
									
							foreach($categories AS $key=>$value){
                                                            if($key=="cat_name"){
								if($value==$courseCategory){ 
                                                                    echo "\t\t\t\t<option value='$value' selected>$value</option>\r\n";
								} else{
                                                                    echo "\t\t\t\t<option value='$value'>$value</option>\r\n";
                                                                }
                                                            }
							}
                                                    }
			
						?>
                                            </select><br><br>
						<input class="dashboardInput" type="text" name="course-title" placeholder="Kurstitel eintragen" value="<?php echo $courseTitle ?>">
						<br><br>
						<legend class="ueberschriftDarkgrey">Bild hochladen:</legend>
						<span class="error"><?php echo $errorImageUpload ?></span><br>
						<div class="flex justifySpaceBetween">
							<input type="file" name="course-picture">
						</div>
                                                <br>
                                                <legend class="ueberschriftDarkgrey">PDF hochladen:</legend>
                                                <span class="error"></span><br>
                                                <div class="flex justifySpaceBetween">
							<input type="file" name="course-pdf">
						</div>
						<br>
						<textarea class="dashboardTextInput dashboardInput" name="course-description" placeholder="Text..."><?php echo $courseDescription ?></textarea><br><br>
						<input class="dashboardInput dashboardSubmitButton" type="submit" value="Neuen Kurs hinzufügen">
                                                
                                        </form>
                                <!-- ---------- FORM KURS-ENTRY END ---------- --> 

                                
                                <!-- ---------- FORM VIDEO-ENTRY ---------- --> 
                                        
                                        <p class="ueberschriftGrey">Neues Video hinzufügen</p>
                                        
                                        <form action="" method="POST">
                                         <input type="hidden" name="formsentNewVideoEntry">
                                         <legend class="ueberschriftDarkgrey">Kategorie auswählen:</legend><br>
                                        <select class="dashboardInput dashboardSelectBox" name="video-category">

						<?php
                                                    foreach ($catArray AS $categories){
									
							foreach($categories AS $key=>$value){
                                                            if($key=="cat_name"){
								if($value==$viedeoCategory){ 
                                                                    echo "\t\t\t\t<option value='$value' selected>$value</option>\r\n";
								} else{
                                                                    echo "\t\t\t\t<option value='$value'>$value</option>\r\n";
                                                                }
                                                            }
							}
                                                    }
			
						?>
                                        </select><br><br>
                                        <input class="dashboardInput" type="text" name="video-title" placeholder="Videotitel eintragen" value="<?php echo $videoTitle ?>"><br><br>
                                        <input class="dashboardInput" type="text" name="video-source" placeholder="Youtube link einfügen" value="<?php echo $videoSource ?>"><br><br>
						<input class="dashboardInput dashboardSubmitButton" type="submit" value="Neues Video hinzufügen">
                                                
                                        </form>
                                        
                                 <!-- ---------- FORM VIDEO-ENTRY END ---------- -->
                                
                               
				</fieldset >
				
			
			
		</div>
	
	</body>
	
</html>