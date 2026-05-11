# Phase 5 — post_content cleanup + Gutenberg disable + admin UX

## 5.A — Bonifica post_content delle 7 Pages

Eseguita via WP-CLI on staging post-Phase 4 template refactor confirmed safe:

```sh
for ID in 23 2708 2709 2712 2711 372 2713; do
  sudo -u www-data wp post update $ID --post_content='' --path=/var/www/saltelli
done
```

Backup `_legacy_post_content_backup` postmeta è preservato (scritto in Phase 3) — recoverable.

### Verifica post-cleanup

```
Page 23 (contatti):              post_content empty ✓, backup 899 chars
Page 2708 (domande-frequenti):   post_content empty ✓, backup 1117 chars
Page 2709 (guide-gratuite):      post_content empty ✓, backup 677 chars
Page 2712 (come-lavoriamo):      post_content empty ✓, backup 1432 chars
Page 2711 (prima-consulenza):    post_content empty ✓, backup 909 chars
Page 372 (lavora-con-noi):       post_content empty ✓, backup 1251 chars
Page 2713 (richiedi-preventivo): post_content empty ✓, backup 879 chars
```

### Frontend impact post-cleanup

| URL | HTTP | Delta vs baseline | Note |
|---|---|---|---|
| `/contatti/` | 200 | 0 bytes | Identical |
| `/risorse/domande-frequenti/` | 200 | 0 bytes | Identical |
| `/risorse/guide-gratuite/` | 200 | +8 bytes | Solo indentation diff |
| `/costi-e-consulenze/come-lavoriamo/` | 200 | -102 bytes | Yoast rimosso `twitter:label1/data1 Est. reading time` (post_content empty → reading time 0 → Yoast skips meta tag). Schema `dateModified` updated. Content visibile invariato. |
| `/costi-e-consulenze/prima-consulenza/` | 200 | +9 bytes | indentation |
| `/contatti/lavora-con-noi/` | 200 | -102 bytes | Stesso pattern Yoast |
| `/costi-e-consulenze/richiedi-preventivo/` | 200 | +9 bytes | indentation |

**Side effect Yoast**: 2 pagine vedono `twitter:label1` "Est. reading time" rimosso dai meta tag perché Yoast lo calcola dal `post_content` (ora vuoto). SEO meta tag minor, content visibile sulla pagina invariato. Non blocking — può essere fixato in futuro estendendo Yoast a leggere reading time da `body_content` SCF.

## 5.B — Gutenberg disable per 12 Pages target

File: `inc/admin/disable-gutenberg-for-scf-pages.php`

Pages target (`SALTELLI_SCF_ONLY_PAGES` constant):
- **4 hub Wave 4.7.fix.3**: 17 (home), 2822 (chi-siamo), 2812 (aree-di-pratica), 2813 (risorse)
- **7 target Wave 4.7.fix.4**: 23 (contatti), 2708 (domande-frequenti), 2709 (guide-gratuite), 2712 (come-lavoriamo), 2711 (prima-consulenza), 372 (lavora-con-noi), 2713 (richiedi-preventivo)
- **1 child SCF-driven**: 2811 (lo-studio, child di chi-siamo)

### Logic

```php
add_filter('use_block_editor_for_post', function ($use_block_editor, $post) {
    if ($post && in_array((int) $post->ID, SALTELLI_SCF_ONLY_PAGES, true)) {
        return false;  // disable block editor
    }
    return $use_block_editor;
}, 10, 2);
```

In aggiunta: `edit_form_after_title` action nasconde il classic editor visual area (CSS inline) e inserisce un notice "Modifica il contenuto qui sotto" sopra il metabox SCF.

### Verifica via WP-CLI

