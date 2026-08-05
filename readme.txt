=== Albo Pretorio Considera ===
Contributors: infoconsidera
Tags: albo pretorio, pubblica amministrazione, atti, trasparenza, comuni
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 4.11.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Pubblicazione degli atti nell'albo pretorio online dell'ente (art. 32 L. 69/2009). Fork manutenuto e messo in sicurezza del plugin di Ignazio Scimone.

== Description ==

Albo Pretorio Considera permette la pubblicazione degli atti nell'albo pretorio
online di un ente, in adempimento dell'art. 32 della Legge 18 giugno 2009 n. 69,
che dal 1° gennaio 2011 richiede la pubblicazione degli atti sul sito
istituzionale perché abbiano efficacia legale.

È un fork mantenuto da Considera del plugin **Albo Pretorio On line** di Ignazio
Scimone, fermo alla versione 4.8 e rimosso da wordpress.org il 7 marzo 2024.
L'autore originale non lo aggiorna più; questo fork parte dall'ultima versione
pubblicata, ne prosegue la manutenzione e ne ha corretto le vulnerabilità di
sicurezza.

= Funzionalità principali =

* Gestione degli atti (inserimento, pubblicazione, scadenza, oblio) dal backend.
* Categorie, enti, unità organizzative e soggetti responsabili.
* Pubblicazione front-end tramite shortcode.
* Filtri di ricerca degli atti nel backend (oggetto, riferimento, numero anche
  parziale, categoria).
* Icone per i tipi di file comuni allegati agli atti.
* Repertorio ed esportazioni (CSV / XML / JSON).

= Shortcode =

* `[Albo]` — tabella degli atti nel front-end (parametri: stato, per_page, cat,
  filtri, minfiltri).
* `[AlboGruppiAtti]` — atti raggruppati per metadato (titolo, meta, valore).
* `[AlboAtto]` — dati di un singolo atto (titolo, numero, anno).

= Sicurezza =

Il fork è stato sottoposto a una revisione di sicurezza sistematica (release
4.9.x e 4.10.1), con audit del codice e proof-of-concept su installazione reale.
Sono state corrette vulnerabilità anche critiche. Per segnalare un problema di
sicurezza usare il canale privato descritto in SECURITY.md: non aprire una
segnalazione pubblica.

= Progetto =

Fork open source (GPL) mantenuto da Considera, con repository pubblico aperto ai
contributi. Codice, changelog completo e canale di segnalazione:
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
