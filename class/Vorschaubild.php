<?php

/**
 * Einführung objektorientierte Programmierung
 *
 * @author Anna
 * @category Schulungsbeispeil
 * @version 1.0
 */
class Vorschaubild {
    
    /*
     * Eigenschaften
     */
    private int $breit_original;
    private int $hoch_original;
    private $zeiger_original;
    private $zeiger_verkleinert;
    private int $breit_verkleinert;
    private int $hoch_verkleinert;
    private string $datei_verkleinert;
    
    /**
     * Konstruktor berechnet Breite und Höhe des Original-Bildes
     * stellt den Zeiger zum Arbeitsspeicher zur Verfügung
     * @param string $datei_original
     */
    public function __construct(string $datei_original) {
        
        # Breite und Höhe des original Bildes
        $this->breit_original = getimagesize($datei_original)[0];
        $this->hoch_original = getimagesize($datei_original)[1];
        
        # Originalbild in den Arbeitsspeicher laden und Verweis (Zeiger) auf den Speicher in einer Variablen merken
        $this->zeiger_original = imagecreatefromjpeg($datei_original);
    }
    
    /**
     * legt Arbeitsspeicher für verkleinertes Bild an
     * kopiert Pixel vom Original in das verkleinerte Bild
     * berechnet Proportionen
     * @param int $breit_verkleinert
     * @param int $hoch_verkleinert
     */
    public function erstelleVorschaubild(int $breit_verkleinert = 0, int $hoch_verkleinert = 0) {
        
        # keine Angaben
        if( $breit_verkleinert == 0 && $hoch_verkleinert == 0 ){
            
            throw new Exception('Keine Breite und Höhe angegeben', 1000);
        }
        
        # Höhe berechnen
        if( $breit_verkleinert > 0 && $hoch_verkleinert == 0 ){
            
            $hoch_verkleinert = round($breit_verkleinert * $this->hoch_original / $this->breit_original);
            
        }
        # Breite berechnen
        elseif( $breit_verkleinert == 0 && $hoch_verkleinert > 0 ){
            
            $breit_verkleinert = round($hoch_verkleinert * $this->breit_original / $this->hoch_original);
        }
        
        # Höhe und Breite in die Eigenschaften speichern
        $this->breit_verkleinert = $breit_verkleinert;
        $this->hoch_verkleinert = $hoch_verkleinert;
        
        # Arbeitsspeicher für verkleinertes Bild anlegen und Verweis (Zeiger) auf den Speicher in einer Variablen merken
        $this->zeiger_verkleinert = imagecreatetruecolor($breit_verkleinert, $hoch_verkleinert);
        # Pixel vom Original zum Ziel-Speicher kopieren und reduzieren und interpolieren
        imagecopyresampled($this->zeiger_verkleinert, $this->zeiger_original, 0, 0, 0, 0, $breit_verkleinert, $hoch_verkleinert, $this->breit_original, $this->hoch_original);
        
        
    }

    
    /**
     * speichert das verkleinerte Bild als Datei ab
     * @param string $datei_verkleinert
     * @param int $qualitaet
     */
    public function speichereVorschaubild(string $datei_verkleinert, int $qualitaet = 80) {
        
        $this->datei_verkleinert = $datei_verkleinert;
        
        imagejpeg($this->zeiger_verkleinert, $datei_verkleinert, $qualitaet);
    }
    
 
}
