# Bug #03 — Discrepanza tra page title "Lo studio" e slug `chi-siamo`

**Severity:** P3 (low · cosmetic / UX)
**Found:** 2026-05-05 by Debug QA Agent
**URL:** /lo-studio/ → 301 → /chi-siamo/

## Descrizione

Page id 19 ha:
- post_title = "Lo studio"
- post_name (slug) = "chi-siamo"

URL `/lo-studio/` ritorna 301 redirect a `/chi-siamo/` (working).
URL `/chi-siamo/` ritorna 200 OK.

Brand-wise (vedi CLAUDE.md, IA, footer menu) la convenzione è "Lo studio".
URL internal/external linking è inconsistent: alcuni link puntano a
`/lo-studio/` (e prendono il redirect), altri a `/chi-siamo/`.

## Atteso

Coerenza URL canonica. Decidere:
- **Opzione A**: rinomina slug a `lo-studio` (allinea a brand naming)
  - PRO: URL canonica coerente con brand
  - CONTRO: rompe ogni link interno hardcoded a `/chi-siamo/` che non passa via redirect (cerca in template-parts, footer, header)
- **Opzione B**: rinomina post_title a "Chi siamo" (allinea a slug)
  - PRO: nessun link rotto
  - CONTRO: cambia brand wording (concorda con Elena prima)

## Reproduce

```bash
curl -I https://staging.studiolegalesaltelli.it/lo-studio/
# HTTP/2 301
# location: https://staging.studiolegalesaltelli.it/chi-siamo/

ssh deploy@178.62.207.50 "sudo -u www-data wp --path=/var/www/saltelli post get 19 --field=post_name --field=post_title"
# chi-siamo, "Lo studio"
```

## Status

- [x] Reproduced
- [x] Root cause identified
- [ ] **DEFERRED — out of scope Debug & QA**
- Decisione orchestratore: discutere con Elena pre-Wave 4

## Note

- Il redirect 301 funziona, quindi UX visitor è OK.
- Bug informativo per allineamento futuro IA.
- Fix richiede coordinamento editorial (Elena) per scegliere brand naming definitivo.
