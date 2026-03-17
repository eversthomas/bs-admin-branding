BS Admin Branding – Implementation Plan
======================================

## 1. Ausgangslage

BS Admin Branding ist ein WordPress-Plugin, das das Admin-Backend visuell anpasst, Footer-/Branding-Informationen steuert und pro Benutzerrolle die Sichtbarkeit von Admin-Menüs konfigurierbar macht.  
Der aktuelle Stand ist ein **solides, aber in Teilen inkonsistentes MVP**:

- **Stärken**
  - modulare, objektorientierte Grundarchitektur mit klaren Namespaces
  - sinnvolle Trennung in `Assets`, `SettingsPage`, `MenuVisibility`, `FooterBranding`, `Defaults` und `Sanitizer`
  - saubere Nutzung der WordPress-Settings-API
  - robuste Basis, um Funktionen behutsam zu erweitern
- **Baustellen**
  - problematisches Slug-Handling bei Menü-/Submenü-Sichtbarkeit (zu aggressive Sanitization)
  - inkonsistentes CSS-Token-/Variablen-System zwischen PHP und CSS
  - referenzierte, aber fehlende oder unfertige Assets (`editor.css`, `login.css`, Fonts)
  - doppelte Darstellung von Branding-Feldern in der Settings-UI
  - unfertige Tab-UX (keine Persistenz, schwache Accessibility)
  - fehlendes oder unzureichendes Nutzerfeedback beim Speichern
  - unübersichtliche Rollen-/Menü-UI
  - technische Schulden liegen überwiegend in UX/Styling, nicht in der Grundarchitektur

Die Architektur ist eine gute Grundlage, benötigt aber gezielte Teil-Refaktoren, um Konsistenz, UX und Erweiterbarkeit zu sichern.

## 2. Strategische Leitidee

Die Weiterentwicklung des Plugins erfolgt als **gezielter Teil-Refaktor mit klaren Phasen**, nicht als Aufschichtung immer neuer Logik über bestehende Strukturen.

- Statt „drüberbauen“ sollen bestehende Komponenten bevorzugt **bereinigt, geschärft oder ersetzt** werden.
- Vorrang haben:
  - **funktionale Stabilisierung** (Slug-Handling, Settings-Klarheit, Feedback)
  - **Systemkonsistenz** (CSS-Token, Assets)
  - **UX-Klarheit** (Tabs, Rollen-/Menü-UI)
  - **saubere Vorbereitung** der späteren Admin-Rollen-Vorschau.
- Erst wenn diese Grundlagen stabil sind, wird die eigentliche Rollen-Vorschau implementiert.

Ziel ist ein Plugin, das intern klar strukturiert und von außen verständlich ist – ohne unnötige Parallelstrukturen und ohne grundlegenden Neubau der Architektur.

## 3. Nicht-Ziele

Die folgenden Punkte sind **explizit nicht** Ziel dieses Projekts:

- **Kein Umbau zu einem Rechtesystem**
  - Die Rollen-/Menülogik bleibt ein **reiner Sichtbarkeitsfilter**.
  - Capabilities, Zugriffsrechte und Sicherheitslogik bleiben bei WordPress und anderen Plugins.
- **Keine unkontrollierte Komplett-Neustrukturierung**
  - Die bestehende Architektur wird nicht grundlos „eingestampft“.
  - Stattdessen werden gezielt Teilbereiche refaktoriert.
- **Keine unnötige Vermehrung von Optionen**
  - Neue Optionen nur, wenn sie fachlich und UX-seitig gerechtfertigt sind.
  - Bestehende inkonsistente oder irreführende Optionen werden bereinigt.
- **Keine UX-Modernisierung um der Modernisierung willen**
  - UX-Änderungen müssen die **Verständlichkeit und Bedienbarkeit** verbessern.
  - Reine kosmetische Änderungen ohne Nutzen für Nutzer:innen sind nachrangig.

## 4. Leitprinzipien

- **Zuerst funktionale Korrektheit, dann UX-Verfeinerung**
  - Fehlerquellen (z. B. Slug-Handling, doppelte Felder, fehlende Notices) werden vor optischer Verfeinerung adressiert.
