# Visual Walkthrough — v0.7.0-beta-pain-points-fixed

**Data:** 2026-04-30
**Tester:** Claude (orchestrator)
**Tool:** Claude in Chrome (browser_batch + javascript_tool)
**Build sotto test:** v0.7.0-beta-pain-points-fixed
**Tempo:** ~12 minuti

---

## Risultati aggregati

| # | Punto | Status | Note |
|---|---|:---:|---|
| 1 | Hero 100vh, 3 righe, no overlap | ✅ PASS | heroHeight 978px, ratio 1.35 vs viewport 723. h1Count=1, hasMain=true, hasInner=true |
| 2 | Lista 19 aree tier-1 evidenziato | ✅ PASS | areasTotal=19, tier1Count=3 (Tributario, Lavoro, Famiglia LGBTQ+) con drop-cap accent bronze |
| 3 | Layout asimmetrico | ✅ PASS | Sezioni respirano, ritmo verticale generoso |
| 4 | Drop-cap "L" Lo studio | ✅ PASS | Validato in walkthrough precedente, design system locked |
| 5 | 4 avvocati asimmetrici, foto Emiliano | ✅ PASS | Foto reale Emiliano (navy abito), 3 placeholder gradient editoriali |
| 6 | Casi rappresentativi tipografici | ✅ PASS | Validato in walkthrough precedente |
| 7 | Footer dark navy | ✅ PASS | 3 colonne, NAP completo |
| 8 | /costi/ layout editoriale | ✅ **PASS PERFETTO** | **fix P0.1 risolto**: eyebrow "§ 01 — COME FUNZIONA" sinistra colonna stretta + body destra colonna larga (asimmetrico desktop ≥1024px) |
| 9 | Single-competenza tier-1 | ✅ **PASS PERFETTO** | h1Count=1, h2InCaps=0, h2Sample tutti sentence-cased ("Avvocato tributarista Napoli"), 5 accordion `+` editoriali oro a destra, subline italic serif (no mono caps) |
| 10 | Single-avvocato Emiliano | 🟡 **WARN** | Sticky TEL/EMAIL si sovrappongono leggermente alla foto sulla sinistra. Funzionale ma non ottimale visivamente |
| 11 | Archive /tipo-area/* | ✅ PASS | Funzionale, fallback archive.php |
| 12 | Mobile 375px responsive | ❌ **FAIL (2 issue)** | (a) Hero "Diritto, con misura" rende su 2 righe ("Diritto, con" + "misura.") invece di 3. (b) Tag "TIER 1 · APPROFONDIMENTO →" / "CONTENZIOSO AMMINISTRATIVO →" / "PER LE IMPRESE →" si sovrappongono al titolo della competenza nella lista aree |

---

## Score globale

**10 PASS · 1 WARN · 1 FAIL**

→ **Decisione: GO con caveat** per Step E Template Polish, integrando 3 mobile fix nel prompt successivo.

---

## Issue residui (lista per priorità)

### M1 (P0 mobile — visivamente grave)
**Overlap tag tipo-area + titolo competenza nella lista aree mobile.**
Il tag `.sl-area__meta` (es. "TIER 1 · APPROFONDIMENTO →") è renderizzato in posizione assoluta o flexbox row che su mobile finisce sopra/dentro la "D" del titolo serif gigante. Su desktop funziona perché il layout grid 2-col è scope `min-width: 1024px`.

**Fix:** in `sections.css`, dentro `@media (max-width: 1023px) { .sl-area__meta { ... } }` forzare `position: static; display: block; margin-top: 8px; clear: both;` o equivalente.

### M2 (P0 mobile — accettabile ma non ideal)
**Hero mobile rende headline su 2 righe ("Diritto, con" + "misura.") invece di 3.**
Il design Claude prevedeva 3 righe distinte ("Diritto," / "con" / "misura.") sia desktop che mobile. Su mobile attuale il wrap fa "Diritto, con" su una riga e "misura." su un'altra.

**Fix:** mobile-only `.sl-hero__headline { max-width: 8ch }` per forzare wrap parola-per-parola, oppure `<br>` espliciti nel template (più robusto).

### M3 (P1 desktop — minor)
**Sticky TEL/EMAIL sovrappone foto avvocato.**
I bottoni sticky `.sl-attorney__sticky-btn` sono `position: fixed; left: clamp(16px, 3vw, 48px)` ma a 1440px finiscono sopra il bordo sinistro della foto.

**Fix:** spostare a `left: 8px` o usare `position: sticky` (non fixed) per stay nel margine interno del container.

---

## Lavoro brillante validato

Il Pain Points Refinement Agent ha eseguito **eccellente lavoro chirurgico**:

- **P0.1 /costi/** layout sbilanciato → asimmetrico editoriale perfetto
- **P0.2** doppio approccio: PHP script `fix-headings.php` + CSS guard. Trovato e risolto problema duplicate H1 (14 H1→H2) e ALL-CAPS (9 sentence-cased) preservando sigle e proper nouns
- **P0.3** Hero overlap risolto adattando a `.sl-hero__inner` esistente (zero rischio regressione template)
- **P1.3** mismatch markup detected (`.sl-acc` vs `.sl-faq`) risolto con CSS unificato
- **P1.4** trade-off intelligente: italic serif per CTA-note, mono uppercase ridotto per `.sl-contact__eyebrow` (coerenza con § 06)
- Foto Emiliano + bio_estesa avvocati Step D **PRESERVATE** (verificato)

---

## Screenshot evidenziali

Allegati nella sessione walkthrough (visibili nella chat orchestrator):

- ss_6905i7eak — Hero homepage above-fold
- ss_8339gu9i3 — Hero homepage scrolled (subline italic visible)
- ss_60876w9r8 — /costi/ above-fold (capsule editoriale)
- ss_7519yn17w — /costi/ § 01 layout asimmetrico
- ss_2554gvk6x — /competenze/diritto-tributario/ FAQ accordion `+` editoriale
- ss_93280fvx2 — /avvocati/emiliano-saltelli/ con sticky overlap
- ss_3075rpy8c — Mobile 375px hero
- ss_4487197z8 — Mobile 375px lista aree (overlap visibile)

---

## Versione successiva attesa

**v0.8.0-beta-templates-mobile** post Step E (Template Polish + Mobile Fix integrati).

Target Step E:
1. M1+M2+M3 mobile fix integrati
2. 9 template smoke test sistematico (single-avvocato × 4, single-competenza tier-1 × 3, single-competenza tier-2 × 2 random, single blog, page generic, 404, search)
3. taxonomy-tipo-area.php template dedicato (sostituisce fallback archive.php)
4. Cross-browser quick check (Chrome/Safari/Firefox)
5. Lighthouse manual run + report

---

*Walkthrough completato. Procedo con commit + prompt Step E.*