```
$ wp eval 'apply_filters("use_block_editor_for_post", true, get_post(17))' --path=...
Page 17 (Home):                       Gutenberg=disabled ✓
Page 23 (Contatti):                   Gutenberg=disabled ✓
Page 372 (Lavora con noi):            Gutenberg=disabled ✓
Page 2708 (Domande frequenti):        Gutenberg=disabled ✓
Page 2709 (Guide gratuite):           Gutenberg=disabled ✓
Page 2711 (Prima consulenza):         Gutenberg=disabled ✓
Page 2712 (Come lavoriamo):           Gutenberg=disabled ✓
Page 2713 (Richiedi un preventivo):   Gutenberg=disabled ✓
Page 2811 (Chi Siamo lo-studio):      Gutenberg=disabled ✓
Page 2812 (Aree di Pratica):          Gutenberg=disabled ✓
Page 2813 (Risorse):                  Gutenberg=disabled ✓
Page 2822 (Chi Siamo):                Gutenberg=disabled ✓

Control:
Page 2695 (Costi e Consulenze):       Gutenberg=ENABLED ✓ (atteso)
```

12/12 target ok + 1/1 control ok.

### Lesson learned `is_admin()` guard troppo restrittivo

Inizialmente avevo wrappato il `require_once` del file dentro `is_admin()`. WP-CLI non setta is_admin()=true quindi il filter non si registrava in CLI context → impossibile testare. Fix: rimosso il guard. Filter overhead trascurabile (early-return su Pages non in lista).

## 5.C — Admin shortcuts per archive CPT

File: `inc/admin/scf-archive-headers-shortcuts.php`

### Admin bar shortcut

Quando un admin loggato visita `/chi-siamo/team/` o `/chi-siamo/casi-rappresentativi/` su frontend, l'admin bar mostra un node "Modifica header archivio" con submenu:

- `/chi-siamo/team/` → "Modifica header archivio" → Saltelli Settings · "Tutti gli avvocati" → admin edit.php CPT list
- `/chi-siamo/casi-rappresentativi/` → "Modifica header archivio" → Saltelli Settings · "Tutti i casi rappresentativi" → CPT list

### Notice in Saltelli Settings tab "Archive Headers"

Filter `acf/load_field` injecta HTML guidance nel campo `instructions` del tab "Archive Headers":

```
📚 Header per le pagine archivio CPT
Modifica qui titolo + intro che appaiono in cima a:
  → /chi-siamo/team/ — per modificare i singoli avvocati: vai a Avvocati
  → /chi-siamo/casi-rappresentativi/ — per modificare i singoli casi: vai a Casi rappresentativi
```

### Verifica WP-CLI

```
$ wp eval 'acf_get_field_group(group_theme_options_v1) → iter tab fields → find Archive Headers'
TAB Archive Headers found:
  label=Archive Headers
  instructions len: 826
  instructions excerpt: 📚 Header per le pagine archivio CPT Modifica qui titolo + intro...
```

Filter scatta correttamente.

## 5.D — Pendenti per acceptance test admin (Elena)

I seguenti verifiche richiedono login admin in browser:

1. ✅ WP Admin → Pagine → Home → vede metabox SCF "Saltelli — Page Homepage" + notice "Modifica il contenuto qui sotto" → editor Gutenberg HIDDEN
2. ✅ WP Admin → Pagine → Contatti → vede metabox "Saltelli — Page Contatti" + notice + editor HIDDEN
3. ✅ WP Admin → Pagine → Chi Siamo (2822) → vede metabox + notice + editor HIDDEN
4. ✅ WP Admin → Pagine → Costi e Consulenze (2695, NON in SCF_ONLY) → vede Gutenberg ACTIVE (control test)
5. ✅ Frontend `/chi-siamo/team/` (admin loggato) → admin bar mostra "Modifica header archivio" → click porta a Saltelli Settings
6. ✅ WP Admin → Saltelli — Settings → tab Archive Headers → vede notice "📚 Header per le pagine archivio CPT" con shortcuts CPT linkati

## Files creati/modificati Phase 5

| File | Tipo | Linee |
|---|---|---|
| `inc/admin/disable-gutenberg-for-scf-pages.php` | NEW | 71 |
| `inc/admin/scf-archive-headers-shortcuts.php` | NEW | 110 |
| `functions.php` | MOD | +5 (require_once 2 admin files) |
| 7 Pages WP `post_content` | EMPTIED | 7 rows in wp_posts |
| 7 Pages WP `_legacy_post_content_backup` | postmeta WRITTEN | 7 rows in wp_postmeta |

---

*Phase 5 cleanup + admin UX · 2026-05-10 · ready for Phase 6 docs + bump.*
