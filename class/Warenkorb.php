<?php

/**
 * Einführung objektorientierte Programmierung
 * Ableitungen und Abhängigkeiten
 * Basisklasse für einen Warenkorb
 *
 * @author Micha
 * @category Schulungsbeispiele
 * @version 1.0
 */
class Warenkorb {
    
    /**
     * assoziativer Array für Artikel
     * Sichtbarkeit protected für diese Klasse und allen Ableitungen
     * @var array (Artikel => Anzahl)
     */
    protected array $artikel = [];
    
    /**
     * speichert einen Artikel mit Anzahl in den Warenkorb
     * @param string $artikel
     * @param int $anzahl
     */
    public function setArtikel(string $artikel, int $anzahl) {
        
        $this->artikel[$artikel] = $anzahl;
    }
    
    /**
     * gibt die Anzahl eines Artikel zurück
     * exsistiert der Artikel nicht, wird -1 zurück gegeben
     * @param string $artikel Artikel
     * @return int Anzahl
     */
    public function getArtikel(string $artikel): int {
        
        # gibt es den Artikel, dann Anzahl zurückgeben
        if( isset( $this->artikel[$artikel] ) ){
            
            return $this->artikel[$artikel];
        }
        else {
            
            return -1;
        }
    }
    
    /**
     * entfernt eine bestimmte Anzahl eines Artikel
     * @param string $artikel
     * @param int $anzahl
     */
    public function remArtikel(string $artikel, int $anzahl) {
        
        # gibt es den Artikel, dann abziehen
        if( isset( $this->artikel[$artikel] ) ){
        
            # ist die gespeicherte Anzahl größer als die abzuziehende, dann abziehen
            if( $this->artikel[$artikel] > $anzahl ){

                $this->artikel[$artikel] -= $anzahl;

            }
            # ansonsten Artikel löschen
            else{

                unset( $this->artikel[$artikel] );
            }
        }
    }
    
    
}
