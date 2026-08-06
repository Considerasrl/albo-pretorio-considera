# Changelog

Tutte le modifiche rilevanti a questo progetto sono documentate qui.

Il formato segue [Keep a Changelog](https://keepachangelog.com/it/1.1.0/)
e il progetto aderisce al [Versionamento Semantico](https://semver.org/lang/it/).

Le release sono pubblicate anche su
[GitHub Releases](https://github.com/Considerasrl/albo-pretorio-considera/releases),
con il pacchetto `.zip` installabile allegato.

## [4.12.0] - 2026-08-06

Il plugin supera ora il **Plugin Check ufficiale di WordPress.org con 0 rilievi**
(ERROR e WARNING) sul pacchetto distribuito.

### Sicurezza
- **Escaping completo dell'output**: tutti i valori dinamici in uscita sono ora
  escapati per contesto (`esc_html`, `esc_attr`, `esc_url`, `esc_js`,
  `wp_kses_post`). Difesa in profondità contro XSS nel back-end e nel front-end.
- **Sanitizzazione, unslash e validazione degli input**: ogni lettura di
  superglobali (`$_GET`/`$_POST`/`$_REQUEST`/`$_SERVER`/`$_FILES`) passa da
  `isset()`/`??`, `wp_unslash()` e un sanitizzatore adeguato prima dell'uso; gli
  upload verificano `is_uploaded_file()`.
- **Verifica dei nonce** sulle operazioni di scrittura (già presente negli
  handler; le pagine di sola visualizzazione leggono i parametri solo per il
  rendering).
- Query su tabelle custom con clausole preparate e caching dove applicabile.
- Creazione delle cartelle di lavoro (allegati, backup, oblio) tramite
  `wp_mkdir_p()` invece di `mkdir()` diretto.

### Modificato
- **Prefisso globale**: tutte le funzioni e le variabili globali del plugin sono
  state prefissate con `albopc_` (il prefisso `ap_`, di 2 caratteri, non è
  accettato da Plugin Check che ne richiede almeno 4). Shortcode, azioni/hook
  AJAX (`ap_editor_*`) e nomi delle opzioni (`opt_AP_*`) restano invariati:
  nessun impatto sul comportamento per l'utente.
- Rimosso il bundle ridondante `js/jquery-ui.min.js` (si usa il jQuery UI del
  core di WordPress). Descrizione della `readme.txt` in inglese per wp.org.
- Backup dati/allegati e oblio: sostituita la libreria di terze parti PclZip con
  l'estensione nativa `ZipArchive` (rimosso `inc/pclzip.php`). Il formato degli
  archivi prodotti è invariato.
- L'esportazione del Repertorio (CSV/XML/JSON) viene ora scritta nella cartella
  uploads del sito invece che dentro la cartella del plugin.
- Gli script e i fogli di stile del plugin vengono ora accodati con un numero di
  versione esplicito (derivato dalla versione del plugin), utile per il
  cache-busting sugli aggiornamenti; gli handle di jQuery UI del core sono
  accodati per solo handle.
- Adeguamento agli standard di WordPress.org (Plugin Check): commenti
  `translators` e segnaposto numerati nelle stringhe traducibili; correzione di
  un dominio di testo errato in un messaggio; uso di `gmdate()` al posto di
  `date()` (comportamento invariato: WordPress opera in UTC). Nessun cambiamento
  funzionale per l'utente.

- I dialog dei pulsanti dell'editor (Albo, Gruppi Atti, Vis. Atto) sono ora
  serviti tramite `admin-ajax.php` con verifica delle capability, invece che da
  file PHP autonomi che si auto-caricavano l'ambiente WordPress: nessun accesso
  diretto ai file e nessun bootstrap manuale di `wp-load.php`.

### Rimosso
- File `Repertori/repertorio_2020.csv` erroneamente incluso nel pacchetto.
- File `js/gencode.php`, `js/buttonEditorGruppiAlbo.php`, `js/buttonEditorVisAtto.php`
  (sostituiti dagli handler AJAX dei dialog editor).

## [4.11.1] - 2026-08-05

### Sicurezza
- Hardening guidato da Plugin Check, limitato ai rilievi meccanici a basso
  rischio: output delle stringhe tradotte ora escapato (`_e` → `esc_html_e`),
  redirect interni resi sicuri (`wp_redirect` → `wp_safe_redirect`),
  `strip_tags` → `wp_strip_all_tags`, e protezione contro l'accesso diretto ai
  file (guard `ABSPATH`) nei file che non fanno da entry-point. I rilievi
  restanti (escaping dell'output HTML, prefisso dei simboli globali,
  sanitizzazione degli input) richiedono interventi non automatici.

## [4.11.0] - 2026-08-05

### Aggiunto
- Icone di ripiego per i tipi di file noti (documenti, fogli di calcolo,
  presentazioni, immagini, archivi, testo, audio, video) mostrate accanto agli
  allegati quando il tipo non è esplicitamente configurato tra i Tipi di Files.
  Le icone (set Tango, pubblico dominio) sono incluse nel plugin in
  `img/tipifiles/` e risolte a runtime da `Albo_URL`, quindi restano valide
  anche se cambia il nome della cartella del plugin.

### Modificato
- **Cambio di identità in preparazione alla distribuzione su wordpress.org**: il
  plugin si chiama ora "Albo Pretorio Considera", il text domain è
  `albo-pretorio-considera` e lo slug/cartella diventa `albo-pretorio-considera`
  (lo slug originale `albo-pretorio-on-line`, del plugin chiuso, non è
  riassegnabile). Aggiunto l'header `Domain Path`; i file di traduzione sono
  stati rinominati di conseguenza.
- All'attivazione le URL delle icone dei Tipi di Files vengono riallineate alla
  cartella corrente del plugin, così un aggiornamento che cambia cartella non
  lascia icone rotte.

## [4.10.1] - 2026-08-05

### Sicurezza
- **Critico**: il salvataggio della configurazione richiede ora un nonce valido
  e la capability `admin_albo`; in precedenza una POST anonima poteva modificare
  tutte le opzioni del plugin (mancava `exit` dopo il redirect di errore).
- **Critico**: SQL injection nel parametro `dadata`/`adata` della REST API
  pubblica (`/wp-json/alboonline/v1/atti`): le date sono ora validate nel
  formato prima di entrare nella query.
- **Alto**: path traversal nel download dei backup (`ExportBackupData`): il nome
  file passa ora per `basename()`.
- **Alto**: SQL injection nel parametro `Anno` del repertorio (`ap_Repertorio`):
  cast a intero.
- **Alto**: le azioni `delete-allegato-atto` e `delete_bulk_atti` richiedono
  ora un nonce valido (prima la cancellazione era possibile via CSRF).
- **Medio**: XSS riflesso nel parametro `titolo` dello shortcode `[AlboAtto]`
  (sanitizzazione + escaping) e nel pulsante "Torna alla Lista" via header
  Referer (uso di `esc_url(wp_get_referer())`).
- **Medio**: le esportazioni del repertorio (CSV/XML/JSON) richiedono login e
  nonce; tutte le azioni di `albo_post()` richiedono ora un utente autenticato.
- **Medio**: nonce sulle azioni `pubblica-atto`, `approva-atto`, `setta-anno`
  e sulla ripubblicazione massiva (`rip`).
- **Medio**: l'associazione di allegati "spuri" verifica che il file sia dentro
  la cartella di upload dell'Albo.
- **Medio**: SQL injection secondarie in `ap_remove_metasatto()` e
  `ap_get_alcuni_soggetti_ruolo()` (prepare/cast interi).
- **Basso**: le directory `AlboOnLine/BackupDatiAlbo` e `OblioDatiAlbo`
  vengono protette con `.htaccess` e `index.php`.

### Corretto
- L'azione pubblica `printatto` andava in fatal error perché `stampe.php` non
  era caricata sul frontend; ora il file viene incluso e la stampa è consentita
  solo per atti pubblicati e non in oblio.

## [4.10.0] - 2026-08-04

### Aggiunto
- Filtri di ricerca nella pagina **Atti** del backend. Oltre alla ricerca per
  stato e alla "Cerca in Oggetto" già presenti, è ora possibile filtrare per:
  - **Riferimento** (corrispondenza parziale);
  - **Numero** (anche parziale: la ricerca avviene sul valore del numero senza
    gli zeri di riempimento, quindi "12" trova 12, 120, 512, …);
  - **Categoria** (menu a tendina con il numero di atti per categoria).
  I filtri si combinano tra loro e con la ricerca per oggetto, restano
  impostati nel modulo e vengono mantenuti su ordinamento e paginazione.

## [4.9.4] - 2026-08-04

### Corretto (sicurezza)
- **SQL injection via `ORDER BY`** in `ap_get_all_atti` e `ap_searchAtti`: la
  clausola di ordinamento veniva interpolata grezza nella query. Ora le colonne
  sono validate contro una allowlist e la direzione è limitata a `ASC`/`DESC`.
- Parametri numerici (`Anno`, `Numero`, `Categoria`, `Ente`) e `LIMIT` ora
  passano da `$wpdb->prepare` con cast a intero; i filtri testuali (`Oggetto`,
  `Riferimento`, ricerca) usano `prepare` + `esc_like` al posto della
  concatenazione diretta.
- **XSS memorizzato** nelle viste dell'atto (front-end e backend): i campi
  `Oggetto`, `Riferimento`, `Richiedente` sono ora emessi con `esc_html()` e le
  Note con `wp_kses_post()`, invece di essere stampati grezzi.
- **XSS via `Referer`**: il bottone "Torna alla lista" costruiva l'URL da
  `$_SERVER['HTTP_REFERER']` senza escape; ora usa `esc_url(wp_get_referer())`.
- **PHP object injection**: tutte le chiamate `unserialize()` sui dati degli atti
  (incluso il parametro `AttiDaAgg` da GET in `utility.php`) impostano ora
  `allowed_classes => false`.

### Corretto
- Ricerca "Cerca in Oggetto" del backend: con l'irrobustimento della query lo
  stato di ricerca era diventato un `WHERE 1` privo di filtro e la lista mostrava
  tutti gli atti. Il termine viene ora instradato attraverso il filtro `Oggetto`
  già preparato, ripristinando il comportamento corretto.

## [4.9.3] - 2026-07-31

### Rimosso
- Pagina backend **Albo → Log Aggiornamenti** (`inc/logaggiornamenti.php`):
  changelog HTML scritto a mano, fermo alla 4.6.6 e ridondante. La cronologia
  delle modifiche è ora gestita in questo file e nelle release GitHub.
- File `readme.txt`: il plugin non è più distribuito da wordpress.org, quindi
  i suoi metadati non hanno più funzione. La documentazione degli shortcode è
  stata spostata in `README.md`.

### Modificato
- Il messaggio mostrato dopo un aggiornamento rimanda ora alle release su GitHub
  invece che alla pagina interna rimossa.

## [4.9.2] - 2026-07-31

### Corretto (sicurezza)
- **SQL injection** nella ricerca degli atti (`ap_get_all_atti`, ricerca per
  oggetto): il parametro di ricerca finiva nella query senza escape. Il percorso
  è raggiungibile solo dalla pagina Atti del backend (capability
  `gest_atti_albo`), quindi non sfruttabile da anonimi, ma consentiva a un
  redattore una lettura non autorizzata del database. Chiuso con `esc_sql()`.
- Stessa vulnerabilità e una precedenza `AND/OR` errata in `ap_searchAtti`.
- Il download degli allegati (`action=dwnalle`) è ora limitato agli atti
  effettivamente pubblici: non è più possibile, scorrendo gli identificativi,
  scaricare gli allegati di atti non ancora pubblicati o oltre la data di oblio.
  Gli atti annullati restano scaricabili, coerentemente con la loro visibilità
  nel front-end.

### Noto
- Gli allegati restano raggiungibili anche per URL diretto, protetti dal solo
  `.htaccess` con controllo del `Referer`. Blindare quell'accesso richiede di
  servire gli allegati esclusivamente via PHP: intervento più invasivo, rimandato.

## [4.9.1] - 2026-07-31

### Corretto (sicurezza)
- Falla di **controllo degli accessi**: le quattro chiamate AJAX del plugin
  verificavano il nonce ma non i permessi dell'utente. Qualsiasi utente
  registrato, anche Sottoscrittore, poteva leggere il nonce dal sorgente di una
  pagina di wp-admin e invocarle. La più grave, `rimuoviAllegato`, cancellava
  dal disco l'allegato di un qualsiasi atto.
- Il nonce non viene più emesso agli utenti privi di capability rilevanti.
- Dichiarato esplicitamente `permission_callback` sulle rotte REST, che ne
  erano prive.

## [4.9.0] - 2026-07-31

Prima release del fork mantenuto da Considera. Base: versione **4.8** di Ignazio
Scimone, ultima pubblicata su wordpress.org prima della rimozione (7 marzo 2024).

### Corretto
- Download degli allegati: gli archivi ZIP e RAR venivano scaricati corrotti
  perché l'output emesso da altri componenti finiva in testa al file e ne faceva
  troncare la coda.
- Controllo sul limite di upload: un valore di `upload_max_filesize` espresso in
  gigabyte veniva interpretato come megabyte.

---

## Storia precedente (upstream)

Le versioni fino alla **4.8** sono opera di Ignazio Scimone. La loro cronologia
non è ripercorsa qui in dettaglio; resta consultabile nel repository originale:
<https://github.com/ignazios/albo-pretorio-on-line>

[Non rilasciato]: https://github.com/Considerasrl/albo-pretorio-considera/compare/v4.11.1...HEAD
[4.11.1]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.11.1
[4.11.0]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.11.0
[4.10.1]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.10.1
[4.10.0]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.10.0
[4.9.4]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.4
[4.9.3]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.3
[4.9.2]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.2
[4.9.1]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.1
[4.9.0]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.0
