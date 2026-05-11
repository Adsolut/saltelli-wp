---
title: Wave 5 STEP 4 — sections.css drift cleanup · inventory & classification
date: 2026-05-11
author: Claude Code (branch feat/wave5-step4-sections-cleanup)
scope: assets/css/sections.css — typography hardcoded values (font-size · letter-spacing · line-height)
baseline: /tmp/drift-audit-baseline.md (git object 3dd00417, 2026-05-08, "605 occorrenze" — pre Wave 5 STEP 2/3 growth)
mode: conservative — ZERO change to computed CSS values; only literals that EXACTLY match a token in tokens.css are swapped to var()
status: DONE — 328 substitutions applied; ~460 phantoms left hardcoded (documented in 02-phantom-values-remaining.md)
---

# sections.css drift cleanup — inventory & classification

## TL;DR

`sections.css` (9720 lines) carried **788 hardcoded typography values** as measured on this branch's
base commit (`b3d8882`) — more than the 605 in the 2026-05-08 baseline audit because Wave 5 STEP 2/3
+ 4 chore commits added new sections in the meantime.

Of those 788:
- **328 (41.6%) were TOKEN-REPLACEABLE** — the literal value byte-for-byte equals a token already
  defined in `tokens.css` (the file rebuilt from `docs/DESIGN.md` in Wave 5 STEP 2). All 328 were
  swapped to `var(--token)`. Computed value provably unchanged (see verification below).
- **~460 (58.4%) are FANTASMA** — the literal value has **no matching token** in `tokens.css`
  (e.g. `13px`, `10px`, `17px`, `-0.015em`, `0.06em`, `1.1`, `1.55`, `1.6`, and ~70 distinct
  ad-hoc `clamp()` curves), **or** is ambiguous between two tokens of equal value (the `22px`
  font-size case — `--fs-h3-floor` and `--fs-lede` are both `22px`). Left hardcoded for now;
  exhaustively listed with `file:line` + rationale in `02-phantom-values-remaining.md`.

No DECORATIVE-but-keep category was needed: every value either matched a token (replaced) or
didn't (phantom). The `0.42em`/`0.32em` extreme letter-spacings on logo/kicker elements are
phantoms, intentional, and noted as such in doc 02.

## Counts (measured, `b3d8882` → this branch)

| Property | Before | Replaced → var() | After (hardcoded) | % resolved |
|---|---:|---:|---:|---:|
| `font-size` (numeric `Npx`/`Nem`) | 279 | 129 | 152 | 46% |
| `font-size` (`clamp()` curves) | 123 | 2 | 121 | 1.6% |
| `letter-spacing` (`Nem`) | 160 | 91 | 69 | 57% |
| `line-height` (unitless) | 226 | 108 | 118 | 48% |
| **Total typography** | **788** | **328** | **460** | **41.6%** |

