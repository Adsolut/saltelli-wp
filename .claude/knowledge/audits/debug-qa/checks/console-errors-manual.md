# Console errors check — Manual delegation

Headless Chrome via shell è fragile per cattura console (richiede CDP).
Lychee non installato, Playwright/Puppeteer non in scope per questa sessione.

**Manual TODO** (delega Elena/orchestratore con DevTools):

URL chiave da aprire in Chrome DevTools (Cmd+Opt+J):
- https://staging.studiolegalesaltelli.it/
- https://staging.studiolegalesaltelli.it/lo-studio/
- https://staging.studiolegalesaltelli.it/avvocati/emiliano-saltelli/
- https://staging.studiolegalesaltelli.it/competenze/diritto-tributario/
- https://staging.studiolegalesaltelli.it/faq/
- https://staging.studiolegalesaltelli.it/contatti/

Cosa cercare:
- ⚠️ Errors rossi (uncaught exceptions, 404, CORS)
- ⚠️ Warnings gialli (deprecated APIs, mixed content)
- ⚠️ Network tab — file non caricati (404 image, font, CSS, JS)

Se trovi errors → file ticket bug-NN-console-* in
.claude/knowledge/audits/debug-qa/bugs/.

Validati via HTML inspection automated (Phase 2):
- 0 PHP error markers visibili
- 0 ACF unrendered tokens
- Mobile viewport meta presente
- 65/65 internal links HTTP 200
- Schema JSON-LD valido + parsable su 5/5 URL chiave
