---
name: Studio Legale Saltelli & Partners
description: Editorial legal-luxury system — navy/cream/bronze, Playfair display + DM Sans + JetBrains Mono.
colors:
  background: "#FAFAF8"
  surface: "#F2F0EA"
  primary: "#1B2B4B"
  accent: "#B8860B"
  text: "#2D2D2D"
  text-muted: "#6B6B6B"
  border: "#E5E0D5"
typography:
  display:
    fontFamily: "Playfair Display, Cormorant Garamond, Georgia, serif"
    fontSize: "clamp(80px, 9vw, 132px)"
    fontWeight: 400
    lineHeight: 0.98
    letterSpacing: "-0.035em"
  h1:
    fontFamily: "Playfair Display, Georgia, serif"
    fontSize: "clamp(48px, 6vw, 96px)"
    fontWeight: 400
    lineHeight: 1.05
    letterSpacing: "-0.02em"
  h2:
    fontFamily: "Playfair Display, Georgia, serif"
    fontSize: "clamp(28px, 3.5vw, 44px)"
    fontWeight: 400
    lineHeight: 1.15
    letterSpacing: "-0.01em"
  h3:
    fontFamily: "Playfair Display, Georgia, serif"
    fontSize: "clamp(22px, 2vw, 32px)"
    fontWeight: 400
    lineHeight: 1.2
    letterSpacing: "-0.005em"
  lede:
    fontFamily: "Playfair Display, Georgia, serif"
    fontSize: "22px"
    fontWeight: 400
    fontStyle: "italic"
    lineHeight: 1.5
    letterSpacing: "normal"
  body:
    fontFamily: "DM Sans, Satoshi, -apple-system, sans-serif"
    fontSize: "16px"
    fontWeight: 400
    lineHeight: 1.7
    letterSpacing: "normal"
  body-marketing:
    fontFamily: "DM Sans, sans-serif"
    fontSize: "18px"
    fontWeight: 400
    lineHeight: 1.65
    letterSpacing: "normal"
  caption:
    fontFamily: "JetBrains Mono, ui-monospace, monospace"
    fontSize: "11px"
    fontWeight: 400
    lineHeight: 1.4
    letterSpacing: "0.08em"
    textTransform: "uppercase"
spacing:
  s-1: "4px"
  s-2: "8px"
  s-3: "16px"
  s-4: "24px"
  s-5: "32px"
  s-6: "48px"
  s-7: "64px"
  s-8: "96px"
  s-9: "128px"
  s-10: "192px"
rounded:
  none: "0"
  xs: "2px"
  sm: "4px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.background}"
    typography: "{typography.body}"
    rounded: "{rounded.none}"
    padding: "16px 24px"
  button-primary-hover:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.background}"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.primary}"
    rounded: "{rounded.none}"
    padding: "16px 0"
  link-editorial:
    backgroundColor: "transparent"
    textColor: "{colors.primary}"
    typography: "{typography.body}"
  eyebrow:
    backgroundColor: "transparent"
    textColor: "{colors.text-muted}"
    typography: "{typography.caption}"
---

## Overview

The Saltelli system is **editorial legal-luxury**. The page IS the brand: typography carries the message, color is restrained navy/cream/bronze, and spacing creates the calm authority. Anti-stock-photo by design — silhouettes, ratios, mono metadata, bronze hairline accents.

**Theme:** light only. The audience is reading legal content during work hours on a desktop or commuting on a phone — always in lit environments, never in dim moods. No dark mode toggle.

**Color strategy:** Restrained. Tinted neutrals (`#FAFAF8` cream, `#F2F0EA` surface) carry 95%, navy `#1B2B4B` is the only saturated weight, bronze `#B8860B` ≤ 10% strictly for accent emphasis (CTA hover, drop-cap, blockquote rule, accordion arrow).

**Register:** brand. Marketing site, not application UI. Design IS the product.

## Colors

