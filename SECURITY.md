# Politica di sicurezza

## Stato del progetto

Questo è un fork mantenuto da [Considera](https://www.considera.it/) del plugin
**Albo Pretorio On line** di Ignazio Scimone, fermo alla versione 4.8.

Il plugin originale è stato **rimosso da wordpress.org il 7 marzo 2024** perché
non superava i controlli di sicurezza richiesti dalla directory ufficiale.
L'autore non lo mantiene più.

In **4.9.1** è stata condotta una prima revisione, che ha individuato e corretto
una falla di controllo degli accessi (vedi il changelog). È una prima passata,
non un audit completo: **non sappiamo se coincida con ciò che wordpress.org
aveva contestato**, e altre vulnerabilità possono benissimo essere ancora
presenti. Chi installa il plugin lo fa su codice non ancora sottoposto a una
revisione di sicurezza sistematica.

Fra i punti già noti e non ancora affrontati: il download degli allegati
(`action=dwnalle`) serve qualsiasi allegato a chiunque, protetto dal solo
controllo dell'header `Referer`, che è banale da falsificare.

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
| 4.9.x (questo fork) | sì |
| 4.8 e precedenti (upstream) | no, non più mantenute dall'autore |
