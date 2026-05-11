---
title: Wave 5 STEP 4 — phantom (hardcoded, no-token) typography values remaining in sections.css
date: 2026-05-11
author: Claude Code (branch feat/wave5-step4-sections-cleanup)
companion: 01-inventory-classification.md (the replacements that WERE made)
scope: assets/css/sections.css AFTER the 328 conservative token swaps of STEP 4
status: ~460 typography literals still hardcoded — listed exhaustively below with file:line + rationale
purpose: hand-off for a future "phantom resolution" wave (promote-to-token vs eliminate vs leave)
---

# Phantom typography values still hardcoded in `sections.css`

After Wave 5 STEP 4's conservative pass, **~460 typography literals remain hardcoded** in
`sections.css`. They fall into three buckets:

- **A — value has no matching token** in `tokens.css` (the bulk: `13px`, `10px`, `17px`,
  `-0.015em`, `0.06em`, `1.1`, `1.55`, `1.6`, ~70 ad-hoc `clamp()` curves, …).
- **B — value is ambiguous** between two equal-value tokens (the `font-size: 22px` case:
  `--fs-h3-floor` and `--fs-lede` are both `22px`).
- **C — value matches a *deprecated* token only** (e.g. `1.65` was the old `--lh-body`; swapping
  to the current `var(--lh-body)` would change computed leading 1.65 → 1.7 — not computed-neutral,
  so left alone).

All line numbers below are **post-STEP-4** (`feat/wave5-step4-sections-cleanup` HEAD).

---

## 1 · font-size — bare px values (no token)

| Value | × | Line numbers | Notes / typical selectors |
|---|---:|---|---|
| `9px`  | 1  | 917 | `.sl-footer__legal`-ish micro-text. Below mobile legibility floor — candidate for **elimination** (bump to 10–11px) rather than a `--fs-nano` token. |
| `10px` | 21 | 212, 447, 655, 672, 743, 849, 1298, 1321, 1369, 1482, 1507, 1682, 1715, 2615, 3671, 6461, 6493, 6544, 6588, 6635, 6731 | `__num`, `__role`, `__id`, `__item-label`, eyebrow-ish mono. The recurring "metadata-too-small" value. Decision: promote to a `--fs-eyebrow: 10px`? Or normalise these to `--fs-caption` (11px)? They're the design-system's caption-nebula (see baseline audit §3a). |
| `13px` | 27 | 365, 370, 925, 947, 957, 961, 1084, 1463, 1473, 1745, 2639, 3680, 4477, 5595, 5637, 5694, 5732, 6238, 6570, 6596, 6603, 6608, 7824, 7932, 8269, 8731, 9508 | Footer copy, colophon body, glossary counters, breadcrumb-ish. **Largest no-token px bucket.** Candidate `--fs-body-sm: 13px`. |
| `15px` | 8  | 1783, 2827, 5927, 6742, 7551, 8045, 8756, 9644 | small-prose. Candidate `--fs-body-xs: 15px` or normalise to 16px (`--fs-body`). |
| `17px` | 19 | 206, 316, 745, 985, 1072, 1371, 3214, 3715, 4430, 4528, 5176, 5622, 5708, 5973, 6216, 7612, 8713, 8992(`!important`), 9347 | The "mobile lede" size per DESIGN.md (lede is 17px mobile / 22px desktop) but there is **no token** for it. Strong candidate to promote: `--fs-lede-mobile: 17px`. |
| `19px` | 6  | 588, 3558, 4039, 5893, 6119, 9111 | lede/prose intermediate. Candidate normalise to 18px (`--fs-body-marketing`) or 20px. |
| `20px` | 5  | 772, 1391, 4180, 5292, 5795 | `__desc`, prose. Candidate `--fs-body-lg: 20px`. |
| `24px` | 9  | 773, 873, 890, 1392, 1438, 3898, 4026, 5739, 7666 | `__outcome`, `__item-value`, `__big`. Coincides with spacing `--s-4: 24px` but that's a spacing token, not type. Candidate `--fs-lede-lg: 24px` or fold into the `--fs-h3` band. |
| `26px` | 6  | 658, 1301, 3674, 3959, 8008, 8041 | `__name` (lawyer/team), small headings. Sits between `--fs-h3-floor` (22) and `--fs-h3-max` (32) — candidate to become `clamp(...)` using `--fs-h3`, or a fixed `--fs-h3-name: 26px`. |
| `28px` | 8  | 275, 500, 945, 1028, 3815, 4191, 5698, 5919 | `__brand`, `__name`, timeline-year. **= the `--fs-h2` clamp floor** but no standalone `--fs-h2-floor` token. Candidate: expose `--fs-h2-floor` (parallel to how `--fs-h3-floor`/`--fs-h3-max` already exist) → then this becomes `var(--fs-h2-floor)`. |
| `36px` | 6  | 522, 712, 730, 1157, 1349, 3746 | `__name` desktop, mid headings. Between `--fs-h3-max` (32) and `--fs-h2` max (44). Candidate fixed `--fs-h2-sm: 36px`. |
| `40px` | 2  | 405, 619 | `.sl-areas__title`, `.sl-studio__title`-ish base sizes. Candidate fold into `--fs-h1-floor`/`--fs-h2`. |
| `52px` | 1  | 6484 | one-off 404 numeral. Decorative — leave or fold into a display token. |
| `56px` | 2  | 838, 3921 | `.sl-contact__big`-ish, competenza pull. Candidate `--fs-h1-sm: 56px`. |
| `60px` | 2  | 537, 9410(`!important`) | display-ish one-offs. Decorative. |
| `64px` | 1  | 5815 | `.sl-glossario__letter` big drop-letter. Coincides with spacing `--s-7: 64px` but that's spacing. Decorative display numeral. |
| `72px` | 2  | 3813, 5892 | `.sl-chi-siamo__dropcap`, `.sl-404__dropcap`. Decorative drop-caps — candidate `--fs-dropcap: 72px`. |
| `84px` | 2  | 593, 9383(`!important`) | `.sl-studio__plate-line`-ish big display. Decorative. |
| `0.9em` | 1 | 2373 | relative em inside a larger-text context — intentionally relative, **no px-token applies**. Leave. |

