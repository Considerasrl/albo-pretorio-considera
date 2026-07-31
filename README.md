# Albo Pretorio On line — fork Considera

Fork mantenuto da [Considera](https://www.considera.it/) del plugin WordPress
**Albo Pretorio On line** di Ignazio Scimone.

## Perché un fork

Il plugin originale è fermo alla versione 4.8 ed è stato **rimosso da
wordpress.org il 7 marzo 2024** per problemi di sicurezza. L'autore non lo
aggiorna più. Questo fork parte dall'ultima release pubblicata e prosegue la
manutenzione.

## Lineage

La storia git è costruita in modo da poter sempre confrontare il fork con il
punto di partenza:

| Commit | Contenuto |
|---|---|
| `Import upstream ... 4.8` | zip ufficiale 4.8, intatto |
| commit successivi | modifiche Considera |

Lo zip di partenza proviene da
`https://downloads.wordpress.org/plugin/albo-pretorio-on-line.4.8.zip`
SHA-256 `c3e2b1e10ff7e21345203de104d495a344ff4cda4f4d210f37503ecd7445b4f2`.

Per vedere l'intero scostamento dall'upstream:

```sh
git diff $(git rev-list --max-parents=0 HEAD) HEAD
```

## Nome del repository e slug del plugin

Il repository si chiama `albo-pretorio-considera`, ma **la cartella del plugin
installata su WordPress resta `albo-pretorio-on-line`**: sono due nomi
indipendenti, e cambiare il secondo ha un costo (vedi sotto).

## Installazione

La cartella del plugin **deve continuare a chiamarsi `albo-pretorio-on-line`**.
Lo slug non è stato cambiato di proposito: l'aggiornamento avviene così in
place, senza disattivare il plugin e senza far ripartire la migrazione dei
percorsi degli allegati che scatta all'attivazione (`AlboPretorio.php`).

Non arriveranno aggiornamenti automatici da wordpress.org: il plugin non è più
presente nella directory.

## Stato della sicurezza

La 4.8 è la versione **su cui** il plugin è stato ritirato, non quella che ne
ha risolto i problemi. Le vulnerabilità che hanno motivato la rimozione non
sono state analizzate né corrette in questo fork. Prima di considerarlo sicuro
per un uso in produzione serve una revisione dedicata.

## Licenza

GPL-2.0+, come l'originale. Opera derivata da "Albo Pretorio On line",
Copyright (C) Ignazio Scimone (eduva.org). Le modifiche successive alla
versione 4.8 sono di Considera e mantengono la stessa licenza.
