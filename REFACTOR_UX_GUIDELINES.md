BS Admin Branding – Refactor & UX Guidelines
============================================

## 1. Zweck dieses Dokuments

Dieses Dokument definiert **verbindliche Leitplanken** für alle künftigen Änderungen am Plugin **BS Admin Branding**.  
Es soll verhindern, dass neue Logik unkontrolliert „drübergebaut“ wird, UX-Probleme nur technisch adressiert werden oder unnötige Komplexität entsteht.  
Alle späteren Refactorings, Bugfixes und Feature-Erweiterungen sollen sich an diesen Grundsätzen orientieren.

## 2. Refactor-Grundsätze

### 2.1 Bestehendes zuerst verstehen

- Vor jeder Änderung wird die **bestehende Logik gelesen und nachvollzogen**:
  - Welche Klasse ist wofür zuständig?
  - Wie hängen Settings, Defaults, Sanitizer, Assets, SettingsPage, MenuVisibility und FooterBranding zusammen?
- Änderungen sollen die bestehenden **Zuständigkeiten respektieren**:
  - Wenn `SettingsPage` für Rendering und Registrierung zuständig ist, bleibt das dort gebündelt.
  - Wenn `Sanitizer` Eingaben bereinigt, werden Sanitizing-Regeln bevorzugt dort ergänzt, nicht an verstreuten Stellen.

### 2.2 Nicht „drüberbauen“

- Keine neue Logik wird einfach zusätzlich über bestehende Logik gelegt, wenn sich die bestehende Logik **bereinigen oder verbessern** lässt.
- Es werden **keine parallelen Zuständigkeiten** erzeugt, z. B.:
  - keine zweite Menüsichtbarkeits-Logik neben `MenuVisibility`
  - keine zweite Settings-Verwaltung neben `Defaults`/`Sanitizer`
- Doppelstrukturen entstehen nur, wenn sie fachlich begründet und bewusst entschieden sind; der Normalfall ist **Anpassung statt Verdopplung**.

### 2.3 Teil-Refaktor vor Neuaufbau

- Bevor neue Klassen oder umfassende Abstraktionen eingeführt werden, wird geprüft:
  - Kann die bestehende Klasse durch **Refaktorieren** klarer und stabiler gemacht werden?
  - Reicht eine **behutsame Erweiterung** vorhandener Methoden aus?
- Neue Abstraktionen (z. B. zusätzliche Service-Klassen) werden nur geschaffen, wenn sie:
  - einen **klaren Mehrwert** in Wartbarkeit oder Verständlichkeit bringen,
  - nicht nur eine weitere indirekte Schicht erzeugen, die das Verständnis erschwert.
- Eine Komplett-Neuerfindung der Architektur ist **kein Ziel** dieses Projekts.

### 2.4 Unfertige Features bewusst behandeln

- Unfertige oder halbfertige Features (z. B. referenzierte, aber nicht vorhandene `login.css`/`editor.css`, Fonts) werden:
  - entweder **sauber vervollständigt** und produktiv nutzbar gemacht,
  - oder **bewusst deaktiviert/versteckt**, bis eine saubere Umsetzung erfolgt.
- Es bleibt **kein Zustand**, in dem Optionen sichtbar sind, aber faktisch nichts bewirken oder nur teilweise funktionieren.

### 2.5 Rückwärtskompatibel denken

- Bei Änderungen an Datenstrukturen (z. B. Optionen, `role_menu_rules`) wird geprüft:
  - Gibt es bereits Installationen mit gespeicherten Werten?
  - Muss eine einfache **Migration** oder ein Fallback implementiert werden?
- Keine stillen Breaking Changes:
  - Bestehende Installationen sollen nach Updates weiterhin funktionieren.
  - Wenn sich Verhalten ändern muss, geschieht dies bewusst und dokumentiert.

## 3. UX-Grundsätze

### 3.1 Klarheit vor Funktionsfülle