- **Zuerst Systemkonsistenz, dann Feature-Ausbau**
  - CSS-Tokens, Assets und Optionslogik werden konsolidiert, bevor neue Features darauf aufbauen.
- **Sichtbarkeitsfilter bleibt Sichtbarkeitsfilter**
  - Die Rollen-/Menülogik steuert ausschließlich die Sichtbarkeit von Menüs, ersetzt aber keine Berechtigungs- oder Sicherheitsmechanismen.
- **Keine parallelen Logikschichten**
  - Bestehende Logik wird bevorzugt angepasst, nicht verdoppelt.
  - Neue Schichten werden nur eingeführt, wenn bestehende Strukturen dafür nachweislich ungeeignet sind.
- **Admin-Vorschau nur mit sicherem Exit**
  - Eine spätere Rollen-Vorschau darf Administrator:innen nie in Sackgassen bringen.
  - Der Exit aus der Vorschau ist global verfügbar und unabhängig von der simulierten Menüstruktur.
- **Inkrementelle, überprüfbare Schritte**
  - Jede Phase ist fachlich abgegrenzt, technisch nachvollziehbar und testbar.
  - Nach jeder Phase gibt es eine kurze Überprüfung, bevor weitergebaut wird.

## 5. Phasenübersicht

1. **Phase 1 – Funktionale Stabilisierung & Settings-Basis**  
   Slug-Handling für Menü-/Submenü-Sichtbarkeit korrigieren, doppelte Branding-Felder bereinigen, sichtbares Settings-Feedback einführen.

2. **Phase 2 – Konsolidierung des CSS-Token- und Asset-Systems**  
   Design-Tokens inventarisieren und harmonisieren, tote/fehlende Variablen bereinigen, Umgang mit `login.css`, `editor.css` und Fonts klären.

3. **Phase 3 – UX-Verbesserungen in Settings-UI, Tabs und Rollen-/Menü-Oberfläche**  
   Tab-Persistenz und Accessibility, klarere Informationsarchitektur, verständlichere Rollen-/Menü-UI, expliziter Hinweis auf reine Sichtbarkeit.

4. **Phase 4 – Technische Vorbereitung der Admin-Rollen-Vorschau**  
   Simulationskontext vorbereiten, klare Trennung zwischen echter und simulierter Rolle, `MenuVisibility` so erweitern, dass Vorschau andockbar ist.

5. **Phase 5 – Umsetzung der Admin-Rollen-Vorschau**  
   Vorschau-UI, globaler Vorschau-Hinweis/Toolbar-Element, robuster Exit, garantierter Rückweg zur normalen Admin-Ansicht.

## 6. Ausführliche Phasenbeschreibung

### Phase 1 – Funktionale Stabilisierung & Settings-Basis

**Ziel**  
Rollen-/Menü-Sichtbarkeit und Einstellungen sollen **verlässlich** funktionieren: Regeln greifen technisch korrekt, Branding-Felder erscheinen nicht doppelt, und nach dem Speichern gibt es sichtbares Feedback.

**Status**  
Abgeschlossen und getestet (Stand: 2026-03-17).

**Warum jetzt**  
Solange Slug-Handling, doppelte Felder und fehlende Notices bestehen, untergraben sie das Vertrauen in das Plugin. Diese Grundprobleme müssen vor UX- und Styling-Arbeiten behoben werden.

**Schwerpunkte**
- Slug-Handling für Menü-/Submenü-Sichtbarkeit
- Bereinigung der doppelten Branding-Felder in der Settings-UI
- Einführung sichtbaren Settings-Feedbacks

**Betroffene Bereiche**
- `src/Admin/SettingsPage.php`
- `src/Settings/Sanitizer.php`
- `src/Admin/MenuVisibility.php`

**Kernaufgaben**
- Slug-Handling:
  - Ermitteln, wie Slugs im Admin-Menü tatsächlich aussehen (einschließlich Query-String-Slugs).
  - Sanitizing-Strategie für `role_menu_rules` so anpassen, dass gespeicherte Slugs exakt zu den von `remove_menu_page()` und `remove_submenu_page()` erwarteten Werten passen.
  - Sicherstellen, dass in `SettingsPage` die Checkbox-Werte und die gespeicherten Slugs 1:1 übereinstimmen.
