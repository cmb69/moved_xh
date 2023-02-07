# Moved_XH

Moved_XH ermöglicht die Behandlung von Seiten, die umbenannt wurden. Direkte
Links zu solchen Seiten ergeben normalerweise einen 404 Not found (nicht
gefunden) Fehler, aber Moved_XH macht es möglich eingehende Anfragen auf
eine andere Seite weiter zu leiten, oder die Seite als entfernt zu
kennzeichnen. Es wird entsprechende Information zurück geliefert, die Bots
über die Änderung informiert, was besonders wichtig bezüglich Suchmaschinen
ist, die dann die URL der Seite ändern bzw. sie aus dem Index entfernen
können.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
  - [Weiterleitungen](#weiterleitungen)
  - [Entfernt](#entfernt)
  - [Platzhalter](#platzhalter)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Moved_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es erfordert CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.

## Download

Das [aktuelle Release](https://github.com/cmb69/moved_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

The Installation erfolgt wie bei vielen anderen CMSimple_XH Plugins auch.
Im [CMSimple_XH Wiki](https://wiki.cmsimple-xh.org/de/?fuer-anwender/arbeiten-mit-dem-cms/plugins)
finden Sie weitere Informationen.

1. **Sichern Sie die Daten auf Ihrem Server.**
1. Entpacken Sie die ZIP-Datei auf Ihrem Computer.
1. Laden Sie das gesamte Verzeichnis `moved/` auf Ihren Server in
   das `plugins/` Verzeichnis von CMSimple_XH hoch.
1. Vergeben Sie Schreibrechte für die Unterverzeichnisse `css/` und
   `languages/`.
1. Navigieren Sie zu `Plugins` → `Moved`, und prüfen Sie, ob alle
   Voraussetzungen für den einwandfreien Betrieb erfüllt sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen
CMSimple_XH Plugins auch im Administrationsbereich der Homepage.
Wählen Sie `Plugins` → `Moved`.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen, falls keine entsprechende
Sprachdatei zur Verfügung steht, oder sie entsprechend Ihren Anforderungen
anpassen.

Das Aussehen von Moved_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Moved_XH speichert seine Daten separat für jede Sprache der CMSimple_XH
Installation in einer Datei `moved.txt` im jeweiligen
`content/` Ordner. Eingehende Anfragen zu Seiten, die nicht
existieren und für die keine Regel in `moved.txt` angegeben
wurde, werden in der Protokolldatei von CMSimple_XH protokolliert. Die
Beschreibung des Protokolleintrags enthält die angefragte Seite und den
Referrer, falls bekannt.

Die Regeln können in der Plugin-Administration unter `Einstellungen`
bearbeitet werden. Jede Zeile der Datei stellt eine Regel dar. Regeln
bestehen aus einer bzw. zwei so genannten *Seiten-URL*s. Technisch
ausgedrückt ist eine *Seiten-URL* der Name des ersten Parameters des
Query-Strings, d.h. alles zwischen dem Fragezeichen (`?`) und dem
ersten Kaufmanns-Und (`&`) bzw. dem Ende der URL, wenn darin
kein Kaufmanns-Und vorkommt. Es wird empfohlen die *Seiten-URL*s aus
der Adressleiste des Browsers zu kopieren anstatt sie manuell einzugeben, da
es einige Überraschungen gibt. Zum Beispiel ist die *Seiten-URL* der
erfundenen Seite [Fahrvergnügen](https://www.example.com/?Fahrvergn%C3%BCgen)
`Fahrvergn%C3%BCgen`, und nicht `Fahrvergnügen` wie man erwarten könnte.

Es gibt zwei verschiedene Regeltypen:

### Weiterleitungen

Weiterleitungs-Regeln leiten eingehende Anfragen für eine alte
*Seiten-URL* auf eine neue *Seiten-URL* oder eine externe URL
(diese muss vollständig qualifiziert sein, d.h. mit der Angabe des
Protokolls, z.B. `https://`, beginnen) um. Beide URLs werden durch
ein Gleichheitszeichen (`=`) getrennt.

Einige Beispiele:

Sie bauen Ihre Website um und möchten die Seite *Eichen* von der obersten
Ebene auf die zweite Ebene unter *Bäume* verschieben; also ergänzen Sie
folgende Regel:

    Eichen=B%C3%A4ume:Eichen

Sie haben die Seite *Eichen* in eine andere CMSimple_XH Installation
verschoben; also ergänzen Sie folgende Regel:

    Eichen=http://www.example.com/baeume/?Eichen

Sie haben ein Upgrade von einer *ISO-8859-1* kodierten CMSimple Version
auf eine *UTF-8* kodierte CMSimple_XH Installation durchgeführt, und
Sie haben eine Seite "Fahrvergnügen"; also ergänzen Sie folgende Regel:

    Fahrverg%FCgen=Fahrvergn%C3%BCgen

### Entfernt

Entfernt-Regeln informieren Besucher, dass eine Seite nicht mehr existiert.
Sie bestehen aus der *Seiten-URL* der entfernten Seite.

Zum Beispiel haben Sie die Seite *Vorübergehende Information* entfernt, weil Sie nicht
mehr gebraucht wird; also ergänzen Sie folgende Regel:

    Vor%C3%BCbergehende_Information

### Platzhalter

Die alte *Seiten-URL* der Regeln kann Platzhalter enthalten, wobei ein
`*` einer beliebigen Anzahl von Zeichen und `?`
einem einzelnen Zeichen entspricht. Nur die erste passende Regel wird
jeweils verwendet; andere werden für die Anfrage einfach ignoriert.
Platzhalter bieten keine neuen Möglichkeiten, sondern sind einfach dazu
gedacht, Wiederholungen ähnlicher Regeln zu vermeiden.
Zum Beispiel:

Sie haben die Seiten "Vorübergehende Information 1" und "Vorübergehende
Information 2" entfernt, weil Sie nicht mehr gebraucht werden; also könnten
Sie folgende Regeln ergänzen:

    Vor%C3%BCbergehende_Information_1
    Vor%C3%BCbergehende_Information_2

Durch die Verwendung eines Platzhalters können sie allerdings vereinfachen:

    Vor%C3%BCbergehende_Information_?

Weitherhin ist es möglich, die Zeichen, die einem Platzhalter entsprechen,
in der neuen *Seiten-URL* einer Weiterleitungsregel (d.h. auf der
rechten Seite des Gleichheitszeichens) einzusetzen.
Dies erfolgt mit Variablen der Form `$1`, `$2` … `$9`,
wobei `$1` dem ersten Platzhalter entspricht,
`$2` dem zweiten Platzhalter, und so weiter.

Wenn Sie, beispielsweise, Ihre Website umbauen und die Seite *Eichen* von der obersten
Ebene auf die zweite Ebene unter *Bäume* verschieben möchten, wobei *Eichen* bereits
drei Unterseiten hat. Sie könnten also folgende Regeln ergänzen:

    Eichen:Weiß-Eiche=B%C3%A4ume:Eichen:Weiß-Eiche
    Eichen:Schwarz-Eiche=B%C3%A4ume:Eichen:Schwarz-Eiche
    Eichen:Blau-Eiche=B%C3%A4ume:Eichen:Blau-Eiche

Durch die Verwendung eines Platzhalters können Sie allerdings vereinfachen:

    Eichen:*=B%C3%A4ume:Eichen:$1

## Einschränkungen

Moved_XH verwendet den
[`custom_404()` Hook](https://wiki.cmsimple-xh.org/de/?tipps-und-tricks/eigene-404-seite),
so dass es nicht funktioniert, wenn der Hook bereits definiert
ist. In diesem Fall erhalten Sie ein leeres Browserfenster, wenn Sie Ihre
Website aufrufen. Entweder deinstallieren Sie Moved_XH, oder Sie entfernen
den bestehenden `custom_404()` Hook.

Die Regeln von Moved_XH funktionieren nicht, wenn Sie in die CMSimple_XH
Installation eingeloggt sind, da dann die `custom_404()`
Hook-Funktion von CMSimple_XH nicht aufgerufen wird. Wenn Sie die Regeln
testen wollen ohne sich zunächst auszuloggen, dann müssen Sie einen zweiten
Browser verwenden.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/moved_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Moved_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Moved_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Moved_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

© 2013-2023 Christoph M. Becker

## Danksagung

Das Plugin-Logo wurde von [World Media Group LLC](https://www.mymovingreviews.com/)
entworfen. Vielen Dank für die Veröffentlichung unter einer liberalen Lizenz.

Vielen Dank an die Community im [CMSimple_XH Forum](https://www.cmsimpleforum.com/)
für Tipps, Anregungen und das Testen.

Und zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