- Es ist besser, **wenige, verständliche Optionen** zu haben, als viele, verwirrende.
- Neue UI-Elemente werden nur hinzugefügt, wenn:
  - sie einen klaren Nutzwert haben,
  - die bestehende UI nicht zuerst strukturiert werden müsste.
- Komplexe Konfigurationen werden, wo möglich, vereinfacht oder erklärender gestaltet.

### 3.2 Informationsarchitektur vor Dekoration

- Vor visuellen Effekten und „coolen“ UI-Elementen steht die Frage:
  - Welche Informationen gehören zusammen?
  - Welche Tabs, Sektionen und Karten unterstützen das mentale Modell der Nutzer:innen?
- Tabs, Karten und Abschnitte werden so entworfen, dass sie **Inhalte strukturieren**, nicht nur die Oberfläche verschönern.

### 3.3 Menschliche Rückmeldung

- Wichtige Aktionen erhalten **sichtbares, verständliches Feedback**:
  - Speichern von Einstellungen
  - Wechsel der Rolle in der Rollen-/Menü-Konfiguration
  - Start und Ende einer späteren Rollen-Vorschau
- Meldungen sind:
  - in klarer, nicht-technischer Sprache formuliert,
  - gut sichtbar platziert,
  - nicht nur über die Standard-WordPress-Notices „abgehakt“, sondern bewusst in den Kontext eingebettet.

### 3.4 Sichtbarkeit von Systemzuständen

- Nutzer:innen müssen jederzeit erkennen können:
  - welcher Tab aktiv ist,
  - welche Rolle gerade konfiguriert wird,
  - ob zusätzliche Funktionen (z. B. eine Vorschau) aktiv sind,
  - ob bestimmte Funktionen gerade deaktiviert oder nicht verfügbar sind.
- Zustände wie „Vorschau aktiv“ oder „Option wirkt aktuell nicht, weil Asset fehlt“ werden **explizit** angezeigt.

### 3.5 Keine UX-Sackgassen

- Die UI darf keine Situationen erzeugen, in denen:
  - Nutzer:innen nicht mehr wissen, wie sie zurück zur Übersicht kommen,
  - Konfigurationen nur schwer rückgängig zu machen sind,
  - Administrator:innen „festhängen“.
- Besonders wichtig für die spätere Rollen-Vorschau:
  - Der Exit darf nicht von Menüs abhängen, die durch die Vorschau ausgeblendet werden könnten.
  - Ein klarer, global sichtbarer Rückweg ist Pflicht.

### 3.6 Accessibility als Grundanforderung

- Interaktive Elemente (insbesondere Tabs, Notices, Banner, Vorschau-Indikatoren) werden:
  - semantisch korrekt ausgezeichnet (Rollen, ARIA-Attribute),
  - per Tastatur bedienbar,
  - in ihrem Fokusverhalten durchdacht.
- Accessibility ist kein „Nice-to-have“, sondern fester Bestandteil der UX-Qualität.

## 4. Konkrete Leitplanken für dieses Plugin

### 4.1 Rollen-/Menülogik

- Die Rollen-/Menülogik bleibt ein **Sichtbarkeitsfilter**, kein Rechtesystem:
  - Sie steuert, welche Menüs sichtbar sind, nicht, ob Aktionen tatsächlich erlaubt sind.
- Es erfolgt **kein Umbau** in Richtung Capability-Management oder Sicherheitsarchitektur.
- Die UI soll diesen Charakter klar kommunizieren:
  - Hinweis, dass URL-Zugriffe und Berechtigungen weiterhin von WordPress/Core/Plugins kontrolliert werden.

### 4.2 Settings-Seite

- **Keine doppelten Felder**:
  - Jede Einstellung erscheint an genau einer Stelle in der UI.
- **Keine widersprüchlichen Strukturen**:
  - Tabs, Sections und Karten dürfen nicht gegeneinander arbeiten (z. B. Branding-Felder in mehreren Tabs).
- Die Struktur soll sich an einer **redaktionell verständlichen Logik** orientieren:
  - Welche Optionen betreffen Layout?
  - Welche betreffen Branding?
  - Welche betreffen Rollen/Menüs?

