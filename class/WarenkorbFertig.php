<?php

/**
 * Einführung objektorientierte Programmierung
 * Ableitungen und Abhängigkeiten
 * Finale Klasse, aus ihr können keine weiteren Ableitungen gebildet werden
 * Konstruktor und einige Methoden werden wegen optionaler Parameter überschrieben
 * wird von WarenkorbErweitert abgeleitet 
 *
 * @author Micha
 * @category Schulungsbeispiele
 * @version 1.0
 */
final class WarenkorbFertig extends WarenkorbErweitert {
    
    /**
     * Konstruktor der Elternklasse wird wegen optionaler Parameter überschrieben
     * Eltern-Konstruktor wird aufgerufen (statischer Aufruf mit ::)
     * @param string $artikel [optional]
     * @param int $anzahl [optional]
     */
    public function __construct(string $artikel = 'nix', int $anzahl = 1) {
        
        # wenn Standardwert des optionalen Parameter, dann Konstruktor der Elternklasse aufrufen
        if( $artikel != 'nix'){ 
        
            parent::__construct($artikel, $anzahl);
        
        }
    }
    
    /**
     * Methode setArtikel() der Basisklasse wird überschrieben
     * Methode setArtikel() der Basisklasse wird aufgerufen
     * @param string $artikel
     * @param int $anzahl [optional]
     */
    public function setArtikel(string $artikel, int $anzahl = 1) {
        
        parent::setArtikel($artikel, $anzahl);
    }

    /**
     * Methode remArtikel() der Basisklasse wird überschrieben
     * Methode remArtikel() der Basisklasse wird aufgerufen
     * @param string $artikel
     * @param int $anzahl [optional]
     */
    public function remArtikel(string $artikel, int $anzahl = -1) {
        
        # ist Anzahl -1, dann Artikel löschen
        if( $anzahl == -1 ){
            
            unset( $this->artikel[$artikel] );
        }
        # ansonsten remArtikel() der Basisklasse aufrufen
        else{
            
            parent::remArtikel($artikel, $anzahl);
        }
    }
    
    /**
     * entfernt alle Artikel
     */
    public function remAlleArtikel() {
        
        $this->artikel = [];
    }

}
