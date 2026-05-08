# Phase 3b — Manual validation of 5 fallback candidates

**Date**: 2026-05-08
**Branch**: `feat/wave4-7-fix-2-true-fix`
**Source**: investigation report `phase3b-deep-investigation.txt` (Section 5).

The investigation regex correlated each `saltelli_field/option` empty default with the
**next** `else` block in the file. Cross-line correlation produced false positives:
the `else` block matched the *next* `if/else` in the file, not necessarily the one
guarding that variable. Re-read file context for each candidate.

| # | File:line | Var | Field | Class | Why |
|---|---|---|---|---|---|
| 1 | `front-page.php:199` | `$studio_body` | `studio_body` | **EDITORIAL** | True `if/else` on `$studio_body`. `else` block = 3 hardcoded paragraphs (1999 origin story, ascolto, Vannella Gaetani). Editor-relevant copy that Elena should own. |
| 2 | `single-avvocato.php:14` | `$ruolo` | `hero_role`/`ruolo_breve` | UX_PLACEHOLDER | `else` at line 56 belongs to `has_post_thumbnail()` (photo placeholder), not `$ruolo`. The actual `$ruolo` render at line 64-66 is `if ($ruolo) ... endif` — no HTML fallback. |
| 3 | `single-avvocato.php:25` | `$linkedin` | `same_as_linkedin` | UX_PLACEHOLDER | At line 25 `$linkedin` falls back to `saltelli_attorney_linkedin()` helper (hardcoded mapping in `inc/helpers.php`, not editor-visible). Actual link rendered conditionally; no HTML editor copy. |
| 4 | `template-parts/page-lo-studio.php:182` | `$bio_breve_av` | `bio_breve` | UX_PLACEHOLDER | `else` at line 201 belongs to the photo `has_post_thumbnail()` chain, not bio_breve. The bio render at line 210-214 is `if ($bio_breve_av) ... elseif (!empty($specs_av))` — alternate content, not editor copy. |
| 5 | `archive-avvocato.php:87` | `$ruolo` | `ruolo_breve` | UX_PLACEHOLDER | `else` at line 104 = `<p>Nessun avvocato pubblicato.</p>` — empty-state copy for the WHOLE archive (no posts), not a per-card fallback for `$ruolo`. The `$ruolo` render is conditional. |

## Conclusion

**Phase 1.B scope**: only `studio_body` (1 field). The other 4 candidates are
either decorative placeholders (`<span class="*-placeholder">`), alternate
content (specs vs bio), or empty-state copy (full archive without items) — none
of them are editor-visible HTML fallbacks for an empty SCF field.

`bio_breve` is a per-CPT field (avvocato), not Theme Options — Elena edits it
directly on the avvocato post; no Theme Options wiring needed.

`ruolo_breve` and `same_as_linkedin` are similarly per-CPT fields; they have no
Theme Options counterpart and no editorial fallback HTML.

Proceed with Phase 1.B targeting `studio_body` only.
