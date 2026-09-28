# Changelog

## [1.1.0] – 2026-09-28

### Neu

- Einstellungen **Text-Abstände** (WCAG 1.4.12) und **Hoher Kontrast**
- Button **Alle Einstellungen zurücksetzen**
- Backend: Position (unten rechts / unten links), Farbschema (hell / dunkel / automatisch) und Akzentfarbe
- Farben als CSS-Variablen überschreibbar
- Standardtexte auf Deutsch und Englisch je nach Sprache der Seite; englische Backend-Übersetzung
- Leere Textfelder zeigen im Backend den Standardtext als Platzhalter

### Geändert

- Das Panel ist ein natives `<dialog>` mit echten Buttons: Fokus bleibt im Panel, Screenreader-Ansagen bei Änderungen, bessere Kontraste, `prefers-reduced-motion` und Windows-Kontrastdesign werden berücksichtigt
- Gespeicherte Einstellungen werden vor dem ersten Rendern angewendet (kein Aufblitzen mehr)
- Die Schriftgröße skaliert relativ zur Theme-Schriftgröße, statt sie zu überschreiben
- **Modus ohne Ablenkungen** blendet nicht mehr alle Bilder, Videos und iframes aus, sondern stoppt Animationen, hält Autoplay-Videos an und blendet nur dekorative Bilder aus
- Einstellungen werden seitenweit unter einem Schlüssel gespeichert; bestehende Einstellungen werden übernommen
- Aktive Einstellungen stehen jetzt zusätzlich als Klassen an `<html>`

### Behoben

- CSS und JavaScript wurden doppelt eingebunden
- CSS-ID und Klasse aus dem Backend wurden nicht ausgegeben; die Texte des Widgets landeten im Suchindex
- Im Modus ohne Ablenkungen verschwand das Icon des runden Buttons (und alle anderen Elemente mit `aria-hidden`)
- Das geschlossene Panel war per Tab-Taste erreichbar
- Schalter hatten keinen sichtbaren Fokusrahmen
- Ein Klick auf Sprungmarken setzte den Fokus auf den Widget-Button
- Das Widget überschrieb die Schriftgröße des Themes auch ohne aktive Einstellung
- Fehler im privaten Modus von Safari, wenn `localStorage` nicht verfügbar ist
- LICENSE enthielt keinen Lizenztext

### Update-Hinweise

- Nach dem Update `contao:migrate` ausführen (neue Felder; die Checkbox-Felder werden auf `boolean` umgestellt, bestehende Werte bleiben erhalten).
- Die neuen Schalter „Text-Abstände“ und „Hoher Kontrast“ sind standardmäßig aktiv und erscheinen damit nach dem Update im Panel. Sie lassen sich im Modul abschalten.
- Wer für den Modus ohne Ablenkungen einen eigenen Schaltertext eingetragen hat, sollte ihn an das neue Verhalten anpassen.
- Angepasste Templates aus 1.0 funktionieren weiter, erhalten aber die Verbesserungen am Panel und die neuen Optionen erst, wenn sie auf Basis des neuen Templates neu erstellt werden.

## [1.0.0]

- Erste Veröffentlichung
