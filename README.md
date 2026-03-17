BS Admin Branding
=================

Ein WordPress-Plugin zur konsistenten Gestaltung und zum Branding des Admin-Backends – inklusive rollenbasierter **Sichtbarkeitssteuerung** für Admin-Menüs und optionaler **Rollen-Vorschau** für Administrator:innen.

> Hinweis: Die Rollen-/Menülogik ist bewusst nur ein **Sichtbarkeitsfilter** und ersetzt kein Rechtesystem oder Capability-Management.

## Funktionsumfang

- **Admin-Branding & Styling**
  - Anpassung des Admin-Backends an ein helles, modernes Design.
  - Steuerung von:
    - Sidebar-Hintergrund-, Hover- und Aktiv-Farben
    - Adminbar-Farben
    - Content-Hintergrund und „Card“-Flächen
    - Akzentfarben für Buttons und Links
  - Optionale Verwendung der Schrift „Figtree“ (falls systemweit verfügbar), sonst sauberer Fallback auf Standardsystemschriften.

- **Layout-Optionen**
  - Einstellbare Sidebar-Breite (`sidebar_width`).
  - Maximale Inhaltsbreite (`content_max_width`).
  - Globaler Border-Radius (`border_radius`) für Karten/Boxen.

- **Branding im Footer**
  - Eigener Footer-Text und optionale Footer-URL.
  - Aktivierung/Deaktivierung des Footer-Brandings per Option.

- **Rollenbasierte Menü-Sichtbarkeit (kein Rechtesystem)**
  - Pro Standardrolle (und ggf. weiteren Rollen) können:
    - Hauptmenüpunkte ausgeblendet werden.
    - Untermenüpunkte (Submenüs) selektiv pro Hauptmenü verborgen werden.
  - Wichtig:
    - Es werden **nur Menüs im UI ausgeblendet**.
    - Rechte, Zugriffe und Capabilities bleiben vollständig bei WordPress und anderen Plugins.
    - Administrator:innen behalten standardmäßig immer alle Menüs (außer in der Rollen-Vorschau, siehe unten).

- **Admin-Rollen-Vorschau (Simulationsmodus für Administrator:innen)**
  - Administrator:innen können das Backend temporär aus Sicht einer ausgewählten Rolle simulieren:
    - Menüs werden so angezeigt, wie sie für diese Rolle konfiguriert sind.
    - Die **echten Rollen und Rechte des Admins werden nicht verändert**.
  - Umsetzung:
    - Vorschaustatus wird pro Admin-User in `user_meta` gespeichert.
    - Global sichtbarer Hinweis (Admin-Notice) mit aktivem Rollennamen.
    - Toolbar-Eintrag „Rollen-Vorschau: [Rolle]“ mit sicherem „Vorschau beenden“-Link.
  - Exit:
    - Der Exit ist nicht an Menüs der simulierten Rolle gekoppelt und bleibt immer sichtbar.
    - Ein Klick beendet die Vorschau und führt zurück in die normale Admin-Ansicht.

## Installation

1. Plugin-Ordner `bs-admin-branding` nach `wp-content/plugins/` kopieren.
2. Im WordPress-Backend unter `Plugins` → `BS Admin Branding` aktivieren.
3. Im Menü `Admin Branding` (Top-Level im Admin-Menü) die gewünschten Einstellungen vornehmen.

## Bedienung

### Allgemeine Einstellungen

Im Tab **„Allgemein“**:

- Admin-CSS aktivieren/deaktivieren.
- Vorbereitung für Login-/Editor-CSS (in der aktuellen Version ggf. noch ohne direkten Effekt – in der UI entsprechend gekennzeichnet).
- Umschalten zwischen System-Schrift und Figtree (sofern verfügbar).
- Footer-Branding aktivieren/deaktivieren.

### Layout & Content

Im Tab **„Content & Layout“**:

- Sidebar-Breite, Content-Maximalbreite und Border-Radius konfigurieren.
- Farben für:
  - Seitenhintergrund (Content-Bereich)
  - Karten-Hintergrund
  - Karten-Text

### Sidebar & Farben

Im Tab **„Sidebar“**:

- Farben für:
  - Sidebar-Hintergrund
  - Untermenü-Hintergrund und Hover
  - Sidebar-Text, Hover-Text und aktive Elemente
  - Adminbar-Hintergrund und -Text
  - Akzentfarben (Buttons, Links, Status-Elemente)

### Branding

Im Tab **„Branding“**:

- Footer-Text und Footer-URL einstellen, sofern Footer-Branding aktiviert ist.

### Rollen & Menüs (Sichtbarkeitsfilter)

Im Tab **„Rollen & Menüs“**:

- Eine Rolle (außer Administrator) auswählen.
- Haupt- und Untermenüpunkte per Checkbox für diese Rolle ausblenden.
- Hinweis im UI macht deutlich:
  - Es wird **nur die Sichtbarkeit** der Menüs gesteuert.
  - Rechte und Zugriffe bleiben vom Plugin unberührt.

### Rollen-Vorschau für Administrator:innen

Ebenfalls im Tab **„Rollen & Menüs“**:

- Als Administrator kann für die aktuell gewählte Rolle eine Vorschau gestartet werden:
  - Button „Rollen-Vorschau für diese Rolle starten“.
  - Danach:
    - globaler Hinweis im Backend („Rollen-Vorschau aktiv …“),
    - Toolbar-Eintrag mit der simulierten Rolle.
- Vorschau beenden:
  - über den Button im Hinweis oder den Toolbar-Eintrag.
  - der Vorschaustatus wird aus `user_meta` gelöscht,
  - die Menüs erscheinen wieder wie in der normalen Admin-Sicht.

## Grenzen & Nicht-Ziele

- Keine Capability- oder Rechteverwaltung:
  - URL-Zugriffe, REST-Endpunkte und API-Rechte bleiben unverändert.
  - Das Plugin ist **kein Sicherheits-Plugin**.
- Keine komplette Neugestaltung des Admins:
  - Ziel ist ein konsistentes, modernes Layout, kein vollumfängliches Admin-Framework.

## Systemvoraussetzungen

- WordPress 6.x (empfohlen, getestet mit aktuellen Versionen zum Zeitpunkt der Entwicklung).
- PHP 8.0 oder höher (empfohlen).

## Lizenz

Dieses Plugin wird unter der **GNU General Public License, Version 3 (GPL-3.0)** veröffentlicht.

- Copyright (c)  
  Der/Die jeweilige Autor:in dieses Plugins.
- Du darfst:
  - den Code ausführen, studieren, verändern und weiterverteilen,
  - Forks erstellen und an eigene Bedürfnisse anpassen,
  - das Plugin in freien oder kommerziellen Projekten einsetzen,
    solange die Bedingungen der GPL-3.0 eingehalten werden.

Kurzfassung (nicht rechtsverbindlich):

- Wenn du dieses Plugin oder abgeleitete Werke weitergibst, muss dies ebenfalls unter einer **GPL-kompatiblen Lizenz** geschehen.
- Es gibt **keine Garantie**; Nutzung auf eigenes Risiko.

Volltext der Lizenz:  
`https://www.gnu.org/licenses/gpl-3.0.html`

