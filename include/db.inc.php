<?php
#******************************************************************************************************#


				/**
				*
				*	Stellt eine Verbindung zu einer Datenbank mittels PDO her
				*
				*	@param [String $dbname		Name der zu verbindenden Datenbank]
				*
				*	@return Object					DB-Verbindungsobjekt
				*
				*/
				function dbConnect($dbname=DB_NAME) { //DB_NAME soll manuel geändert werden
if(DEBUG_DB)	echo "<p class='debugDb'><b>Line " . __LINE__ . ":</b> Versuche mit der DB <b>$dbname</b> zu verbinden... <i>(" . basename(__FILE__) . ")</i></p>\r\n";					
					
					// EXCEPTION-HANDLING (Umgang mit Fehlern)
					// Versuche, eine DB-Verbindung aufzubauen
					try {
						// wirft, falls fehlgeschlagen, eine Fehlermeldung "in den leeren Raum"
					
						//$pdo = new PDO(DB_SYSTEM . ":host=" . DB_HOST . "; dbname=$dbname; charset=utf8mb4", DB_USER, DB_PWD); //DB_SYSTEM-> DB Treiber,"root"->benutzername(wir haben kein, deswegen root)
                                                $pdo = new PDO('mysql:dbname=blog;host=localhost;port=3306', 'root', 'cimdata');
					// falls eine Fehlermeldung geworfen wurde, wird sie hier aufgefangen					
					} catch(PDOException $error) {
						// Ausgabe der Fehlermeldung
if(DEBUG_DB)		echo "<p class='error'><b>Line " . __LINE__ . ":</b> <i>FEHLER: " . $error->GetMessage() . " </i> <i>(" . basename(__FILE__) . ")</i></p>\r\n";
						// Skript abbrechen
						exit;
					}
					// Falls das Skript nicht abgebrochen wurde (kein Fehler), geht es hier weiter
if(DEBUG_DB)	echo "<p class='debugDb ok'><b>Line " . __LINE__ . ":</b> Erfolgreich mit der DB <b>$dbname</b> verbunden. <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					// DB-Verbindungsobjekt zurückgeben
					return $pdo;
				}
				
				
#******************************************************************************************************#
?>