**Bucket A font-size px subtotal: 129 occurrences (across 19 distinct values; `22px`/23 is counted separately in §2 below).**

---

## 2 · font-size — `22px` AMBIGUOUS (bucket B — 23 occurrences, deliberately not replaced)

`tokens.css` has **two tokens worth `22px`**: `--fs-h3-floor` (the H3 clamp floor) and `--fs-lede`
(the desktop Playfair-italic lede). A mechanical value-swap can't pick. The 23 call sites, with the
token each *would* take if resolved per-selector:

| Line | Selector (abbrev) | Likely token |
|---|---|---|
| 247  | `.sl-header__brand-title` | `--fs-h3-floor` (it's a title) |
| 354  | `.sl-hero__lede` | `--fs-lede` ✓ |
| 448  | `.sl-areas .sl-area .sl-area__title` | `--fs-h3-floor` |
| 795  | `.sl-press__item` | ambiguous — large body |
| 852  | `.sl-contact__item-value` | ambiguous — large body |
| 1034 | `.sl-header__brand-name` (media) | `--fs-h3-floor` |
| 1117 | `.sl-hero__subheadline` | ambiguous — lede-ish |
| 1405 | `.sl-press__outlet` | ambiguous |
| 1429 | `.sl-contact__big` | ambiguous — large body |
| 1587 | `.sl-areas__list:empty::before` | ambiguous (placeholder text) |
| 1598 | `.sl-team__grid:empty::before` | ambiguous (placeholder text) |
| 3754 | `.sl-chi-siamo__timeline-t` | ambiguous |
| 3786 | `.sl-chi-siamo__cta-lede` | `--fs-lede` ✓ |
| 3860 | `.sl-attorney__casi-outcome` | ambiguous |
| 4257 | `.sl-casi__cta-lede` | `--fs-lede` ✓ |
| 4544 | `.sl-contatti-w3__address` | ambiguous |
| 4690 | `.sl-contatti-w3__come-t` | ambiguous |
| 5589 | `.sl-glossario__lede` | `--fs-lede` ✓ |
| 5883 | `.sl-404__lede`-ish | `--fs-lede` ✓ |
| 6062 | `.sl-404__article-title` | `--fs-h3-floor` |
| 6108 | `.sl-404__cta-text` | `--fs-lede` ✓ |
| 9076 | `.sl-info-page__trust-headline` | `--fs-h3-floor` |
| 9282 | `.sl-team__archive-trust-headline` | `--fs-h3-floor` |

**Recommendation:** this is the single highest-value remaining cleanup (23 lines, all genuinely
token-mappable). Resolve per-selector in a follow-up — roughly half go to `var(--fs-lede)` and half
to `var(--fs-h3-floor)`. Still computed-neutral (both = 22px).

---

## 3 · font-size — `clamp()` curves (bucket A — 121 occurrences, ~70 distinct curves)

None match a token's resolved clamp (the two that did — `clamp(48px,6vw,96px)` = `--fs-h1`,
`clamp(28px,3.5vw,44px)` = `--fs-h2` — were replaced in STEP 4). The rest are ad-hoc. Grouped by
"what design-system band they're drifting around":

