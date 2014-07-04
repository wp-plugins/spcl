=== Save Post. Check Links. ===
Contributors: sergej.mueller
Tags: check, links, broken, seo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5RDDW9FEHGLG6
Requires at least: 3.7
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Prüfung der Links auf ihre Richtigkeit bzw. Erreichbarkeit. Automatisiert und autonom beim Speichern der Beiträge.



== Description ==

*Save Post. Check Links.* übernimmt die Prüfung interner und externer Verlinkungen innerhalb der WordPress-Artikel. Das Plugin erkennt somit Tipp- sowie Copy&Paste-Fehler in gesetzten Links. Der Vorteil: Defekte Webseiten-Verknüpfungen werden noch vor der Veröffentlichung der Beiträge erkannt und vom Autor korrigiert.

Beim Speichern bzw. Publizieren der Artikel sucht sich die WordPress-Erweiterung alle URLs aus dem Inhalt heraus und pingt sie zwecks Richtigkeit/Erreichbarkeit an. Fehlerhafte Links samt Ursache (Fehlercode) listet das Plugin zur Kontrolle bzw. zum Nachbessern auf.


= Stärken =
* Links-Check im Hintergrund
* Anzeige der Fehlerursache
* Keine Konfiguration notwendig
* Intakte Links = SEO-Optimierung
* Performante Lösung, übersichtlicher Code

= Systemvoraussetzungen =
* PHP ab 5
* WordPress ab 3.7


= Speicherbelegung =
* Im Backend: ~ 0,04 MB
* Im Frontend: ~ 0,03 MB


= Unterstützung =
* Per [Flattr](https://flattr.com/submit/auto?user_id=sergej.mueller&url=https%3A%2F%2Fwordpress.org%2Fplugins%2Fspcl%2F)
* Per [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5RDDW9FEHGLG6)


= Dokumentation =
* [Save Post. Check Links.](https://plus.google.com/110569673423509816572/posts/hDtKSyEozeR)


= Autor =
* [Twitter](https://twitter.com/wpSEO)
* [Google+](https://plus.google.com/110569673423509816572)
* [Plugins](http://wpcoder.de)



== Changelog ==

= 0.6.2 =
* Zusatzprüfung für extrahierte Links

= 0.6.1 =
* Werte zu Plugin-Speichernutzung hinzugefügt
* `get_current_user_id` statt `wp_get_current_user()->ID`

= 0.6.0 =
* Support zu WordPress 3.9
* Überarbeitung des Sourcecodes

= 0.5.1 =
* Tausch `esc_url` gegen `esc_url_raw`

= 0.5 =
* Xmas Edition

= 0.4.1 =
* Hotfix für URLs mit Hash-Fragmenten

= 0.4 =
* Live auf wordpress.org

= 0.3 =
* Ausgabe des Fehlers bzw. Status Codes
* Quelltext-Überarbeitung

= 0.2 =
* Umstellung der Action auf `admin_notices`
* Zusätzliche Prüfung des Status Codes 405

= 0.1 =
* Plugin-Veröffentlichung



== Screenshots ==

1. Ausgabe fehlerhafter Links