# Albo Pretorio On line — fork Considera

Fork mantenuto da [Considera](https://www.considera.it/) del plugin WordPress
**Albo Pretorio On line** di Ignazio Scimone.

Il plugin permette la pubblicazione degli atti nell'albo pretorio online di un
ente, in adempimento dell'art. 32 della Legge 18 giugno 2009, n. 69, che dal
1° gennaio 2011 richiede la pubblicazione degli atti sul sito istituzionale
dell'ente perché abbiano efficacia legale.

## Perché un fork

Il plugin originale è fermo alla versione 4.8 ed è stato **rimosso da
wordpress.org il 7 marzo 2024** per problemi di sicurezza. L'autore non lo
aggiorna più. Questo fork parte dall'ultima release pubblicata e prosegue la
manutenzione.

## Installazione

1. Scaricare l'ultimo `.zip` dalle
   [release](https://github.com/Considerasrl/albo-pretorio-considera/releases).
2. In WordPress: *Plugin → Aggiungi nuovo → Carica plugin*, oppure scompattare
   nella cartella dei plugin.
3. Attivare il plugin e inserire gli atti dal backend.

La cartella del plugin **deve chiamarsi `albo-pretorio-on-line`**. Lo slug non è
stato cambiato di proposito: l'aggiornamento avviene così in place, senza
disattivare il plugin e senza far ripartire la migrazione dei percorsi degli
allegati che scatta all'attivazione. Non arriveranno aggiornamenti automatici
da wordpress.org: il plugin non è più presente nella directory.

## Shortcode

### `[Albo]` — tabella degli atti nel front-end

```
[Albo stato="1" per_page="10" cat="1" filtri="si" minfiltri="no"]
```

| Parametro | Descrizione |
|---|---|
| `stato` | `1` solo atti in corso di validità · `2` solo atti scaduti (storico) |
| `per_page` | numero massimo di atti per pagina |
| `cat` | *(opzionale)* ID della categoria di cui mostrare gli atti |
| `filtri` | *(opzionale)* mostra la finestra dei filtri; se omesso, i filtri sono mostrati. Utile disattivarli nelle pagine di Amministrazione Trasparente |
| `minfiltri` | *(opzionale)* minimizza la finestra dei filtri; se omesso, è minimizzata |

### `[AlboGruppiAtti]` — atti raggruppati per metadato

```
[AlboGruppiAtti titolo="" meta="" valore=""]
```

| Parametro | Descrizione |
|---|---|
| `titolo` | titolo mostrato sopra la tabella |
| `meta` | metadato di raggruppamento |
| `valore` | valore del metadato per cui raggruppare gli atti |

Raggruppa un insieme eterogeneo di atti accomunati da un metadato con uno
specifico valore — ad esempio tutti gli atti di una stessa gara d'acquisto.

### `[AlboAtto]` — dati di un singolo atto

```
[AlboAtto titolo="" numero="" anno=""]
```

| Parametro | Descrizione |
|---|---|
| `titolo` | intestazione mostrata sopra i dati dell'atto |
| `numero` | numero dell'atto da visualizzare |
| `anno` | anno di riferimento del numero dell'atto |

## Changelog

Le modifiche sono documentate in [CHANGELOG.md](CHANGELOG.md) e pubblicate come
[GitHub Releases](https://github.com/Considerasrl/albo-pretorio-considera/releases),
con il pacchetto installabile allegato a ciascuna.

## Sicurezza

Questo fork è stato sottoposto a una **revisione di sicurezza sistematica**,
conclusa con la release **4.10.1**: audit del codice con proof-of-concept
eseguiti su installazione reale, che ha individuato e corretto vulnerabilità
anche critiche (modifica della configurazione e SQL injection sfruttabili da
anonimi, path traversal sui backup, CSRF sulle cancellazioni, XSS riflessi).
Il dettaglio è nel [CHANGELOG.md](CHANGELOG.md); le release precedenti (4.9.x)
avevano corretto controllo accessi AJAX, SQL injection nella ricerca e accesso
agli allegati.

L'audit non garantisce l'assenza di altri problemi: per segnalare una
vulnerabilità vedi [SECURITY.md](SECURITY.md) (in privato, non con una issue
pubblica).

## Rapporto con l'upstream

La storia git parte dalla versione 4.8 originale, importata intatta. Per vedere
l'intero scostamento dal punto di partenza:

```sh
git diff $(git rev-list --max-parents=0 HEAD) HEAD
```

Lo zip di partenza proviene da
`https://downloads.wordpress.org/plugin/albo-pretorio-on-line.4.8.zip`
(SHA-256 `c3e2b1e10ff7e21345203de104d495a344ff4cda4f4d210f37503ecd7445b4f2`).
Il repository originale di Ignazio Scimone, non più aggiornato, resta
consultabile su <https://github.com/ignazios/albo-pretorio-on-line>.

## Licenza

GPL-2.0+, come l'originale. Opera derivata da "Albo Pretorio On line",
Copyright (C) Ignazio Scimone (eduva.org). Le modifiche successive alla
versione 4.8 sono di Considera e mantengono la stessa licenza.

Si ringrazia Alessandro Cingolani per la consulenza in ambito sicurezza
informatica sul progetto originale.
