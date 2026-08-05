# Politica di sicurezza

## Stato del progetto

Questo è un fork mantenuto da [Considera](https://www.considera.it/) del plugin
**Albo Pretorio On line** di Ignazio Scimone, fermo alla versione 4.8.

Il plugin originale è stato **rimosso da wordpress.org il 7 marzo 2024** perché
non superava i controlli di sicurezza richiesti dalla directory ufficiale.
L'autore non lo mantiene più.

Il fork è stato sottoposto a revisioni progressive:

- **4.9.1 – 4.9.4**: controllo degli accessi sulle chiamate AJAX, SQL injection
  nella ricerca atti, accesso agli allegati (`dwnalle`), SQL injection via
  `ORDER BY`, XSS memorizzato, PHP object injection;
- **4.10.1**: **audit di sicurezza completo**, con proof-of-concept eseguiti su
  installazione reale. Sono state corrette, fra le altre: modifica della
  configurazione senza autenticazione, SQL injection non autenticata sulla
  REST API, path traversal nel download dei backup, CSRF sulle cancellazioni
  di atti e allegati, XSS riflessi. Il dettaglio è nel
  [changelog](CHANGELOG.md).

L'audit non garantisce l'assenza di vulnerabilità residue: le segnalazioni
restano benvenute tramite i canali indicati sotto.

Il fork esiste perché diverse amministrazioni continuano a usare il plugin
comunque: meglio una copia manutenuta e con un canale di segnalazione che una
abbandonata.

## Come segnalare una vulnerabilità

**Non aprire una issue pubblica.** Un albo pretorio pubblica atti con valore
legale e le installazioni sono facilmente individuabili: una segnalazione
aperta è un invito a sfruttarla prima che sia corretta.

Usa uno di questi canali:

1. **Segnalazione privata su GitHub** — dalla scheda *Security* del repository,
   voce *Report a vulnerability*. È il canale preferito: la discussione resta
   riservata fino alla pubblicazione della correzione.
2. **Email** a `info@considera.it`, con `[SICUREZZA]` nell'oggetto.

Sono utili, se li hai: versione del plugin, versione di WordPress e PHP, i passi
per riprodurre il problema e l'impatto che gli attribuisci. Un proof of concept
è benvenuto ma non indispensabile — meglio una segnalazione parziale che nessuna
segnalazione.

## Cosa aspettarti

Considera mantiene questo fork per i propri clienti, non è un progetto con
personale dedicato alla sicurezza. Nei limiti del possibile:

- riscontro entro **5 giorni lavorativi**;
- valutazione e, se confermata, una correzione in tempi proporzionati alla
  gravità;
- credito nella release a chi ha segnalato, salvo diversa preferenza.

Non c'è un programma di ricompense.

## Versioni supportate

| Versione | Supporto |
|---|---|
| 4.10.x (questo fork) | sì |
| 4.9.x (questo fork) | solo aggiornamento alla 4.10.x consigliato |
| 4.8 e precedenti (upstream) | no, non più mantenute dall'autore |
