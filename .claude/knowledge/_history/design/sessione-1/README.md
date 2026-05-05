# Claude Design — Sessione 1 — Design System + Frame 1 (Homepage)

> Cartella di handoff dell'output della sessione 1 di Claude Design.
> Obiettivo: trasformare gli artifact visivi della sessione in input azionabili per gli agent multi-tmux che fanno il build (Style & Animation Agent, Theme Architect, GEO Engineer).

## Convenzione di naming

Salvare i file con prefisso numerico per ordine di lettura:

```
01-design-system-overview.png         # screenshot pannello tokens completo
02-frame-1-homepage-desktop.png       # 1440px
03-frame-1-homepage-mobile.png        # 375px
04-tokens.json                        # se Claude Design lo esporta in formato JSON/W3C
05-tokens-extracted.md                # estrazione manuale dei valori in markdown leggibile
06-figma-handoff.fig                  # se disponibile
07-additional-frames-or-components.png  # eventuali altri screenshot utili
```

## Cosa serve davvero agli agent del build

Anche se Claude Design produce 50 artifact, per il **Style & Animation Agent** servono in concreto solo:

1. **Valori esatti dei design tokens** (in `05-tokens-extracted.md` se manca il JSON):
   - Colori hex puntuali (background, surface, primary, accent, text, text-muted, border)
   - Font family + weight (display + body + mono)
   - Type scale clamp values per ogni livello (display, h1, h2, h3, body, small)
   - Spacing scale numerica
   - Grid + breakpoints
   - Eventuali variazioni dai valori già locked nel `CLAUDE_DESIGN_PROMPT.md` (se Claude Design ha proposto miglioramenti, segnalare)

2. **Screenshot Frame 1 a risoluzione piena** (`02-...desktop.png`, `03-...mobile.png`) — serviranno come reference visiva confronto pixel-by-pixel

3. **Decisioni "non token" prese da Claude Design** che devono finire nel codice:
   - Anatomia esatta hero (proporzioni headline / sotto-headline / spazio negativo)
   - Comportamento esatto della lista 19 aree pratica (timing animazioni, ampiezza preview hover)
   - Layout asimmetrico dei 4 avvocati (offset specifici)
   - Stile bottoni / link (text + line-bottom / o varianti)
   - Stile CTA "Prenota un primo incontro"
   - Trattamento footer (3 colonne, gerarchia tipografica del footer)

## Per Theme Architect

Le decisioni di Frame 1 non riguardano direttamente lui (lui completa template hierarchy + ACF + menu) ma deve sapere che **la HOME ha 7 sezioni in ordine fissato** (vedi `CLAUDE_DESIGN_PROMPT.md` sezione 5 → Frame 1):
1. Hero 100vh
2. Aree di pratica (lista tipografica)
3. Lo studio (prose editoriale)
4. Avvocati (asimmetrico)
5. Casi e risultati
6. Earned media (nascondere se vuoto)
7. Contatti / CTA

## Per GEO Engineer

Lo schema da iniettare in homepage è già definito in `geo-assets/schema/01-organization-legalservice.json`. Da Frame 1 deve solo verificare che i meta description / OG image / OG title abbiano i valori corretti coerenti con il copy del design system.

## TODO operativi (ordine di lavoro)

- [ ] Duccio esporta artifact da `claude.ai/design` e li salva in questa cartella
- [ ] Claude (chat) compila `05-tokens-extracted.md` leggendo gli screenshot
- [ ] Si lanciano i 3 prompt agent in tmux che leggono questa cartella

---
*Ultimo aggiornamento: 2026-04-28 — apertura cartella per sessione 1.*