| Role | Token | Hex | Usage |
|---|---|---|---|
| Background | `--background` | `#FAFAF8` | Page surface, pristine cream |
| Surface | `--surface` | `#F2F0EA` | Press strip, /costi/ CTA, accordion bg |
| Primary | `--primary` | `#1B2B4B` | Headlines (Playfair display), navy buttons, footer bg |
| Accent | `--accent` | `#B8860B` | Bronze: drop-cap, CTA hover, focus-visible outline, blockquote rule, em-dash bullet |
| Text | `--text` | `#2D2D2D` | Body copy. **Never `#000000`** |
| Text-muted | `--text-muted` | `#6B6B6B` | Captions, mono metadata, disabled, secondary lede |
| Border | `--border` | `#E5E0D5` | Hairlines, dividers, card border, blog row dividers |

**Rules:**
- Never use `#000` or `#fff`. Tinted toward warm cream (background) or cool navy (primary).
- Bronze `#B8860B` is **scarce**: never two bronze elements in the same viewport. Used as accent restraint.
- Pure white `#FFFFFF` only inside `.sl-whatsapp-sticky` SVG icon for brand fidelity (#25D366 background).

## Typography

**Pairing (locked):** Playfair Display 400 (display + headlines) + DM Sans 400/500 (body) + JetBrains Mono 400 (metadata).

**Hierarchy ratios (≥1.25 between steps):**
- Hero display 132px → H1 96px = **1.375** ✓
- H1 96px → H2 44px = **2.18** ✓
- H2 44px → H3 32px = **1.375** ✓
- H3 32px → body 16px = **2.0** ✓ (intentional jump for readability)
- Body 16px → caption 11px = **1.45** ✓

**Letter-spacing optical (Playfair):**
- ≥80px display: `-0.035em` (visual tightness for large display)
- 48-80px H1: `-0.02em`
- 28-44px H2: `-0.01em`
- 22-32px H3: `-0.005em`
- Body DM Sans: normal (no manual tracking — DM Sans is optically tuned)
- Mono caption: `+0.08em` (always uppercase)

**Italic stress:** Playfair italic carries the lede sotto h1 (single blog post + competenza tier-1) and the drop-cap "L" §02 Lo studio. Never italic body — use semantic `<em>` only.

**Weight discipline:**
- Display + headings: weight **400** only (Playfair). Never 700 — Playfair Display SemiBold/Bold breaks the editorial silence.
- Body: 400 default, **500** for `<strong>`, 700 reserved for `.sl-mono` numerals where tabular emphasis required.
- Mix of 400 + 500 in the same paragraph allowed (semantic emphasis), 400 + 700 forbidden (visual weight inconsistency).

**Line-length:**
- Body prose `.sl-page__prose p` / `.sl-post__body p`: max-width **60ch** (≈ 65ch with average char width)
- Hero lede `.sl-hero__subheadline`: 44ch
- Editorial blockquote: 60ch
- Display headline: no max-width constraint, typography drives length

**Line-height optical:**
- Display ≥80px: **0.98** (tight for visual cohesion)
- H1: **1.05**
- H2: **1.15**
- H3: **1.2**
- Lede italic Playfair: **1.5**
- Body DM Sans: **1.7** (legal content density requires generous leading)
- Mono caption: **1.4**

## Elevation

**No elevation.** This is editorial brand work — flat surfaces, hairlines for separation. No drop-shadows, no card shadows, no glassmorphism.

**Exceptions (necessary):**
- `.sl-whatsapp-sticky` mobile fixed button: `box-shadow: 0 4px 16px rgba(37, 211, 102, 0.32)` (brand WhatsApp brand fidelity, brand-color tinted shadow not neutral)
- Focus-visible outline: `2px solid var(--accent)` + `outline-offset: 4px` (a11y, not elevation)

**Borders:**
- `1px solid var(--border)` for hairline dividers, card edges, blog row separators
- `2px solid var(--accent)` for blockquote left rule (editorial pull-quote)

## Components

**Button — primary** (`.sl-btn.sl-btn--primary`)
- Navy background, cream text, no rounded, padding 16px 24px
- Inline arrow `→` after label
- Hover: bronze background, 200ms quart-out

**Button — ghost** (`.sl-btn.sl-btn--ghost`)
- Transparent, navy text, hairline border-bottom only
- Used for `/contatti/` secondary CTA

**Eyebrow / Caption** (`.sl-mono`)
- JetBrains Mono 11px, +0.08em, UPPERCASE, text-muted
- Sits above h1/h2 with 24px gap, marks section number "§ 01" or pattern "STUDIO LEGALE · NAPOLI · CHIAIA · DAL 1999"

**Breadcrumb** (`.sl-page__breadcrumb`)
- Same mono caption visual weight
- Separator `/` muted 50% opacity, 4px margin-inline
- Current page navy weight 500, others muted with bronze hover bottom-border

**Drop-cap §02 Lo studio** (`.sl-studio__prose p:first-of-type::first-letter`)
- Playfair Display 84px, line-height 0.85, color bronze, `float: left`, `margin: 0 12px -8px 0`
- Visible only desktop (≥1024px)

**Lede italic** (`.sl-hero__subheadline`, `.sl-post__lede`, `.sl-competenza__lede`)
- Playfair italic, 22px desktop / 17px mobile, navy color, line-height 1.5
- Sits below h1 with 16-24px gap

**Accordion** (`.sl-acc`, `details > summary`)
- Hairline border-bottom
- Summary: navy weight 400, bronze chevron `→` rotates 90deg on `[open]`
- 200ms ease-editorial transition

**Tag pill** (`.sl-tag`, `.sl-area__cat`)
- 1px hairline border, 4px 10px padding, mono caption typography
- No background fill, transparent

**Card — case** (`.sl-cases__row`)
- Grid 200px / 1fr / 200px (id mono / desc Playfair italic / outcome bronze)
- Hairline border-bottom only, no card chrome

**Card — area row** (`.sl-area`)
- Number `01 / 19` mono → title Playfair → tier label mono → arrow `→`
- Hover: background `var(--surface)` + arrow translateX(8px), 200ms

**Sticky attorney CTA** (`.sl-attorney__sticky`)
- Bottom 32px right 32px desktop, only on `single-avvocato.php`
- "Contatta L'Avv. {Nome}" navy → bronze hover

## Do's and Don'ts

**Do**
- Use Playfair italic to mark lede, blockquote, drop-cap, area `em` modifiers ("Tre presidiate in *profondità*")
- Use mono caption for ALL metadata (dates, tags, eyebrows, numerated lists "01 / 19")
- Use bronze sparingly — one bronze element per viewport unless intentional cluster (e.g. drop-cap + accent rule)
- Use em-dash `—` as bullet glyph (`::before { content: "—" }`) for editorial lists; never bullet `•`
- Use `.sl-container` for all horizontal alignment — section-level padding is always padding-block ONLY (post v0.14.1 cleanup)
- Use `prefers-reduced-motion: reduce` opt-out for every transform/scale animation
- Sentence-case Italian sigle (INPS, IRPEF, IMU, RC, LGBTQ+) — preserve uppercase, never normalize

**Don't**
- Don't use `#000` or `#fff` — pure values break the cream/navy tint discipline
- Don't mix 400+700 weights in the same hierarchy role
- Don't use Playfair below 18px — kerning collapses, switch to DM Sans
- Don't use ALL-CAPS body text — only mono caption may be uppercase
- Don't add card shadows, glassmorphism, gradient fills — editorial is flat by definition
- Don't introduce new font families without explicit instruction — the pairing is locked
- Don't override design tokens in component CSS — read them, never write them
- Don't optimize desktop "wow" at the cost of mobile LCP