- Doppelte Branding-Felder:
  - Entscheiden, in welchem Tab Branding-Felder dauerhaft angezeigt werden sollen (z. B. nur im Branding-Tab).
  - `render_page()` so anpassen, dass `bsab_section_branding` nur einmal gerendert wird.
- Settings-Feedback:
  - `settings_errors()` (oder vergleichbare Mechanik) auf der Settings-Seite integrieren.
  - Prüfen, dass Erfolgs- und Fehlermeldungen klar sichtbar und verständlich sind.

**Definition of Done**
- Menü- und Submenü-Regeln wirken im Admin sichtbar und reproduzierbar wie konfiguriert.
- Branding-bezogene Felder werden an genau einem Ort und in einem passenden Tab dargestellt.
- Nach Speichern von Einstellungen erscheinen nachvollziehbare Notices auf der BS-Admin-Branding-Seite.

**Hinweise / Risiken**
- Änderungen am Slug-Handling können bestehende Konfigurationen beeinflussen; es ist zu prüfen, ob eine Migration oder ein Fallback nötig ist.
- Notices müssen sauber im bestehenden Layout platziert werden, ohne das Karten-/Tab-Layout zu „sprengen“.

---

### Phase 2 – Konsolidierung des CSS-Token- und Asset-Systems

**Ziel**  
Das CSS-Token- und Asset-System soll **konsistent, vollständig und beherrschbar** sein: gesetzte Variablen werden genutzt, genutzte Variablen werden gesetzt, und referenzierte Assets existieren oder sind bewusst deaktiviert.

**Status**  
Abgeschlossen und getestet (Stand: 2026-03-17).

**Warum jetzt**  
Nach der funktionalen Stabilisierung ist es sinnvoll, das visuelle Fundament zu konsolidieren. Ein stabiles Token-System verhindert, dass spätere UI-Änderungen zu schwer nachvollziehbaren Stilbrüchen führen.

**Schwerpunkte**
- Inventur und Harmonisierung der CSS-Variablen
- Auflösen toter oder fehlender Tokens
- Klärung von login/editor-Styles und Fonts

**Betroffene Bereiche**
- `src/Admin/Assets.php`
- `assets/css/admin.css`
- ggf. `assets/css/login.css`
- ggf. `assets/css/editor.css`
- ggf. Fonts-Verzeichnis
- `src/Admin/SettingsPage.php` (für `data-css-vars`)

**Kernaufgaben**
- Token-Inventur:
  - Alle in PHP gesetzten Variablen (`print_css_variables()`, `print_editor_css_variables()`) erfassen.
  - Alle in `admin.css` verwendeten Variablen erfassen.
  - Gegenüberstellung: gesetzt & genutzt, gesetzt & ungenutzt, genutzt & ungesetzt.
- Namensschema:
  - Gemeinsame Namenskonvention definieren (z. B. `--bsab-*` plus eindeutige Kompatibilitätsvariablen).
  - CSS oder PHP-Ausgabe so anpassen, dass jede verwendete Variable eindeutig gesetzt wird.
  - Basis-Variablen wie `--body-bg`, `--sidebar-border`, `--sidebar-text-sub`, `--adminbar-border` konsistent definieren oder entfernen.
- Assets:
  - Entscheidung treffen, ob `enable_login_css` und `enable_editor_css` im MVP bereits produktiv sein sollen.
  - Falls ja: minimal sinnvolle `login.css` und `editor.css` erstellen, basierend auf dem gemeinsamen Token-Set.
  - Falls nein: Optionen entsprechend kennzeichnen oder in der UI ausblenden/deaktivieren, um keine falschen Erwartungen zu wecken.
  - Fonts-Einbindung klären (Fonts liefern oder Referenzen entschärfen).
- Live-Preview:
  - Sicherstellen, dass `data-css-vars` pro Farbfeld korrekt gesetzt sind und zu den finalen Tokens passen.

