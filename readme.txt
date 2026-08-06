=== Albo Pretorio Considera ===
Contributors: infoconsidera
Tags: albo pretorio, pubblica amministrazione, atti, trasparenza, comuni
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 4.12.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Publish official notices on an Italian public body's online notice board ("albo pretorio", art. 32 Law 69/2009). Maintained, security-hardened fork.

== Description ==

Albo Pretorio Considera lets an Italian public body publish official acts on its
online notice board ("albo pretorio"), as required by art. 32 of Law no. 69 of
18 June 2009, which since 1 January 2011 requires acts to be published on the
institutional website in order to have legal effect.

It is a fork, maintained by Considera, of the **Albo Pretorio On line** plugin by
Ignazio Scimone, which stopped at version 4.8 and was removed from wordpress.org
on 7 March 2024. The original author no longer updates it; this fork starts from
the last published version, continues its maintenance and has fixed its security
vulnerabilities.

= Main features =

* Manage acts (create, publish, expiry, right-to-be-forgotten) from the backend.
* Categories, bodies, organizational units and responsible people.
* Front-end publishing via shortcodes.
* Backend search filters for acts (subject, reference, full or partial number,
  category).
* Icons for the common file types attached to acts.
* Register and exports (CSV / XML / JSON).

= Shortcodes =

* `[Albo]` — table of acts on the front-end (parameters: stato, per_page, cat,
  filtri, minfiltri).
* `[AlboGruppiAtti]` — acts grouped by metadata (titolo, meta, valore).
* `[AlboAtto]` — details of a single act (titolo, numero, anno).

= Security =

The fork underwent a systematic security review (releases 4.9.x and 4.10.1), with
code audit and proof-of-concept on a real installation. Vulnerabilities, some of
them critical, have been fixed. To report a security issue please use the private
channel described in SECURITY.md: do not open a public report.

= Project =

Open source fork (GPL) maintained by Considera, with a public repository open to
contributions. Code, full changelog and reporting channel:
https://github.com/Considerasrl/albo-pretorio-considera

== Installation ==

1. Caricare il plugin in `wp-content/plugins/`, oppure installarlo da
   *Plugin → Aggiungi nuovo*.
2. Attivare il plugin dalla schermata *Plugin*.
3. Configurare l'ente e i parametri dal menu *Albo OnLine* nel backend.
4. Inserire gli atti e pubblicarli nelle pagine tramite gli shortcode.

== Frequently Asked Questions ==

= È lo stesso plugin "Albo Pretorio On line" rimosso da wordpress.org? =

È un fork indipendente, mantenuto da Considera, che parte dalla versione 4.8
dell'originale e ne corregge le vulnerabilità di sicurezza che ne avevano
causato la rimozione. I crediti dell'autore originale, Ignazio Scimone, sono
mantenuti.

= I dati e gli allegati esistenti vengono conservati? =

Sì. Il plugin lavora sulle stesse tabelle e cartelle dell'originale.

== Changelog ==

Il changelog completo è mantenuto nel file CHANGELOG.md del repository.

= 4.11.1 =
Hardening di sicurezza guidato da Plugin Check: escaping dell'output delle
stringhe tradotte, redirect interni resi sicuri, protezione contro l'accesso
diretto ai file.

= 4.11.0 =
Icone di ripiego per i tipi di file noti. Preparazione alla distribuzione su
wordpress.org: rinomina del plugin in "Albo Pretorio Considera" e del text
domain in `albo-pretorio-considera`. Migrazione automatica delle icone dei
tipi file al cambio di cartella.

= 4.10.1 =
Audit di sicurezza completo: modifica configurazione senza autenticazione,
SQL injection sulla REST API, path traversal nei backup, CSRF sulle
cancellazioni, XSS riflessi. Correzione del fatal error di `printatto` sul
front-end.

= 4.10.0 =
Filtri di ricerca degli atti nel backend: riferimento, numero (anche parziale),
categoria.

= 4.9.0 – 4.9.4 =
Prima release del fork (base 4.8). Download allegati corrotti, controllo accessi
AJAX, SQL injection nella ricerca e via ORDER BY, XSS memorizzato, object
injection, accesso agli allegati.

== Upgrade Notice ==

= 4.11.0 =
Include tutte le correzioni di sicurezza fino alla 4.10.1. Aggiornamento
consigliato a chiunque usi ancora il plugin originale non più mantenuto.