`font-size: clamp()` is the lowest-yield bucket on purpose: only **two** `clamp()` literals in the
whole file matched a token's resolved value exactly (`clamp(48px, 6vw, 96px)` = `--fs-h1`;
`clamp(28px, 3.5vw, 44px)` = `--fs-h2`). The other 121 are a sprawl of ~70 distinct ad-hoc curves
with slightly-off floors/`vw`/ceilings (`clamp(56px, 6vw, 96px)`, `clamp(48px, 7vw, 96px)`,
`clamp(22px, 2vw, 32px)` vs the token's `2.2vw`, …). Consolidating those is a design decision —
it would change computed values — so it is **out of scope for this conservative pass** and recorded
as a recommendation in doc 02.

Colour hex / `font-weight` were not part of this pass (the baseline audit found `font-weight: 700|bold`
already at 0 hits in `sections.css`; the only hardcoded hex live in `assets/css/critical/*.css` build
artifacts, out of `sections.css` scope).

## TOKEN-REPLACEABLE — what was swapped

`tokens.css` `:root` resolved values used as the matching key (see `tokens.css` lines 36–80):

### font-size

| Literal | → token | × | Match quality |
|---|---|---:|---|
| `11px` | `var(--fs-caption)` | 44 | **semantic-exact** — `--fs-caption` *is* the 11px JetBrains-Mono metadata primitive (eyebrow, breadcrumb, "01 / 19", labels) |
| `12px` | `var(--fs-micro)` | 28 | value-exact — `--fs-micro` is the "decorative micro-label" 12px; some of these 12px are arguably metadata that *should* be 11px (`--fs-caption`) — left as `--fs-micro` (computed-neutral); future wave to decide |
| `14px` | `var(--fs-small)` | 18 | value-exact — `--fs-small` is the 14px utility token (skip-link, accordion +/− icon, "not in DESIGN.md scale") |
| `16px` | `var(--fs-body)` | 14 | **semantic-exact** — `--fs-body` *is* the fixed 16px DM-Sans body |
| `18px` | `var(--fs-body-marketing)` | 15 | value-exact — `--fs-body-marketing` is the 18px DM-Sans marketing-copy token; most call sites are lede/marketing-ish so the fit is reasonable, a few are plain body at 18px |
| `32px` | `var(--fs-h3-max)` | 5 | value-exact — `32px` is the H3 clamp ceiling; used here as a fixed size on heading-ish selectors |
| `96px` | `var(--fs-h1-max)` | 3 | value-exact — `96px` is the H1 clamp ceiling; used here as a fixed display size |
| `clamp(48px, 6vw, 96px)` | `var(--fs-h1)` | 1 | **semantic-exact** — landed on `.sl-page__title` (a page-title H1). |
| `clamp(28px, 3.5vw, 44px)` | `var(--fs-h2)` | 1 | **semantic-exact** — landed on `.sl-competenza__prose h2 / .sl-page__prose h2 / .entry-content h2`. |

Subtotal font-size: **129**.

### letter-spacing

| Literal | → token | × | Match quality |
|---|---|---:|---|
| `-0.02em` | `var(--ls-h1)` | 37 | value-exact — DESIGN.md optical letter-spacing for the 48–80px H1 band; also the most common heading tightness in the file |
| `-0.01em` | `var(--ls-h2)` | 19 | **semantic-exact** — DESIGN.md optical letter-spacing for the 28–44px H2 band |
| `-0.035em` | `var(--ls-display)` | 7 | **semantic-exact** — DESIGN.md optical letter-spacing for ≥80px display |
| `-0.005em` | `var(--ls-h3)` | 4 | **semantic-exact** — DESIGN.md optical letter-spacing for the 22–32px H3 band |
| `0.08em` | `var(--ls-mono)` | 24 | **semantic-exact** — DESIGN.md mono-caption tracking (always uppercase) |

Subtotal letter-spacing: **91**.

### line-height

| Literal | → token | × | Match quality |
|---|---|---:|---|
| `0.98` | `var(--lh-display)` | 14 | **semantic-exact** — DESIGN.md ≥80px display leading; call sites are display-sized headings |
| `1.05` | `var(--lh-h1)` | 14 | **semantic-exact** — DESIGN.md H1 leading |
| `1.15` | `var(--lh-heading)` | 12 | **semantic-exact** — DESIGN.md H2 leading |
| `1.2` | `var(--lh-h3)` | 13 | value-exact — DESIGN.md H3 leading; some call sites are bigger headings using 1.2 generically |
| `1.4` | `var(--lh-mono)` | 9 | value-exact — DESIGN.md mono-caption leading; most call sites are mono `__id`/`__role`/`__num` so the fit is reasonable |
| `1.5` | `var(--lh-lede)` | 29 | value-exact — `--lh-lede` is the Playfair-italic-lede leading; **not all 29 call sites are ledes** — many are generic mid-density prose. Swap is computed-neutral; see "Semantic-coupling caveat" below. |
| `1.7` | `var(--lh-body)` | 17 | **semantic-exact** — DESIGN.md DM-Sans body leading ("legal content density requires generous leading") |

Subtotal line-height: **108**.

**Grand total replaced: 328.**

## FANTASMA — what was left hardcoded (summary; full list in doc 02)

### font-size px (no token)
`9px` (1) · `10px` (21) · `13px` (27) · `15px` (8) · `17px` (19) · `19px` (6) · `20px` (5) ·
`24px` (9) · `26px` (6) · `28px` (8) · `36px` (6) · `40px` (2) · `52px` (1) · `56px` (2) · `60px` (2) ·
`64px` (1) · `72px` (2) · `84px` (2) · `0.9em` (1).
> `28px` is the H2 clamp *floor* but there is no standalone `--fs-h2-floor` token, so a bare `font-size: 28px` is a phantom. Same logic for `44px` (no standalone token; only inside `--fs-h2`).

### font-size — `22px` (AMBIGUOUS — 23 occurrences, deliberately NOT replaced)
`tokens.css` defines **both** `--fs-h3-floor: 22px` **and** `--fs-lede: 22px`. A bare `font-size: 22px`
could be either. The 23 call sites are a genuine mix — `.sl-hero__lede`/`*__cta-lede`/`*__cta-text`
(→ would be `--fs-lede`), `.sl-header__brand-title`/`*__area__title`/`*__name` (→ would be
`--fs-h3-floor`), and several `__item-value`/`__big`/`__address`/`__outcome`/`__article-title` that
fit neither cleanly. Picking the right token per selector is a semantic judgment, not a mechanical
value match, so all 23 were left hardcoded. **Recommendation:** resolve per-selector in a follow-up
(it's the single biggest remaining replaceable bucket — 23 lines).

### font-size — `clamp()` (no exact token; ~70 distinct ad-hoc curves, 121 occurrences)
e.g. `clamp(40px, 5vw, 72px)` (×8) · `clamp(28px, 3vw, 44px)` (×6) · `clamp(32px, 4vw, 56px)` (×5) ·
`clamp(56px, 7vw, 104px)` (×4) · `clamp(56px, 6.5vw, 96px)` (×4) · `clamp(48px, 5vw, 72px)` (×4) ·
`clamp(20px, 2vw, 26px)` (×4) · `clamp(16px, 1.4vw, 18px)` (×4) · … and ~60 more, mostly singletons.
None match `--fs-display` `clamp(80px, 9vw, 132px)`, `--fs-h1` `clamp(48px, 6vw, 96px)` (the 1 that
did was replaced), `--fs-h2` `clamp(28px, 3.5vw, 44px)` (the 1 that did was replaced), or `--fs-h3`
`clamp(22px, 2.2vw, 32px)`. Consolidating these changes computed values → out of scope.

### letter-spacing em (no token)
`-0.015em` (21) · `-0.025em` (15) · `0.06em` (14) · `0.04em` (6) · `0.32em` (4) · `-0.03em` (4) ·
`0.42em` (1) · `0.24em` (1) · `0.18em` (1) · `0.16em` (1) · `0.01em` (1).
> `0.42em`/`0.32em`/`0.24em`/`0.18em`/`0.16em` are extreme tracking on `.sl-logo__kicker-text`,
> `.sl-foot-badge`, and a few all-caps labels — intentional, out of the optical-tracking scale.

### line-height (no token)
`1.1` (21) · `1.55` (18) · `1.6` (16) · `0.95` (10) · `1.3` (9) · `1` (7) · `2` (7) · `1.75` (6) ·
`1.85` (5) · `1.65` (5) · `1.25` (3) · `0.85` (3) · `1.8` (2) · `1.45` (2) · `1.02` (1) · `0.9` (1).
> `1.65` is notable: it was the *old* `--lh-body` value before the Wave 5 STEP 2 rebuild bumped it to
> `1.7`. The 5 remaining `line-height: 1.65` call sites are now stale-by-value but were *not* changed
> (changing them to `var(--lh-body)` would alter computed leading from 1.65 → 1.7 = a visible change).
> Flagged in doc 02 for a deliberate decision.

## Semantic-coupling caveat (read before extending this pass)

A value-exact swap like `line-height: 1.5` → `var(--lh-lede)` keeps the computed value identical
**today**, but creates a coupling: if a future wave retunes `--lh-lede` (say 1.5 → 1.45 for tighter
lede leading), all 29 call sites move with it — including the ones that aren't ledes. Same risk for
`12px → --fs-micro`, `18px → --fs-body-marketing`, `32px → --fs-h3-max`, `96px → --fs-h1-max`,
`1.4 → --lh-mono`.

This was accepted for this pass because (a) the task brief explicitly enumerated these values as
token-replaceable, (b) "conservative" here means *computed-neutral*, which holds, and (c) the
alternative — never indirect to tokens — defeats the point of the design-system cleanup. The clean
long-term fix is to **promote the phantom values to explicit named tokens** (`--lh-prose-loose: 1.55`,
`--fs-lede-mobile: 17px`, …) and split the over-loaded ones — but that is "promote phantom → token",
explicitly deferred to a future wave per the task brief ("Fantasma documentati e lasciati hardcoded
per ora").

## Verification (computed-neutrality proof)

1. **Token resolution check** — every one of the 21 token mappings was resolved against `tokens.css`
   (recursively, e.g. `--fs-h1` → `clamp(var(--fs-h1-floor), var(--fs-h1-vw), var(--fs-h1-max))` →
   `clamp(48px, 6vw, 96px)`) and confirmed byte-equal (whitespace-normalised) to the literal it
   replaced. **21/21 OK.**
2. **Diff is value-only** — `diff` of `sections.css` before/after: 656 changed line-halves (= 2×328),
   **0 changed lines that aren't a `font-size`/`letter-spacing`/`line-height` declaration**, line
   count unchanged (9720), brace count unchanged (1706 `{` / 1713 `}` both sides — the pre-existing
   3-brace imbalance is in comments/content strings, untouched), paren count balanced both sides
   (2086 → 2412, Δ +326 = 328 new `var(...)` minus 2 `clamp(...)` whose parens were already counted).
3. **No malformed output** — `0` occurrences of `var(var(`, `var()`, stray-space-in-`var(`, or
   double-semicolons near the new refs.
4. **var() already works in this file** — `sections.css` had 1063 pre-existing `var(--*)` refs
   (incl. 7 `font-size: var(`, 5 `letter-spacing: var(`, 2 `line-height: var(`); enqueue chain is
   `tokens → base → components → logo → sections`, so `:root` is loaded before `sections.css` applies.

## Tools used

- `grep -nE` for occurrence extraction (per-property, per-value, with `!important` and `[;}]` terminator handling).
- `/tmp/step4_replace.py` — Python regex substitution: `(prop:\s*)LITERAL((?:\s*!important)?\s*[;}])` → `\1var(--token)\2`. Anchored on the property name + immediate value, so longer numbers (`211px`, `1.55`, `0.985`) can't partial-match. `!important` and terminator preserved verbatim.
- Token-resolution verifier (inline Python) for the computed-neutrality proof.