**Definition of Done**
- Es existiert ein klar dokumentiertes Set an CSS-Variablen, das in PHP und CSS konsistent genutzt wird.
- Es gibt keine offensichtlichen toten oder fehlenden Variablen in `admin.css`.
- Optionen für Login-/Editor-Styling sind entweder funktionsfähig oder bewusst als „(noch) nicht verfügbar“ behandelt.
- Font-Referenzen erzeugen keine unnötigen Fehler (404) mehr.

**Hinweise / Risiken**
- Änderungen am Token-System können bestehende visuelle Anpassungen beeinflussen; möglichst rückwärtskompatibel arbeiten.
- Neue CSS-Dateien müssen behutsam in die WordPress-Umgebung integriert werden, um Konflikte mit Themes und Plugins zu minimieren.

---

### Phase 3 – UX-Verbesserungen in Settings-UI, Tabs und Rollen-/Menü-Oberfläche

**Ziel**  
Die Settings-Oberfläche soll **verständlich gegliedert, zugänglich und gut bedienbar** sein. Tabs, Informationsarchitektur und Rollen-/Menü-UI sind so gestaltet, dass Nutzer:innen schnell verstehen, was sie wo konfigurieren.

**Status**  
Abgeschlossen und getestet (Stand: 2026-03-17).

**Warum jetzt**  
Nachdem Funktionalität und Styling-Basis stabiler sind, lohnt sich der Fokus auf Bedienbarkeit. Eine gute UX macht die vorhandenen Features erst voll nutzbar.

**Schwerpunkte**
- Tab-Verhalten (Persistenz, Accessibility, Tastaturbedienung)
- Klare Informationsarchitektur auf der Settings-Seite
- Verbesserte Rollen-/Menü-UI
- Expliziter Hinweis, dass Rollen-Menüs nur Sichtbarkeit steuern

**Betroffene Bereiche**
- `src/Admin/SettingsPage.php`
- `assets/js/settings.js`
- `assets/css/settings.css`

**Kernaufgaben**
- Tabs:
  - Mechanismus zur Persistenz des aktiven Tabs (z. B. URL-Parameter oder `localStorage`).
  - ARIA-Rollen und -Attribute für Tabs und Panels ergänzen.
  - Optional: Tastatursteuerung implementieren (Pfeiltasten, Fokus).
- Informationsarchitektur:
  - überprüfen, ob Tab-Namen und Inhalte logisch zusammenpassen („Allgemein“, „Sidebar“, „Content“, „Branding“, „Rollen & Menüs“).
  - Branding-, Layout- und Farboptionen so gruppieren, dass sie aus redaktioneller Sicht Sinn ergeben.
  - Hilfetexte/Descriptions ergänzen oder schärfen, wo nötig.
- Rollen-/Menü-UI:
  - visuelle Strukturierung der Menü-Liste verbessern (Spacing, Gliederung, ggf. Gruppen).
  - Beschreibungstexte für Häkchen anpassen, damit klar ist, was Ein-/Ausblenden bewirkt.
  - sicherstellen, dass Rollenwechsel nicht „aus dem Tab wirft“ (Synchronität mit Tab-Persistenz).
- Sichtbarkeits-Hinweis:
  - klaren Hinweis ergänzen, dass die Rollen-/Menülogik nur UI-Sichtbarkeit steuert und kein Rechtesystem ist.

**Definition of Done**
- Tabs behalten ihre Auswahl über Reloads, Rollenwechsel und Speichern hinweg.
- Die Settings-Seite wirkt logisch strukturiert und ist für typische WordPress-Nutzer:innen verständlich.
- Die Rollen-/Menü-UI ist deutlich besser lesbar und erklärt ihren Zweck (Sichtbarkeitsfilter).

**Hinweise / Risiken**
- Zusätzliche JS-Logik darf keine instabilen Zustände erzeugen (defensive Checks).
- UX-Optimierungen sollten bestehende Workflows verbessern, nicht verschlechtern.

---

### Phase 4 – Technische Vorbereitung der Admin-Rollen-Vorschau

