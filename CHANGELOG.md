# Changelog

Tutte le modifiche rilevanti a questo progetto sono documentate qui.

Il formato segue [Keep a Changelog](https://keepachangelog.com/it/1.1.0/)
e il progetto aderisce al [Versionamento Semantico](https://semver.org/lang/it/).

Le release sono pubblicate anche su
[GitHub Releases](https://github.com/Considerasrl/albo-pretorio-considera/releases),
con il pacchetto `.zip` installabile allegato.

## [Non rilasciato]

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

[Non rilasciato]: https://github.com/Considerasrl/albo-pretorio-considera/compare/v4.9.3...HEAD
[4.9.3]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.3
[4.9.2]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.2
[4.9.1]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.1
[4.9.0]: https://github.com/Considerasrl/albo-pretorio-considera/releases/tag/v4.9.0
