<?php
#****************************************************************************************#


				/**
				*
				*	Entschärft und säubert einen String
				*
				*	@param String $value 	- Der zu entschärfende und zu bereinigende String
				*
				*	@return String 			- Der entschärfte und bereinigte String, bei Leerstring NULL
				*
				*/
				function cleanString($value) {
if(DEBUG_F)		echo "<p class='debugCleanString'>Aufruf cleanString('$value')</p>\r\n";	
					
					// htmlspecialchars() entschärft HTML-Steuerzeichen wie < > & ""
					// und ersetzt sie durch &lt;, &gt;, &amp;, &quot;
					// ENT_QUOTES | ENT_HTML5 ersetzt zusätzlich '' durch &apos;
					$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
					
					// trim() entfernt am Anfang und am Ende eines Strings alle 
					// sog. Whitespaces (Leerzeichen, Tabulatoren, Zeilenumbrüche)
					$value = trim($value);
					
					// Damit cleanString() nicht NULL-Werte in Leerstings verändert, wird 
					// geprüft, ob $value überhaupt einen gültigen Wert besitzt
					if(!$value){
					
						$value=NULL;					
					}
					return $value;					
				}


#****************************************************************************************#

				
				/**
				*
				*	Prüft einen String auf Leerstring, Mindest- und Maxmimallänge
				*
				*	@param String $value 									- Der zu prüfende String
				*	@param [Integer $minLength=INPUT_MIN_LENGTH] 	- Die erforderliche Mindestlänge
				*	@param [Integer $maxLength=INPUT_MAX_LENGTH] 	- Die erlaubte Maximallänge
				*
				*	@return String/NULL - Ein String bei Fehler, ansonsten NULL
				*	
				*/
				function checkInputString($value, $minLength=INPUT_MIN_LENGTH, $maxLength=INPUT_MAX_LENGTH) {
if(DEBUG_F)		echo "<p class='debugCheckInputString'>Aufruf checkInputString( '$value' [ $minLength | $maxLength ] )</p>\r\n";	
					
					$errorMessage = NULL;
					
					// Prüfen auf leeres Feld
					if( !$value ) {
						$errorMessage = "Dies ist ein Pflichtfeld!";
					
					// Prüfen auf Mindestlänge
					} 
                                        elseif( mb_strlen($value) < $minLength ) {
						$errorMessage = "Muss mindestens $minLength Zeichen lang sein!";
					
					// Prüfen auf Maximallänge
					} elseif( mb_strlen($value) > $maxLength ) {
						$errorMessage = "Darf maximal $maxLength Zeichen lang sein!";						
					}
					
					return $errorMessage;
				}


#****************************************************************************************#


				/**
				*
				*	Prüft eine Email-Adresse auf Leerstring und Validität
				*
				*	@param String $value 	- Die zu prüfende Email-Adresse
				*
				*	@return String/NULL 		- Ein String bei Fehler, ansonsten NULL
				*
				*/
				function checkEmail($value) {
if(DEBUG_F)		echo "<p class='debugCheckEmail'>Aufruf checkEmail('$value')</p>\r\n";	
					
					$errorMessage = NULL;
					
					// Prüfen auf leeres Feld
					if( !$value ) {
						$errorMessage = "Dies ist ein Pflichtfeld!";
					
					// Email auf Validität prüfen
					} elseif( !filter_var($value, FILTER_VALIDATE_EMAIL) ) {
						$errorMessage = "Dies ist keine gültige Email-Adresse!";
					}
					
					return $errorMessage;
				}		


#****************************************************************************************#


				function CleanAlignmentString($value){
if(DEBUG_F)		echo "<p class='debugCleanString'>Aufruf cleanString('$value')</p>\r\n";	
					

					$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
					

					$value = trim($value);
					
					if($value=="align right"){
						
						$value = "floatR";
					} else{
						
						$value = "floatL";
						
					}
						
					// Damit cleanString() nicht NULL-Werte in Leerstings verändert, wird 
					// geprüft, ob $value überhaupt einen gültigen Wert besitzt
					if(!$value){
					
						$value=NULL;					
					}
					return $value;					
				}


#****************************************************************************************#

					function CleanCategoryString($value){
						
if(DEBUG_F)		echo "<p class='debugCleanString'>Aufruf cleanString('$value')</p>\r\n";	
					

					$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
					

					$value = trim($value);
					
					$value = ucfirst($value);
					//$categoryFirstLetter = substr($value, 0, 1);
					//$categoryPartFromSecondLetter = substr($value, 1);
					//$categoryFirstLetter .= mb_strtolower($categoryPartFromSecondLetter);
					
					//$value = $categoryFirstLetter;
				
						
					// Damit cleanString() nicht NULL-Werte in Leerstings verändert, wird 
					// geprüft, ob $value überhaupt einen gültigen Wert besitzt
					if(!$value){
					
						$value=NULL;					
					}
					return $value;					
				}