### 3a · Display-band drift (≥48px ceilings ≥96px) — should converge on `--fs-display` / `--fs-h1` / a new `--fs-display-sm`
`clamp(40px, 5vw, 72px)` ×8 (L115, 1171, 8659, 9177 + …) · `clamp(48px, 5vw, 72px)` ×4 (L463, 584, 687, 765) ·
`clamp(56px, 7vw, 104px)` ×4 (L3528, 8924`!important`, 9044, 9447) · `clamp(56px, 6.5vw, 96px)` ×4 (L3776, 4245, 6096, 8881`!important`) ·
`clamp(40px, 4.5vw, 64px)` ×3 (L3541, 5761) [and `clamp(36px,4.5vw,64px)` ×2 L5323, 7511] · `clamp(44px, 9vw, 72px)` ×2 (L3812, 4267, 6118) ·
`clamp(40px, 5vw, 64px)` ×2 (L8547`!important`, 8584`!important`) · `clamp(64px, 8vw, 132px)` ×2 (L4012, 9252) · `clamp(64px, 9vw, 140px)` ×3 (L4319, 4817, …) ·
singletons: `clamp(80px,9vw,140px)` L4065 · `clamp(72px,9vw,140px)` L5857 · `clamp(64px,8vw,124px)` L5574 · `clamp(64px,14vw,96px)` L4091 · `clamp(56px,8vw,132px)` L5222 · `clamp(56px,6vw,96px)` L866 (`.sl-contact__title`) · `clamp(56px,6vw,88px)` L8157 · `clamp(56px,12vw,96px)` L4038, 5890 · `clamp(56px,12vw,88px)` L… · `clamp(48px,7vw,96px)` L7432 · `clamp(48px,7vw,132px)` L7317 · `clamp(48px,6vw,64px)` L3625 · `clamp(48px,6.5vw,96px)` L5533 · `clamp(48px,5.5vw,80px)` L5781 · `clamp(40px,9vw,64px)` L7389 · `clamp(40px,6.5vw,96px)` L9565 · `clamp(40px,5vw,84px)` L2196 · `clamp(40px,5vw,56px)` L5357 · `clamp(36px,4.5vw,56px)` L7735 · `clamp(36px,4vw,56px)` ×3 (L6001, 7789, 8046? — actually 7789) · `clamp(32px,3.5vw,48px)` ×3 (L8497`!important`, 9533) · `clamp(32px,3.2vw,40px)` L6685 · `clamp(32px,3.5vw,44px)` L8534`!important`.
> ~14 distinct big-display curves where the design intent is "an H1/display heading at this scale"
> but every one rolled their own floor/`vw`/ceiling. **Highest-value clamp consolidation** — but it
> changes computed values, so it needs design sign-off.

