<?php

/**
 * Einführung objektorientierte Programmierung
 * Ableitungen und Abhängigkeiten
 * Klasse für Kunden
 * Abhängigkeit zu Warenkorb durch setWarenkorb()
 *
 * @author Micha
 * @category Schulungsbeispiele
 * @version 1.0
 */
class Kunde {
    
    /*
     * Eigenschaften für Kunde
     */
    private int $kundennr;
    private string $name;
    private string $vorname;
    
    /**
     * Eigenschaft für Warenkorb als Objekt
     * damit wird eine Abhängigkeit zur Klasse Warenkorb gebildet
     * @var WarenkorbErweitert der Warenkorb als Objekt
     */
    private WarenkorbErweitert $warenkorb;
    
    /**
     * speichert die Parameter in die Eigenschaften
     * @param int $kundennr
     * @param string $name
     * @param string $vorname
     */
    public function __construct(int $kundennr, string $name, string $vorname) {
        $this->kundennr = $kundennr;
        $this->name = $name;
        $this->vorname = $vorname;
    }

    /**
     * speichert ein Objekt vom Typ WarenkorbErweitert mit allen Eigenschaften und Methoden 
     * @param WarenkorbErweitert $warenkorb
     */
    public function setWarenkorb(WarenkorbErweitert $warenkorb) {
        $this->warenkorb = $warenkorb;
    }

    /**
     * gibt den Warenkorb als gesamtes Objekt zurück
     * @return WarenkorbErweitert
     */
    public function getWarenkorb(): WarenkorbErweitert {
        return $this->warenkorb;
    }

    /*
     * Standard-Setter und -Getter
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setVorname(string $vorname): void {
        $this->vorname = $vorname;
    }

    public function getKundennr(): int {
        return $this->kundennr;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getVorname(): string {
        return $this->vorname;
    }


}