#****************************************************************************************#

			function imageUpload($uploadedImage,
										$maxWidth			= IMAGE_MAX_WIDTH,
										$maxHeight 			= IMAGE_MAX_HEIGHT,
										$maxSize 			= IMAGE_MAX_SIZE,
										$allowedMimeTypes = IMAGE_ALLOWED_MIMETYPES,
										$uploadPath 		= IMAGE_UPLOADPATH
										){
				
if(DEBUG) 	echo "<p class='debugImageUpload'>Aufruf ImageUpload()</p>\r\n";
	
if(DEBUG)	echo "<pre class='debugImageUpload'>\r\n";					
if(DEBUG)	print_r($uploadedImage);					
if(DEBUG)	echo "</pre>\r\n";


				#********** BILDINFORMATIONEN SAMMELN **********#
	
				//Dateiname 
				$fileName = $uploadedImage['name'];
		
				//Leerzeichen durch "_" ersetzen
				$fileName = str_replace(" ", "_", $fileName);
				//Dateinamen in Kleinbuchstaben umwandeln
				$fileName = mb_strtolower($fileName); 
				//Umläte ersetzen
				$fileName = str_replace(array ("ä","ö", "ü", "ß"), array("ae", "oe", "ue", "ss"), $fileName); 
				
				//zufälligen Dateinamen generieren
				$randomPrefix = rand(1, 999999) . str_shuffle('abcdefghijklmnoprstuvwxyz') . time(); //time -> Unix-TimeStamp
if(DEBUG_F)		echo "<p class='debug'>\$randomPrefix: $randomPrefix)</p>\r\n";	
				
				//Pfad zum endgültigen Speicherort
				$fileTarget = $uploadPath . $randomPrefix . "_" . $fileName;
				
				//Dateigöße
				$fileSize = $uploadedImage['size'];
				
				//Temporär Pfad auf dem Server (Quarantäneverzeichnis)
				$fileTemp = $uploadedImage['tmp_name'];
				
				
			

if(DEBUG) 		echo "<p class='debugImageUpload'>\$fileName: $fileName</p>\r\n";
if(DEBUG) 		echo "<p class='debugImageUpload'>\$fileSize: " . round($fileSize/1024, 2) . " kB</p>\r\n"; // round_-> erste Zahl die gerundet sein soll, zweite Zahl - Nachkommstellen(hier zwei)
if(DEBUG) 		echo "<p class='debugImageUpload'>\$fileTemp: $fileTemp</p>\r\n";
if(DEBUG) 		echo "<p class='debugImageUpload'>\$fileTarget: $fileTarget</p>\r\n";


			//Genauere Informationen zum Bild holen
				
				$imageData = getimagesize($fileTemp);
			
				if(DEBUG)	echo "<pre class='debugImageUpload'>\r\n";					
				if(DEBUG)	print_r($imageData);					
				if(DEBUG)	echo "</pre>\r\n";


		
		
				$imageWidth = $imageData[0];
				$imageHeight = $imageData[1];
				$imageMimeType = $imageData['mime'];
			
if(DEBUG) 		echo "<p class='debugImageUpload'>\$imageWidth: $imageWidth</p>\r\n";
if(DEBUG) 		echo "<p class='debugImageUpload'>\$imageHeight: $imageHeight</p>\r\n";
if(DEBUG) 		echo "<p class='debugImageUpload'>\$imageMimeType: $imageMimeType</p>\r\n";

				#********** BILD PRÜFEN **********#
							
				//MIME-Type prüfen
				//Whitelist mit erlaubten Bildtypen
				$allowedMimeTypes = IMAGE_ALLOWED_MIMETYPES; //const Variable in congfig.inc
				
				if (!in_array($imageMimeType, $allowedMimeTypes) ){ // in_array prüft ob in $imageMimeType in $allowedMimeTypes-array aufgelistete Mime_type gibt. Liefert true oder false zurück
								
					$errorMessage = "Dies ist kein gültiger Bildtype!";
								
								
				} 	elseif($imageHeight > $maxHeight){
								
								$errorMessage = "Die Bildhöhe darf maximal" . IMAGE_MAX_HEIGHT . "Pixel betragen!";

					}elseif($imageWidth > $maxWidth){
								
								$errorMessage = "Die Bildbreite darf maximal" . IMAGE_MAX_WIDTH . "Pixel betragen!";

					}elseif($fileSize > $maxSize){
								
								$errorMessage = "Die Dateigröße darf maximal 128 Kb betragen";

					}else{
								
								$errorMessage = NULL;
							}
							
							
							#********** ABSCHLIESSENDE BILDPRÜFUNG **********#
				if(!$errorMessage){
								
								//Erfolgsfall
								
if(DEBUG) 		echo "<p class='debugImageUpload'>Die Bildprüfung ergab keine Fehler.</p>\r\n";

					#********** BILD SPEICHERN **********#
							
					if (!@move_uploaded_file($fileTemp, $fileTarget) ){ // move_uploaded_file schiebt Dateien von eine Verzeichnis nach ein anderes
							
								//Fehlerfall
								
if(DEBUG) 			echo "<p class='debugImageUpload err'>Fehler beim Verschieben des Bildes nach '$fileTarget'.</p>\r\n";
								$errorMessage = "Fehler beim Speichern des Bildes";

								
								
					}	else{
								
if(DEBUG) 		echo "<p class='debugImageUpload ok'>Bild erfolgreich nach '$fileTarget' verschoben.</p>\r\n";
								
						}

				}

							#********** FEHLERMELDUNG/NULL UND BILDPFAD ZURÜCKGEBEN **********#
							
							
							return array("imageError"=>$errorMessage, "imagePath"=>$fileTarget);

			
				
			}

?>