### 3b · H2-band drift (28–32px floor, 36–44px ceiling) — should converge on `--fs-h2` `clamp(28px,3.5vw,44px)`
`clamp(28px, 3vw, 44px)` ×6 (L1927, 1995, 2662, 3125, 7362, 9676) · `clamp(28px, 3vw, 40px)` ×2 (L483, 9134) ·
`clamp(28px, 3vw, 36px)` ×3 (L7953, 8898 + …) · `clamp(28px, 3.2vw, 44px)` L7988 · `clamp(28px, 2.8vw, 36px)` L6839 ·
`clamp(28px, 6vw, 36px)` L2041 · `clamp(32px, 4vw, 56px)` ×5 (L2737, 4379, 4662, 4991, 5142) · `clamp(24px,5vw,32px)` L7392.
> Off mostly by the `vw` term (`3vw`/`3.2vw`/`2.8vw` vs the token's `3.5vw`) or the ceiling (`40`/`36` vs `44`).

### 3c · H3-band drift (22–24px floor, 28–32px ceiling) — should converge on `--fs-h3` `clamp(22px,2.2vw,32px)`
`clamp(22px, 2vw, 32px)` L1832 · `clamp(22px, 2.4vw, 32px)` ×2 (L7543) · `clamp(24px, 2.4vw, 32px)` ×2 (L9339, 9636) ·
`clamp(24px, 2.5vw, 32px)` L4078 · `clamp(22px, 2vw, 28px)` ×3 (L2446, 7344, 7829) · `clamp(22px, 2.4vw, 28px)` ×4 (L5365, 8902, 9145) ·
`clamp(22px, 3vw, 36px)` L5435 · `clamp(20px, 2vw, 28px)` ×2 (L1912, 7327? — 7327 is `clamp(20px,1.6vw,28px)`) · `clamp(20px, 2.4vw, 28px)` L4334 ·
`clamp(20px, 2vw, 26px)` ×4 (L2690, 8391, 9057, 9460) · `clamp(24px, 3vw, 40px)` L3610 · `clamp(24px, 2.4vw, 32px)` (dup above).
> Mostly off by `2vw`/`2.4vw`/`2.5vw` vs the token's `2.2vw`, or by ceiling 28/26 vs 32.

### 3d · Body/lede-band fluid (15–22px) — there is NO fluid body/lede token (only fixed `--fs-body:16px`, `--fs-body-marketing:18px`, `--fs-lede:22px`)
`clamp(16px, 1.4vw, 18px)` ×4 (L2672, 2748, 3135, 7998) · `clamp(16px, 1.6vw, 18px)` L8321 · `clamp(15px, 1.5vw, 18px)` L2208 ·
`clamp(17px, 1.5vw, 19px)` L4958 · `clamp(17px, 1.6vw, 19px)` L5498 · `clamp(17px, 1.5vw, 20px)` ×2 (L5155, 8674) ·
`clamp(18px, 2vw, 22px)` ×3 (L8170, 8275, 9190) · `clamp(18px, 1.8vw, 22px)` L9190 · `clamp(18px, 1.6vw, 22px)` L7442 · `clamp(18px, 1.4vw, 22px)` L9577 ·
`clamp(18px, 2vw, 24px)` ×2 (L4827, 5231) · `clamp(19px, 1.8vw, 24px)` L9265 · `clamp(20px, 1.6vw, 24px)` L2293 · `clamp(20px, 1.5vw, 24px)` L5060 ·
`clamp(20px, 2.2vw, 24px)` L5505 · `clamp(20px, 1.6vw, 28px)` L7327.
> **Candidate: add a fluid body/lede token** — e.g. `--fs-body-fluid: clamp(16px, 1.4vw, 18px)` and
> `--fs-lede-fluid: clamp(18px, 2vw, 22px)` — then ~20 of these collapse onto two tokens.

**Bucket A clamp() subtotal: 121 occurrences.** (Singleton curves not all individually listed above;
`grep -nE "font-size:[ \t]*clamp\(" sections.css` enumerates every one with its line.)

---

## 4 · letter-spacing — em values (bucket A, no token; + a few intentional extremes)

| Value | × | Line numbers | Notes |
|---|---:|---|---|
| `-0.015em` | 21 | 3712, 3749, 3961, 4193, 4760, 5062, 5366, 5436, 5506, 5700, 5921, 6064, 8499, 8536, 8895, 8989(`!important`), 9136, 9344, 9535, 9638, 9678 | Sits between `--ls-h2` (-0.01) and `--ls-h1` (-0.02). **Largest no-token tracking bucket.** Candidate: a `--ls-h2-tight: -0.015em`, or normalise to `--ls-h2`. |
| `-0.025em` | 15 | 839, 1426, 3777, 4246, 5534, 5782, 6098, 7320, 7434, 8159, 8586, 8883, 9046, 9179, 9449 | Between `--ls-h1` (-0.02) and `--ls-display` (-0.035). Often on `__cta-title`/`__big`. Candidate `--ls-h1-tight: -0.025em` or normalise to `--ls-h1`. |
| `0.06em` | 14 | 673, 1322, 1683, 1798, 2053, 2698, 2732, 2872, 8315, 8701, 8979(`!important`), 9333, 9559, 9653 | Sub-mono tracking (mono caption is `--ls-mono` 0.08). The "breadcrumb 0.06em" anti-pattern from the baseline audit §3a. Candidate normalise to `--ls-mono` (0.08) — **but that's a computed change**, so leave for now. |
| `0.04em` | 6 | 3323, 3370, 5640, 6151, 6239, 7483 | Footer-copy mono tracking (baseline audit: `.sl-footer__copy.sl-mono` at 0.04em vs spec 0.08). Same story — normalise = computed change. |
| `0.32em` | 4 | 6456, 6525, 6590, 7038 | All-caps wide labels (`.sl-blog2__*`, `.sl-tier1__*`). Intentional wide tracking — candidate `--ls-label-wide: 0.32em`. |
| `-0.03em` | 4 | 3530, 4068, 5668, 8926 | The "hybrid" hero/heading tracking flagged in the baseline audit (between display -0.035 and h1 -0.02). Candidate: pick `--ls-display` or `--ls-h1` per selector (computed change). |
| `0.42em` | 1 | 6477 | `.sl-logo__kicker-text` — extreme intentional logo tracking. **Leave.** |
| `0.24em` | 1 | 6494 | `.sl-blog2__*` wide label. Leave or `--ls-label-wide`. |
| `0.18em` | 1 | 7467 | `.sl-tier1__*` wide label. Leave or `--ls-label-wide`. |
| `0.16em` | 1 | 6635 | `.sl-foot-badge` italic badge. Leave. |
| `0.01em` | 1 | 1548 | one-off button-ish tracking. Not in DESIGN.md. Candidate eliminate (→ 0). |

**Bucket A letter-spacing subtotal: 69 occurrences (across 11 distinct values).**

---

## 5 · line-height — unitless values (bucket A, no token; + one bucket-C deprecated-value case)

| Value | × | Line numbers | Notes |
|---|---:|---|---|
| `1.1`  | 21 | 209, 524, 659, 732, 892, 988, 1159, 1302, 2742, 3675, 4381, 4664, 4950, 6840, 7512, 7736, 7790, 8498, 8535, 8986(`!important`), 9534 | Tighter than `--lh-heading` (1.15). On `__brand-sub`, `__title` bases, `__name`. Candidate `--lh-heading-tight: 1.1` or normalise to `--lh-h3` (1.2) / `--lh-heading` (1.15) per selector (computed change). |
| `1.55` | 18 | 1914, 2210, 2295, 2674, 2750, 3137, 5073, 5718, 6597, 6604, 6609, 7443, 8054, 8675, 8732, 9292, 9578, 9645 | Between `--lh-lede` (1.5) and `--lh-body` (1.7). On `__sub`, `__prose`, `__lede` variants. **Largest no-token leading bucket.** Strong candidate to promote: `--lh-prose: 1.55`. |
| `1.6`  | 16 | 129, 2640, 2828, 3204, 3238, 3761, 4456, 4700, 5372, 5928, 6400, 6504, 6693, 7718, 7961, 8046 | Also between lede and body. On `__p`, `__text`. Candidate fold into a `--lh-prose` token (1.55 or 1.6 — pick one) or normalise to `--lh-body` (1.7) (computed change). |
| `0.95` | 10 | 840, 4013, 4067, 4320, 4818, 5223, 5575, 5858, 6485, 9253 | Tighter than `--lh-display` (0.98). On big display headings / hero h1s. Candidate `--lh-display-tight: 0.95`. |
| `1.3`  | 9  | 853, 1430, 4545, 4613, 4695, 4759, 8276, 9078, 9284 | Between `--lh-h3` (1.2) and `--lh-lede` (1.5). On `__item-value`, `__address`. Candidate `--lh-h3-loose: 1.3`. |
| `1`    | 7  | 866, 3627, 3748, 3922, 4194, 5358, 8548 | Pure 1 — display/numeral tightest. On `.sl-contact__title`, big numerals. Candidate `--lh-tight: 1` or leave (decorative). |
| `2`    | 7  | 900, 926, 958, 1474, 3521, 6550, 6571 | Double leading — footer link lists, spaced vertical menus. Intentional airy stacking. Candidate `--lh-stack: 2` or leave. |
| `1.75` | 6  | 589, 2433, 3559, 7644, 8505, 9112 | Just above `--lh-body` (1.7). Candidate normalise to `--lh-body` (computed change) or `--lh-body-loose: 1.75`. |
| `1.85` | 5  | 910, 1464, 4642, 6513, 6624 | Footer / airy lists. Candidate `--lh-body-loose` family. |
| `1.65` | 5  | 3716, 4478, 5709, 7745, 8993(`!important`), 9348 | **Bucket C** — `1.65` was the *previous* `--lh-body` value (pre Wave 5 STEP 2 rebuild → now 1.7). Swapping to `var(--lh-body)` would change leading 1.65 → 1.7 = a visible change. **Left hardcoded deliberately.** Decision needed: bump these 5 to 1.7 (= `var(--lh-body)`, aligns with DESIGN.md) or keep 1.65 intentionally. |
| `1.25` | 3  | 501, 5061, 6063 | Between `--lh-h3` (1.2) and `--lh-lede` (1.5). Candidate fold into `--lh-h3-loose`. |
| `0.85` | 3  | 539, 3571, 9384(`!important`) | Very tight — big display numerals/plates. Decorative. |
| `1.8`  | 2  | 948, 7470 | Airy. `--lh-body-loose` family. |
| `1.45` | 2  | 4181, 5499 | Just below `--lh-lede` (1.5). Candidate normalise to `--lh-lede` (computed change). |
| `1.02` | 1  | 7433 | Display, near `--lh-display` (0.98) / `--lh-h1` (1.05). One-off. Candidate normalise. |
| `0.9`  | 1  | 5667 | `.sl-glossario__letter` big drop-letter leading. Decorative. |

**line-height subtotal: ~118 occurrences across 16 distinct values** (incl. the 5 bucket-C `1.65`; the row counts above sum to ~116, the ~2-line gap is `!important`/edge formatting variants — re-measure with the command at the bottom of this doc for the exact figure).

---

## Grand total still hardcoded

| Bucket | Count |
|---|---:|
| A — font-size px (no token, excl. `22px`) | 129 |
| B — font-size `22px` (ambiguous: `--fs-h3-floor` vs `--fs-lede`) | 23 |
| A — font-size `clamp()` (no exact token) | 121 |
| A — letter-spacing em (no token) | 69 |
| A+C — line-height (no token; incl. 5× `1.65` = the deprecated old `--lh-body` value) | ~118 |
| **Total** | **~460** |

Cross-check vs the precise re-measure greps: font-size `152` (bare px) `+ 121` (clamp) `= 273`; letter-spacing `69`; line-height `118`. `273 + 69 + 118 = 460`. The 328 that were swapped + 460 remaining = `788` total typography literals on the base commit `b3d8882`.

---

## Recommended order for a future "phantom resolution" wave

Ranked by value/effort, **computed-neutral first** then **design-change** items:

1. **`font-size: 22px` per-selector resolve** (23 lines, **computed-neutral**, no token changes) —
   pick `var(--fs-lede)` vs `var(--fs-h3-floor)` per call site. Pure follow-up to STEP 4.
2. **Expose `--fs-h2-floor: 28px`** in `tokens.css` (parallels existing `--fs-h3-floor`/`--fs-h3-max`)
   → then `font-size: 28px` (8 lines) becomes `var(--fs-h2-floor)`, **computed-neutral**.
3. **Promote the recurring no-token values to named tokens** (DESIGN.md update first, then `tokens.css`
   re-derive, then `sections.css` swap — all **computed-neutral**):
   `--fs-eyebrow: 10px` (21) · `--fs-body-sm: 13px` (27) · `--fs-lede-mobile: 17px` (19) ·
   `--ls-h2-tight: -0.015em` (21) · `--ls-h1-tight: -0.025em` (15) ·
   `--lh-prose: 1.55` (18) + decide 1.6 (16) folds in or gets `--lh-prose-loose: 1.6` ·
   `--lh-heading-tight: 1.1` (21) · `--lh-display-tight: 0.95` (10) · `--lh-h3-loose: 1.3` (9).
   That alone retires ~190 more phantoms with zero visual change.
4. **`clamp()` consolidation** (≈121 lines — **CHANGES COMPUTED VALUES**, needs design sign-off):
   converge the display-band sprawl on `--fs-display` / `--fs-h1` / a new `--fs-display-sm`; the
   H2/H3-band sprawl on `--fs-h2` / `--fs-h3`; add `--fs-body-fluid` / `--fs-lede-fluid` for the
   15–24px fluid bucket. Biggest visual realignment; do under Playwright pixel-diff regression.
5. **Caption-nebula normalisation** (baseline audit §3a — `9/10/12px` metadata → `--fs-caption` 11px;
   `0.04/0.06em` tracking → `--ls-mono` 0.08em; the stale `1.65` leading → `--lh-body` 1.7) —
   **CHANGES COMPUTED VALUES** but small ones; restores the editorial coherence the audit flagged.
6. **Eliminate one-offs** (`0.9` lh on dropcaps, `0.01em` ls, `9px` fs) — case-by-case, minor.

---

## How to re-measure

```sh
F=wp-content/themes/saltelli/assets/css/sections.css
grep -coE 'font-size:[ \t]*-?[0-9]+(\.[0-9]+)?(px|rem|em)([ \t]*!important)?[ \t]*[;}]' $F   # bare px font-size
grep -coE 'font-size:[ \t]*clamp\(' $F                                                       # clamp font-size
grep -coE 'letter-spacing:[ \t]*-?[0-9]+(\.[0-9]+)?em([ \t]*!important)?[ \t]*[;}]' $F        # em letter-spacing
grep -coE 'line-height:[ \t]*[0-9]+(\.[0-9]+)?([ \t]*!important)?[ \t]*[;}]' $F               # unitless line-height
# distinct values: pipe the -oE variant of the same regex through `sort | uniq -c | sort -rn`
```
