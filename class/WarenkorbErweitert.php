<?php

/**
 * Einführung objektorientierte Programmierung
 * Ableitungen und Abhängigkeiten
 * Erweiterte Klasse wegen zusätzlichem Konstruktor
 * wird von der Basisklasse Warenkorb abgeleitet 
 *
 * @author Micha
 * @category Schulungsbeispiele
 * @version 1.0
 */
class WarenkorbErweitert extends Warenkorb {
    
    /**
     * zusätzlicher Konstruktor
     * ruft die Methode setArtikel() aus der Basisklasse auf
     * @param string $artikel
     * @param int $anzahl
     */
    public function __construct(string $artikel, int $anzahl) {
        
        $this->setArtikel($artikel, $anzahl);
    }
    
    /**
     * gibt den gesamten Array zurück
     * Funktion greift auf die Eigenschaft der Basisklasse zu (muss dann dort protected sein)
     * @return array der Array mit allen Artikeln
     */
    public function getAlleArtikel(): array {
        
        return $this->artikel;
    }
    
    /**
     * gibt alle Artikel als Liste aus
     */
    public function showAlleArtikel() {
        
        echo '<ul>';
        foreach( $this->artikel as $artikel => $anzahl){
            
            echo "<li>$artikel: $anzahl</li>";
        }
        echo '</ul>';
    }
    
}
