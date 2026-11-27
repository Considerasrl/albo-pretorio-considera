=== Albo Pretorio On line ===
Contributors: considera, ignazios
Tags: albo pretorio, pubblica amministrazione, atti, trasparenza, comuni
Requires at least: 5.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 4.14.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Publish official notices on an Italian public body's online notice board ("albo pretorio", art. 32 Law 69/2009). Maintained, security-hardened fork.

== Description ==

Albo Pretorio On line lets an Italian public body publish official acts on its
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

= 4.14.3 =
* Requisiti allineati al codice: WordPress 5.3 o successivo (usa wp_date()), PHP 7.4 o successivo. Verificato fino a WordPress 7.1 e PHP 8.5.
* Corretto: le traduzioni e l'aggiornamento automatico delle impostazioni partono da init (avviso "Translation loading … triggered too early" da WordPress 6.7).
* Corretto: nelle nuove installazioni l'opzione dei soggetti predefiniti veniva salvata vuota.

= 4.14.2 =
* Avviso in bacheca: dalla 4.14.0 lo storico pubblico degli atti scaduti è disattivato di default (Linee guida Garante privacy 15/05/2014). L'avviso spiega come riattivarlo e a quali condizioni.

= 4.14.1 =
* Sicurezza: gli atti con pubblicazione futura non compaiono più negli elenchi pubblici e nella REST quando si usa il filtro "da data".
* Sicurezza: la protezione della cartella degli allegati viene verificata e, se necessario, riscritta in area amministrativa, con avviso se non è possibile.

= 4.14.0 =
Correzioni di sicurezza: bozze e atti non pubblici non più accessibili dal sito,
cartella allegati chiusa all'accesso diretto, atti pubblicati non modificabili,
registro non cancellabile, disinstallazione che conserva i dati. Storico
pubblico disattivato di default (Linee guida Garante 2014), durata minima di
pubblicazione verificata, gestione date corretta, compatibilità PHP 8.2-8.4.

= 4.13.0 =
Conformità wordpress.org e GDPR: rimosso il caricamento remoto del WebFont
Loader di Google. Rimossa una funzione legacy inutilizzata segnalata in review.
Query dei metadati resa parametrizzata. Compatibilità PHP 8 sulla creazione dei
file di protezione. Compatibilità dichiarata con WordPress 7.1.

= 4.11.1 =
Hardening di sicurezza guidato da Plugin Check: escaping dell'output delle
stringhe tradotte, redirect interni resi sicuri, protezione contro l'accesso
diretto ai file.

= 4.11.0 =
Icone di ripiego per i tipi di file noti. Migrazione automatica delle icone dei
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

= 4.14.3 =
Aggiornamento di sicurezza: corregge vulnerabilità che esponevano atti non
pubblici e allegati (GHSA-w3m3-64pc-486v, GHSA-75h3-49jg-mw5w). Aggiornare
subito. Su nginx/IIS negare l'accesso diretto alla cartella
uploads/AllegatiAttiAlboPretorio nella configurazione del server.
Dalla 4.14.0 lo storico pubblico degli atti scaduti è disattivato di
default (Garante privacy, Linee guida 15/05/2014): vedere Parametri → Shortcode.

= 4.11.0 =
Include tutte le correzioni di sicurezza fino alla 4.10.1. Aggiornamento
consigliato a chiunque usi ancora il plugin originale non più mantenuto.