**Ziel**  
Es entsteht eine **Backend-Architektur**, die eine spätere Rollen-Vorschau ermöglicht, ohne bestehendes Verhalten zu verändern. Ein klar definierter Simulationskontext wird eingeführt, der Menüs temporär aus Sicht einer Rolle darstellen kann.

**Status**  
Vorbereitet, ohne Verhaltensänderung (Stand: 2026-03-17).

**Warum jetzt**  
Nach Stabilisierung und UX-Verbesserung ist die Codebasis bereit, um vorsichtig neue Architekturbausteine einzuführen, ohne das aktuelle Verhalten zu riskieren.

**Schwerpunkte**
- Einführung eines Simulationskontexts für Rollen
- Trennung von „echter“ und „simulierter“ Rolle
- Vorbereitung von `MenuVisibility` auf die Vorschau

**Betroffene Bereiche**
- `src/Admin/MenuVisibility.php`
- ggf. neue Hilfsklasse(n) (z. B. `RoleContext`, `RolePreviewContext`)
- `src/Settings/Sanitizer.php` (falls Vorschau-bezogene Einstellungen vorbereitet werden)

**Kernaufgaben**
- Simulationskonzept:
  - definieren, wie ein temporärer Rollen-Kontext technisch abgebildet wird (user_meta, Cookie, Transient, Query-Parameter + Nonce o. Ä.).
  - klären, wie lange eine Simulation gültig sein soll und wann sie endet.
- Abstraktion der Rollen-Ermittlung:
  - in `MenuVisibility` eine Schicht einführen, die entscheidet, welche „aktuellen“ Rollen für die Sichtbarkeitsregeln gelten (real vs. simuliert).
  - sicherstellen, dass `administrator` real weiterhin nie eingeschränkt wird, die Vorschau aber trotzdem eine simulierte Sicht verwenden kann.
- Datenmodell:
  - prüfen, ob `role_menu_rules` ohne Anpassung für die Vorschau genutzt werden kann (Ziel: keine Duplikate).

**Definition of Done**
- Es existiert eine klar definierte Stelle im Code, an der entschieden wird, ob und welche Rolle simuliert wird.
- Ohne aktivierte Vorschau verhält sich das Plugin exakt wie zuvor.
- `MenuVisibility` ist so aufgebaut, dass eine spätere Vorschau-UI einfach andocken kann.

**Hinweise / Risiken**
- Falsche Entkopplung kann dazu führen, dass an verschiedenen Stellen unterschiedliche Rollenbegriffe verwendet werden; Konsistenz ist entscheidend.
- Die Vorbereitung darf bestehende Performance oder Stabilität nicht negativ beeinflussen.

---

### Phase 5 – Umsetzung der Admin-Rollen-Vorschau

**Ziel**  
Administrator:innen können das Backend aus Sicht einer ausgewählten Rolle **temporär simulieren**, ohne ihre echten Rechte zu ändern, und jederzeit sicher zur normalen Admin-Ansicht zurückkehren.

**Warum jetzt**  
Die Rollen-Vorschau setzt auf einer stabilen Funktions-, UX- und Architektur-Basis auf. Erst nach Abschluss der Phasen 1–4 ist das Risiko gering, dass die Vorschau das System destabilisiert.

**Schwerpunkte**
- Vorschau-UI (Start, Statusanzeige, Ende)
- global sichtbarer Vorschau-Indikator
- robuster Exit-Mechanismus, unabhängig von der simulierten Menüstruktur

**Betroffene Bereiche**
- `src/Admin/MenuVisibility.php` (Nutzung des Simulationskontexts)
- neue Admin-Komponenten (z. B. für Toolbar/Banner)
- `src/Admin/Assets.php` (Styles für Vorschau-Indikator)
- Admin-CSS/JS (für Banner und Interaktionen)

**Kernaufgaben**
- Aktivierung:
  - UI-Element(e) definieren, über die Administrator:innen eine Rollen-Vorschau starten können (z. B. von der Settings-Seite oder der Toolbar aus).
  - beim Start der Vorschau den Simulationskontext setzen (inkl. Security via Nonce) und Nutzer:innen sauber weiterleiten.