### 4.3 CSS-/Token-System

- Einheitliche Benennung:
  - Für jeden semantischen Zweck gibt es einen klar benannten Token (z. B. `--bsab-sidebar-bg`).
  - Alias-Variablen (z. B. `--sidebar-bg`) sind bewusst und dokumentiert, nicht zufällig gewachsen.
- Keine toten Variablen:
  - Tokens, die nirgendwo verwendet werden, werden entfernt oder klar als Reserve dokumentiert.
- Kein Nebeneinander widersprüchlicher Namen:
  - Z. B. wird vermieden, dass `--sidebar-submenu-bg` und `--sidebar-bg-sub` parallel für dasselbe stehen.

### 4.4 Optionen und Assets

- Optionen werden nur sichtbar, wenn sie **sinnvoll nutzbar** sind:
  - Wenn `enable_login_css` oder `enable_editor_css` gesetzt werden können, muss klar sein, was sie bewirken.
- Unfertige Asset-Features:
  - werden entweder vollständig implementiert,
  - oder so behandelt, dass sie Nutzer:innen nicht irreführen (z. B. Option ausgeblendet oder mit Hinweis versehen).

### 4.5 Spätere Rollen-Vorschau

- Die echten **Admin-Rechte und -Rollen** bleiben unangetastet:
  - Die Vorschau arbeitet mit einem **Simulationskontext**, nicht mit echten Rollenänderungen.
- Die Vorschau ist ein **temporärer Zustand**, der nur die Sichtbarkeit im UI beeinflusst.
- Der Exit aus der Vorschau:
  - ist global sichtbar (z. B. Toolbar/Banner),
  - ist unabhängig von der aktuellen Menüstruktur,
  - darf nicht durch die Simulation selbst unsichtbar werden.

## 5. Arbeitsregeln für spätere Umsetzungs-Prompts

- Pro Prompt möglichst nur **eine Phase oder ein klar abgegrenzter Teilbereich** bearbeiten:
  - z. B. „Slug-Handling in Phase 1“ oder „Tab-Persistenz in Phase 3“.
- Vor neuen Klassen wird geprüft:
  - Kann eine bestehende Klasse sinnvoll erweitert oder aufgeräumt werden?
  - Muss die Verantwortung wirklich aufgeteilt werden oder reicht eine klarere Struktur innerhalb der vorhandenen Klasse?
- Vor neuen UI-Elementen wird geprüft:
  - Ob bestehende UI zuerst **vereinfacht** oder klarer strukturiert werden sollte.
- Bei jeder Änderung sollte zumindest intern beantwortet werden:
  - Welches Problem löst diese Änderung?
  - Warum wurde **refaktoriert** statt einfach zusätzliche Logik aufzuschichten?

## 6. Definition guter Änderungen in diesem Projekt

Gute Änderungen im Kontext von BS Admin Branding zeichnen sich durch folgende Kriterien aus:

- **Weniger Redundanz**
  - Doppelte Logik, doppelte Felder und doppelte Variablen werden reduziert.
- **Klarere Zuständigkeiten**
  - Jede Klasse und jede Funktion hat einen nachvollziehbaren, begrenzten Verantwortungsbereich.
- **Verlässlichere Funktion**
  - Verhalten ist reproduzierbar und entspricht klar der UI-Beschreibung.
- **Verständlichere UI**
  - Nutzer:innen müssen weniger raten und können schneller erkennen, was eine Einstellung bewirkt.
- **Weniger Irreführung**
  - Optionen, die nichts oder nur halb funktionieren, werden vermieden.
  - Systemzustände (z. B. Vorschau aktiv) werden klar kommuniziert.
- **Bessere Wartbarkeit ohne unnötige Abstraktion**
  - Der Code wird verständlicher und einfacher anzupassen, ohne in unübersichtlichen Abstraktionshierarchien zu enden.

Diese Richtlinien sollen sicherstellen, dass das Plugin sich evolutionär verbessert, dabei aber übersichtlich, verständlich und stabil bleibt.

