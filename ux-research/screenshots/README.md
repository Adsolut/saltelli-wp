# UX Reference Screenshots — Saltelli Project

Catturati con Playwright su macOS, Chromium headless 131. Data: 2026-04-28.

## File presenti (20)

```
screenshots/
├── 00-saltelli-{desktop,mobile}.png         CLIENTE  (desktop ⚠ vedi nota)
├── 01-difiorenunziato-{desktop,mobile}.png  LOCAL — competitor diretto
├── 02-marcopiccolo-{desktop,mobile}.png     LOCAL — specialist tributario
├── 03-legalilavoro-{desktop,mobile}.png     LOCAL — specialist lavoro
├── 04-lombardini-{desktop,mobile}.png       LOCAL — specialist immigrazione
├── 05-bicklaw-{desktop,mobile}.png          INT — boutique editorial Webby
├── 06-stowe-{desktop,mobile}.png            INT — UK family law boutique
├── 07-seddons-{desktop,mobile}.png          INT — UK editorial bordeaux  (desktop ⚠)
├── 09-pedersoligattai-{desktop,mobile}.png  INT — boutique italiano premium
└── 10-deepjudge-{desktop,mobile}.png        INT — legaltech Awwwards HM
```

## Reference scartate

- **Sfera Legal (08-)** — boutique LATAM Awwwards Honorable Mention. Richiede interazione esplicita su modale "What kind of law firm are you looking for?" che blocca tutto il rendering finché non viene cliccato. Tentativi headless falliti su 3 strategie. Scartato perché abbiamo già 5 internazionali solide.

## ⚠ Note sui due "sospetti"

**`00-saltelli-desktop.png` e `07-seddons-desktop.png`** sono entrambi 60 KB, anomalo per pagine con immagini.

Tre tentativi di ricattura con strategie diverse (cookie dismissal aggressivo, JS injection per rimozione overlay, `networkidle` + scroll forzato lazy-load, user-agent diversi) hanno tutti prodotto lo stesso risultato.

**Diagnosi probabile:** anti-bot detection lato server (SiteGround per Saltelli ha SG Security + SG Cachepress; Seddons probabilmente Cloudflare) che serve una pagina vuota/di challenge a Playwright headless.

**Mitigazione:**

1. Le **versioni mobile** di entrambi i siti hanno funzionato (788 KB e 1401 KB) — sono utilizzabili come reference visiva primaria
2. **Per Saltelli desktop**: conoscenza già completa via Docker locale + audit GEO + search results — non c'è bisogno di screenshot esterno
3. **Per Seddons desktop**: se serve, Aldo può fare CMD+Shift+4 manuale aprendo `seddons.co.uk` da Chrome/Safari personali (5 secondi) e salvare come `07-seddons-desktop-manual.png`

## File leggeri ma corretti

- `09-pedersoligattai-desktop.png` (51 KB) e `10-deepjudge-desktop.png` (84 KB) sono **genuinamente leggeri** perché i siti hanno design molto pulito + palette monocromatica + poche immagini. Verificati a vista, sono catture corrette.

## Riferimento celle del report

Gli screenshot sono usati come supporto visivo per `SALTELLI_UX_REFERENCE_REPORT.md`. Quando si compila il prompt Claude Design v2, gli screenshot vanno allegati come reference visiva nei tab del prompt.

## Script di cattura

- `../capture_screenshots.py` — script principale (16 catture iniziali)
- `../recapture_suspicious.py` — script di recovery (non risolve l'anti-bot ma ha tentato)

Per rilanciarli: `python3 capture_screenshots.py` dalla cartella `ux-research/`.

---
*Generato: 2026-04-28*