- Status & Hinweis:
  - global sichtbaren Hinweis (Banner oder Toolbar-Element) implementieren, der „Vorschau aktiv: [Rolle]“ anzeigt.
  - optisch und textlich klar machen, dass es sich um eine Simulation handelt.
- Beenden:
  - einen immer sichtbaren Exit-Link-/Button implementieren, der unabhängig von Menüs erreichbar ist.
  - beim Exit den Simulationskontext auflösen und auf eine sichere Seite (z. B. Dashboard) zurückführen.
- Navigation:
  - sicherstellen, dass der Simulationskontext konsistent über Seitenaufrufe hinweg angewendet wird, bis er explizit beendet wird.

**Definition of Done**
- Administrator:innen können eine Rolle auswählen und das Menü/Backend aus dieser Perspektive betrachten.
- Ein deutlicher Hinweis zeigt den aktiven Vorschau-Modus an.
- Ein global sichtbarer Exit beendet die Vorschau jederzeit, ohne von Menüs abzuhängen.
- Echte Rollen/Capabilities werden durch die Vorschau nicht verändert.

**Hinweise / Risiken**
- Exit-Mechanismus darf niemals nur an einen Menüpunkt gebunden sein, der in der Simulation verborgen sein könnte.
- UI-Feedback zur Vorschau (Start/Ende) muss klar genug sein, um Missverständnisse zu vermeiden.

## 7. Reihenfolge der tatsächlichen Umsetzung

- **Zuerst**: Phase 1 – Funktionale Stabilisierung & Settings-Basis  
  Höchste Priorität, da hier direkte Fehlfunktionen und Unklarheiten adressiert werden.

- **Danach**: Phase 2 – Konsolidierung des CSS-Token- und Asset-Systems  
  Stabilisiert das visuelle Fundament und verhindert spätere, schwer erklärbare Stilprobleme.

- **Anschließend**: Phase 3 – UX-Verbesserungen in Settings-UI, Tabs und Rollen-/Menü-Oberfläche  
  Macht die bestehende Funktionalität besser erfahrbar und reduziert Reibung in der Bedienung.

- **Später**: Phase 4 – Technische Vorbereitung der Admin-Rollen-Vorschau  
  Bereitet die Architektur so vor, dass die Vorschau sauber integriert werden kann.

- **Zuletzt**: Phase 5 – Umsetzung der Admin-Rollen-Vorschau  
  Liefert das sichtbare neue Feature, baut aber vollständig auf den stabilisierten Grundlagen auf.

## 8. Offene Architekturentscheidungen

- **Speicherort des Vorschau-Zustands**
  - `user_meta`, Cookie, Transient oder Kombination – Abwägung zwischen Sicherheit, Persistenz und Einfachheit.
- **Lebensdauer der Vorschau**
  - Nur bis zum expliziten Exit oder zusätzlich automatische Begrenzung (z. B. Logout, Timeout).
- **Platzierung von Exit und Vorschau-Indikator**
  - Toolbar, fester Banner unterhalb der Adminbar oder Kombination.
- **Umfang der V1-Vorschau**
  - Nur Menü-/Sidebar-Sichtbarkeit oder auch weitere UI-Aspekte (z. B. bestimmte Notices).

Diese Entscheidungen sollten vor Beginn der Phasen 4 und 5 bewusst getroffen und dokumentiert werden.

## 9. Arbeitsmodus für spätere Umsetzungsphasen

- Jede Phase wird **separat** geplant und umgesetzt.
- In einzelnen Umsetzungs-Prompts wird möglichst nur eine Phase oder ein klar abgegrenzter Teilbereich verändert.
- Zwischen den Phasen erfolgt jeweils eine kurze Überprüfung:
  - Funktionalität (wirkt alles wie erwartet?)
  - UX (ist die Bedienung klarer geworden?)
  - Codequalität (wurden Redundanzen reduziert, Zuständigkeiten geschärft?)

Die Umsetzung soll Schritt für Schritt erfolgen, mit nachvollziehbaren Änderungen und ohne Vermischung mehrerer großer Umbauten in einem Schritt.

