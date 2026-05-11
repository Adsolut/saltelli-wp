---
title: Wave 5 Design Realign — Visual Regression
date: 2026-05-11
target: https://staging.studiolegalesaltelli.it
deployed: assets/css/{tokens,components,sections}.css (3 file) via rsync→/home/deploy/wave5-css-tmp + sudo install -o 33 -g 33 -m 644
---

# Wave 5 Design Realign — Visual Regression

## Deploy

Il `deploy` user non ha write su `/var/www/saltelli/wp-content/themes/saltelli/assets/css/` (dir e file owned by uid/gid 33 = www-data, mode 755/644). `rsync` diretto fallisce con `Permission denied`. Workaround usato (2-step, sudo-NOPASSWD disponibile):

```sh
# 1) rsync i 3 file modificati nella home di deploy (writable)
rsync -avz tokens.css components.css sections.css deploy@178.62.207.50:/home/deploy/wave5-css-tmp/
# 2) sudo install nel theme dir con ownership www-data + mode 644, poi cleanup
ssh deploy@178.62.207.50 'for f in tokens.css components.css sections.css; do
  sudo install -o 33 -g 33 -m 644 /home/deploy/wave5-css-tmp/$f \
    /var/www/saltelli/wp-content/themes/saltelli/assets/css/$f; done
  rm -rf /home/deploy/wave5-css-tmp'
ssh deploy@178.62.207.50 'cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli'
```

**Nota per il runbook DEPLOY.md:** il pattern `rsync diretto da locale` documentato in CLAUDE.md non funziona più (perms www-data sul theme dir). Usare il 2-step sopra, o `rsync --rsync-path="sudo rsync" --chown=33:33`.

Post-deploy listing droplet:
```
-rw-r--r-- 1 33 33   8620 May 11 10:18 tokens.css      (era 4492)
-rw-r--r-- 1 33 33  12075 May 11 10:18 components.css  (era 12045)
-rw-r--r-- 1 33 33 289808 May 11 10:18 sections.css    (era 289683)
-rw-r--r-- 1 33 33   4903 May 11 07:27 base.css        (invariato — NON toccato)
```

Byte-equality verificata: `md5(served via curl) == md5(local worktree)` per tutti e 3 i file. ✓

## HTML markup invariant (5 pagine chiave)

`curl` di `/ /chi-siamo/ /aree-di-pratica/ /costi-e-consulenze/ /contatti/` prima e dopo il deploy. Diff:

| Pagina | Diff HTML |
|---|---|
| `/` | solo `wpa_field_value` (nonce random per-request del plugin honeypot) — *nessun altro byte* |
| `/chi-siamo/` | idem |
| `/aree-di-pratica/` | idem |
| `/costi-e-consulenze/` | idem |
| `/contatti/` | idem |

Il `wpa_field_value` cambia ad **ogni** page load (anti-spam token), indipendentemente dal deploy. **Markup server-rendered: invariato.** Atteso — modifiche solo a file CSS, zero PHP/template. `<h1 class="sl-hero__headline" data-split-reveal>` presente, 5 `<link>` CSS presenti con `?ver=1.3.11-wave4-7-fix-5-cleanup` (invariato — no version bump).

## CSS values live on staging (curl, cache-bypassed con ?cb=timestamp)

`tokens.css` servito:
```
--fs-display: clamp(var(--fs-display-floor), var(--fs-display-vw), var(--fs-display-max));   /* = clamp(80px, 9vw, 132px) — era clamp(48px, 8vw, 120px) */
--fs-h1: clamp(var(--fs-h1-floor), var(--fs-h1-vw), var(--fs-h1-max));                       /* = clamp(48px, 6vw, 96px) — era clamp(36px, 5vw, 64px) */
--fs-h3: clamp(var(--fs-h3-floor), 2.2vw, var(--fs-h3-max));                                 /* = clamp(22px, 2.2vw, 32px) — era clamp(20px, 2vw, 28px) */
--fs-body: 16px;                                                                            /* era clamp(16px, 1.1vw, 18px) */
--fs-caption: 11px;                                                                         /* NUOVO */
--lh-display: 0.98;                                                                         /* era 1.05 */
--lh-body: 1.7;                                                                             /* era 1.65 */
--ls-display: -0.035em;                                                                     /* era -0.02em */
--radius-xs: 2px;                                                                           /* NUOVO */
```
`components.css` servito: `.sl-mono` → `font-size: var(--fs-caption)`; `.sl-btn` → `font-size: var(--fs-body)` (no `letter-spacing` fantasma); `.sl-area` → `gap: var(--s-5)` / `padding: var(--s-4) 0`; `.sl-area__num` → `font-size: var(--fs-caption)`. ✓
`sections.css` servito: `.sl-hero__headline` (×3 breakpoint) → `font-size: var(--fs-display)` / `line-height: var(--lh-display)` / `letter-spacing: var(--ls-display)`; `.sl-hero__eyebrow` / `.sl-hero__colophon-label` / `.sl-hero__colophon-body` → `font-size: var(--fs-caption)`; `.sl-page__breadcrumb` → `letter-spacing: var(--ls-mono)`. ✓

## ⚠ Browser cache — hard refresh richiesto per visitatori di ritorno

Header servito: `Cache-Control: public, immutable, max-age=31536000` (1 anno). Il `?ver=` non è cambiato (no version bump, per direttiva prompt). Conseguenza:
- **Visitatore fresco** (no cache) → vede il realign subito.
- **Visitatore di ritorno** (CSS in cache sotto `?ver=1.3.11-wave4-7-fix-5-cleanup`) → continua a vedere il CSS vecchio finché non fa **hard refresh** (Cmd/Ctrl+Shift+R) o la cache scade.

Per validazione manuale su staging: **hard refresh obbligatorio**. Per renderlo live a tutti i visitatori di ritorno: bump `SALTELLI_THEME_VERSION` in functions.php (follow-up, fuori scope di questa wave lean).

## Validazione visiva manuale — DA FARE (non eseguibile da CLI)

Apri https://staging.studiolegalesaltelli.it/ con **hard refresh**, viewport `<768px` (DevTools device toolbar):
- [ ] Hero headline ("Diritto, con misura.") — font-size visibilmente **più grande** rispetto a prima (da ~56-64px a min **80px**)
- [ ] Hero headline — letter-spacing **più tight** (da ~-0.02/-0.03em a -0.035em)
- [ ] Hero headline — line-height su mobile **più stretto** (da 1.05 a 0.98 — le 2 righe più vicine)
- [ ] Eyebrow ("STUDIO LEGALE · NAPOLI…") — leggermente **più grande** (10→11px)
- [ ] Colophon labels ("Coordinate" ecc.) — leggermente **più grandi** (9-10→11px)
- [ ] Body legal pages (`/costi-e-consulenze/`, `/contatti/`) — leading **più ampio** (1.65→1.7); su schermi wide il body è 16px fisso (era fino a 18px)
- [ ] Breadcrumb — invariato (era già a 0.08em via override; ora token-driven)
- [ ] Areas list rows (`/aree-di-pratica/` hub) — leggermente **più compatte** (padding 28→24px) — verificare che non collassino
- [ ] Footer copyright ("© 2026 …") — tracking più ampio (0.04→0.08em) — **valutare se accettabile** su stringa lunga lowercase, eventualmente rivedere

## Esito

- Deploy: ✓ (3 file, ownership/perms corretti, md5 match)
- Markup invariant: ✓ (solo honeypot nonce diverge)
- CSS values live: ✓ (verificati via curl)
- Validazione visiva pixel: ⏳ richiede occhio umano + hard refresh
