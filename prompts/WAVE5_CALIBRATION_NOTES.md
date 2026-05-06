# 🔧 WAVE 5 — CALIBRATION NOTES (read FIRST before the v1.1 prompt)

> **Audience**: Claude Code agent dedicato Wave 5.
> **Funzione**: questo file calibra 6 punti del prompt `PROMPT_AGENT_v1.1_WAVE5_IA_REFACTOR.md` rispetto alla realtà del codice MVP. **Le indicazioni qui hanno la precedenza** sulle assunzioni del prompt v1.1 ovunque ci sia conflitto.
> **Origine**: lettura puntuale dei file MVP da parte dell'orchestratore (sere 2026-05-06): `inc/cpt-competenza.php`, `inc/cpt-avvocato.php`, `inc/cpt-recovery.php`, `inc/seo/legacy-redirects.php`, `page.php`, `inc/helpers.php`.

---

## 🎯 Cosa fare con questo file

1. Leggi prima `CLAUDE.md` (single source of truth).
2. **Leggi questo file** per primo, prima del prompt v1.1.
3. Tieni questo file a portata durante l'esecuzione. Quando il prompt v1.1 dice "modifica X" e questo file dice "ATTENZIONE su X, in realtà la struttura è Y", **applica Y, non X**.
4. Il prompt v1.1 è 1100+ righe — tutto quello che NON è citato in questo file di calibrazione è da considerare valido così com'è.

---

## 📍 CAL-01 — Slug effettivi delle 19 competenze nel CPT MVP

**Discrepanza**: il prompt v1.1 Phase 3.6.a usa slug semplificati (es. `tributario`, `cartelle-esattoriali`, `bancario`) che NON corrispondono agli slug effettivi del CPT `competenza` nel database del MVP.

**Realtà** (dedotta da `inc/seo/legacy-redirects.php` riga 18-37, dove ogni URL legacy mappa a `/competenze/{slug-reale}/`):

| Cluster | Titolo area | **Slug REALE nel CPT** | Note |
|---|---|---|---|
| privati | Tributario | `diritto-tributario` | NON `tributario` |
| privati | Cartelle esattoriali | `cartelle-esattoriali-e-multe` | NON `cartelle-esattoriali` |
| privati | Diritto del lavoro | `diritto-del-lavoro` | ✅ |
| privati | Diritto di famiglia | `diritto-di-famiglia` | NB: NO "lgbtq" nello slug — il LGBTQ+ è in title/contenuto |
| privati | Responsabilità medica | `responsabilita-medica` | ✅ |
| privati | Bancario | `diritto-bancario` | NON `bancario` |
| privati | Condominiale e immobiliare | `diritto-condominiale` | NON `condominiale-immobiliare` (slug semplificato) |
| privati | Immigrazione | `diritto-dellimmigrazione` | NON `immigrazione` (apostrofo elision: dell'+immigrazione) |
| privati | Penale | `diritto-penale` | NON `penale` |
| privati | Previdenziale | `diritto-previdenziale` | NON `previdenziale` |
| privati | Successioni | `diritto-delle-successioni` | NON `successioni` |
| privati | Risarcimento Danni | `risarcimento-danni` | ✅ |
| imprese | Recupero crediti | `recupero-crediti` | ✅ |
| imprese | Domiciliazione impresa | `domiciliazione-dimpresa` | NON `domiciliazione-impresa` (apostrofo elision: d'+impresa) |
| contenzioso-amministrativo | Diritto amministrativo | `diritto-amministrativo` | ✅ |

**Action obbligatoria**: in **Phase 1.5 NUOVA** (prima di Phase 2), esegui discovery slug effettivi:

```bash
# Phase 1.5 — Discovery slug effettivi competenze
wp post list --post_type=competenza --post_status=publish \
  --format=csv --fields=ID,post_name,post_title \
  > .claude/knowledge/audits/wave5/slug-discovery.csv

# Verifica conteggio (atteso: 19 competenze)
wc -l .claude/knowledge/audits/wave5/slug-discovery.csv

# Mostra a video per audit visivo
cat .claude/knowledge/audits/wave5/slug-discovery.csv
```

Confronta l'output con la tabella sopra. Se trovi slug ulteriormente diversi (es. la repo è cambiata dopo che ho letto i file), **aggiorna i loop di Phase 3.6.a usando gli slug effettivi**, non quelli del prompt v1.1.

**Loop Phase 3.6.a corretto** (sostituisci quello del prompt v1.1):

```bash
# Privati: 12 aree (con slug REALI dal CPT)
declare -a PRIVATI_SLUGS=(
  "diritto-tributario"
  "cartelle-esattoriali-e-multe"
  "diritto-del-lavoro"
  "diritto-di-famiglia"
  "responsabilita-medica"
  "diritto-bancario"
  "diritto-condominiale"
  "diritto-dellimmigrazione"
  "diritto-penale"
  "diritto-previdenziale"
  "diritto-delle-successioni"
  "risarcimento-danni"
)
for slug in "${PRIVATI_SLUGS[@]}"; do
    POST_ID=$(wp post list --post_type=competenza --name="$slug" --field=ID --posts_per_page=1)
    if [ -z "$POST_ID" ]; then
        echo "⚠️  SLUG NON TROVATO: $slug — annota in blockers.md, NON fermarti"
        echo "$slug" >> .claude/knowledge/audits/wave5/missing-slugs.txt
        continue
    fi
    wp post term set "$POST_ID" tipo-area privati
    echo "✅ $slug → privati (post_id=$POST_ID)"
done

# Imprese: 2 aree
for slug in "recupero-crediti" "domiciliazione-dimpresa"; do
    POST_ID=$(wp post list --post_type=competenza --name="$slug" --field=ID --posts_per_page=1)
    if [ -z "$POST_ID" ]; then
        echo "⚠️  SLUG NON TROVATO: $slug" >> .claude/knowledge/audits/wave5/missing-slugs.txt
        continue
    fi
    wp post term set "$POST_ID" tipo-area imprese
    echo "✅ $slug → imprese (post_id=$POST_ID)"
done

# Contenzioso amministrativo: 1 area
POST_ID=$(wp post list --post_type=competenza --name="diritto-amministrativo" --field=ID --posts_per_page=1)
wp post term set "$POST_ID" tipo-area contenzioso-amministrativo
echo "✅ diritto-amministrativo → contenzioso-amministrativo"
```

---

## 📍 CAL-02 — Le 4 PENDING DELETE potrebbero NON esistere come CPT MVP

**Discrepanza**: il prompt v1.1 Phase 3.6.b assume che le 4 aree PENDING DELETE (Assicurazioni, Resp. civile, Consulenze online, Diritto commerciale) esistano come CPT `competenza` nel DB del MVP, da eliminare con `wp post delete --force`.

**Realtà**: il file `inc/seo/legacy-redirects.php` MVP-existing (righe 19-44) elenca i mapping da URL legacy Elementor a CPT competenza. **Le 4 aree PENDING NON sono in quella lista**. Inoltre, "Pages orfane senza CPT corrispondente → archive competenze" cita esplicitamente `/diritto-societario/`, `/contrattualistica/`, `/aste-immobiliari/`, `/servizi-legali/` come "page WP del legacy senza CPT MVP".

**Conclusione probabile**: le 4 PENDING DELETE **non sono mai state migrate come CPT** nel MVP. Erano nel BRIEF originale ma Wave 2 (Content Migration) le ha skippate.

**Action**: in Phase 3.6.b, sostituisci il loop di delete con un loop **idempotente** che gestisce sia il caso "esistono" sia il caso "non esistono":

```bash
# Phase 3.6.b — DELETE 4 PENDING (idempotente, no fail su slug mancante)
declare -a DELETE_SLUGS=(
  "assicurazioni"
  "responsabilita-civile"
  "consulenze-online"
  "diritto-commerciale"
)
for slug in "${DELETE_SLUGS[@]}"; do
    POST_ID=$(wp post list --post_type=competenza --name="$slug" --field=ID --posts_per_page=1)
    if [ -z "$POST_ID" ]; then
        echo "ℹ️  $slug: già non presente nel DB MVP (skip — atteso secondo CAL-02)"
        echo "$slug → skip (non in CPT MVP)" >> .claude/knowledge/audits/wave5/delete-log.txt
        continue
    fi
    OLD_URL=$(wp post get $POST_ID --field=url 2>/dev/null || echo "—")
    echo "🗑  Eliminando $slug (post_id=$POST_ID, old_url=$OLD_URL)"
    wp post delete $POST_ID --force
    echo "$slug → deleted (was post_id=$POST_ID, old_url=$OLD_URL)" >> .claude/knowledge/audits/wave5/delete-log.txt
done

cat .claude/knowledge/audits/wave5/delete-log.txt
```

I redirect 301 in Phase 6 per i 4 slug LEGACY ELEMENTOR (non MVP CPT) restano comunque utili: backlink esterni storici al sito Elementor potrebbero ancora puntare lì. Phase 6 mantiene i 4 redirect dichiarati nel prompt v1.1 verso `/aree-di-pratica/` archive, indipendentemente dall'esistenza CPT.

---

## 📍 CAL-03 — Aste immobiliari + Infortunistica stradale già presenti come URL legacy

**Discrepanza**: il prompt v1.1 Phase 3.6.c crea `infortunistica-stradale` e `aste-immobiliari` come 2 NUOVE competenze. Questo è corretto **per il CPT MVP**.

**Realtà aggiuntiva**: il file `legacy-redirects.php` MVP-existing già ha redirect dal sito legacy Elementor:
- `/infortunistica-stradale/` → `/competenze/risarcimento-danni/` (riga 32)
- `/infortunistica-stradale-italia/` → `/competenze/risarcimento-danni/` (riga 33)
- `/aste-immobiliari/` → `/competenze/` archive (riga 47)

Cliente nelle decisioni del 2026-05-06 sere ha specificato che Infortunistica stradale resta **autonoma** (NO MERGE con risarcimento) e Aste immobiliari diventa una pratica autonoma a sé.

**Action**: oltre a creare i 2 NUOVI CPT (Phase 3.6.c invariata), aggiorna **anche `legacy-redirects.php` esistente** in Phase 6 per puntare ai nuovi URL specifici (non più → `/competenze/risarcimento-danni/` o → `/competenze/` archive).

Edit puntuale al file `inc/seo/legacy-redirects.php`, dentro la `saltelli_legacy_redirect_map()` esistente:

```php
// PRIMA (legacy-redirects.php attuale):
'/infortunistica-stradale/'                 => '/competenze/risarcimento-danni/',
'/infortunistica-stradale-italia/'          => '/competenze/risarcimento-danni/',
// ...
'/aste-immobiliari/'                        => '/competenze/',

// DOPO (Wave 5 calibrazione):
'/infortunistica-stradale/'                 => '/aree-di-pratica/privati/infortunistica-stradale/',
'/infortunistica-stradale-italia/'          => '/aree-di-pratica/privati/infortunistica-stradale/',
// ...
'/aste-immobiliari/'                        => '/aree-di-pratica/privati/aste-immobiliari/',
```

Anche tutti gli **altri redirect legacy esistenti** in `saltelli_legacy_redirect_map()` (righe 18-44) devono essere aggiornati al nuovo schema URL. Esempio:

```php
// PRIMA
'/recupero-crediti/'   => '/competenze/recupero-crediti/',
// DOPO Wave 5
'/recupero-crediti/'   => '/aree-di-pratica/imprese/recupero-crediti/',
```

Itera il pattern per tutte le 19 righe della map, applicando lo schema `/aree-di-pratica/{cluster}/{slug-reale}/` con cluster preso da `cluster-mapping-17-areas.csv` e slug-reale dalla tabella CAL-01.

---

## 📍 CAL-04 — `legacy-redirects.php` usa `init priority 1`, NON `template_redirect`

**Discrepanza**: il prompt v1.1 Phase 6 propone di usare `add_action('template_redirect', ..., 5)` per il nuovo array `$mvp_to_audit_redirects`.

**Realtà**: il file `legacy-redirects.php` esistente usa `add_action('init', 'saltelli_legacy_redirect', 1)` con check `is_admin/DOING_AJAX/WP_CLI`. Pattern già consolidato da Wave 0 (commit `v0.13.0 IA Unification`) e funzionante.

**Action**: in Phase 6 **NON aggiungere un secondo hook con priority diversa**. Estendi la funzione `saltelli_legacy_redirect()` esistente con la nuova map `$mvp_to_audit`, mantenendo lo stesso hook `init priority 1`.

Pattern corretto (sostituisce il blocco `add_action('template_redirect', ...)` del prompt v1.1):

```php
// Estende la funzione esistente saltelli_legacy_redirect_map() per includere
// la nuova mappa MVP → audit-aligned. NON crea un nuovo hook.

if (!function_exists('saltelli_mvp_to_audit_redirect_map')) :
function saltelli_mvp_to_audit_redirect_map() {
    return [
        // Sezioni hub rinomina (statici)
        '/lo-studio/'             => '/chi-siamo/lo-studio/',
        '/avvocati/'              => '/chi-siamo/team/',
        '/competenze/'            => '/aree-di-pratica/',
        '/casi/'                  => '/chi-siamo/risultati/',
        '/blog/'                  => '/risorse/blog/',
        '/faq/'                   => '/risorse/domande-frequenti/',
        '/glossario-legale/'      => '/risorse/glossario-legale/',
        '/guide-gratuite/'        => '/risorse/guide-gratuite/',
        '/come-lavoriamo/'        => '/costi-e-consulenze/come-lavoriamo/',
        '/prima-consulenza/'      => '/costi-e-consulenze/prima-consulenza/',
        '/richiedi-preventivo/'   => '/costi-e-consulenze/richiedi-preventivo/',
        '/lavora-con-noi/'        => '/contatti/lavora-con-noi/',
        '/costi/'                 => '/costi-e-consulenze/',
        '/tipo-area/privati/'     => '/aree-di-pratica/privati/',
        '/tipo-area/imprese/'     => '/aree-di-pratica/imprese/',
        '/tipo-area/contenzioso-amministrativo/' => '/aree-di-pratica/contenzioso-amministrativo/',
        // 4 PENDING DELETE — backlink esterni storici → archive
        '/competenze/assicurazioni/'         => '/aree-di-pratica/',
        '/competenze/responsabilita-civile/' => '/aree-di-pratica/',
        '/competenze/consulenze-online/'     => '/aree-di-pratica/',
        '/competenze/diritto-commerciale/'   => '/aree-di-pratica/',
    ];
}
endif;

// Modifica saltelli_legacy_redirect() esistente per consultare anche $mvp_to_audit
function saltelli_legacy_redirect() {
    if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) return;
    if (defined('WP_CLI') && WP_CLI) return;

    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    $path = (string) parse_url($request_uri, PHP_URL_PATH);

    if ($path === '' || $path === '/') return;
    if (substr($path, -1) !== '/') {
        $path .= '/';
    }

    // Step 1 — Legacy Elementor → audit-aligned (mappa esistente, AGGIORNATA in CAL-03)
    $legacy_map = saltelli_legacy_redirect_map();
    if (isset($legacy_map[$path])) {
        wp_safe_redirect(home_url($legacy_map[$path]), 301);
        exit;
    }

    // Step 2 — MVP corrente → audit-aligned (NUOVA mappa Wave 5)
    $mvp_map = saltelli_mvp_to_audit_redirect_map();
    if (isset($mvp_map[$path])) {
        wp_safe_redirect(home_url($mvp_map[$path]), 301);
        exit;
    }

    // Step 3 — Pattern dynamic per /competenze/{slug}/ (regex)
    if (preg_match('#^/competenze/([^/]+)/?$#', $path, $matches)) {
        $slug = $matches[1];
        $post = get_page_by_path($slug, OBJECT, 'competenza');
        if ($post) {
            wp_safe_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }

    // Step 4 — Pattern dynamic per /avvocati/{slug}/, /blog/{slug}/, /category/{cat}/, /tag/{tag}/
    if (preg_match('#^/avvocati/([^/]+)/?$#', $path, $matches)) {
        wp_safe_redirect(home_url("/chi-siamo/team/{$matches[1]}/"), 301);
        exit;
    }
    if (preg_match('#^/blog/(.+)$#', $path, $matches)) {
        wp_safe_redirect(home_url("/risorse/blog/{$matches[1]}"), 301);
        exit;
    }
    if (preg_match('#^/(category|tag|author)/(.+)$#', $path, $matches)) {
        wp_safe_redirect(home_url("/risorse/blog/{$matches[1]}/{$matches[2]}"), 301);
        exit;
    }
}

// Hook esistente — NON aggiungere nuovo hook
add_action('init', 'saltelli_legacy_redirect', 1);
```

---

## 📍 CAL-05 — `page.php` router usa `is_page()` con slug, NON `$template_map` array

**Discrepanza**: il prompt v1.1 Phase 4.3 propone di sostituire la struttura di `page.php` con un array `$template_map` slug → template-part.

**Realtà**: il `page.php` esistente (~80 righe) usa una catena di `is_page('slug')` con if/elseif, già pulita e leggibile. Sostituirla con un array funziona ma è più invasivo del necessario, e crea risk di rompere il pattern dei case speciali (`/glossario-legale/` ha `include` invece di `get_template_part`, le 5 pagine info-shared sono raggruppate in `is_page([...array...])`).

**Action**: in Phase 4 mantieni la struttura `is_page()` esistente, **estendendola** con i nuovi case per le 4 pagine hub e applicando il rename slug della pagina "Lo Studio".

Pattern corretto:

```php
// page.php — modifiche minime, mantiene struttura is_page() esistente

while (have_posts()) :
    the_post();
    
    // CAL-05: la pagina "Lo Studio" rinomina slug da 'chi-siamo' a 'lo-studio' (Phase 5)
    // La nuova pagina hub "Chi Siamo" prende slug 'chi-siamo'
    $sl_lo_studio = is_page('lo-studio');     // ✏️ era: is_page('chi-siamo')
    $sl_casi      = is_page('casi');
    ?>
    <article <?php post_class('sl-page' . ($sl_lo_studio ? ' sl-lo-studio' : '') . ($sl_casi ? ' sl-casi-page' : '')); ?>>
        <?php
        if ($sl_lo_studio) {
            get_template_part('template-parts/page', 'lo-studio');     // ✏️ era: 'chi-siamo'
        } elseif ($sl_casi) {
            get_template_part('template-parts/page', 'casi');
        } elseif (is_page('contatti')) {
            get_template_part('template-parts/page', 'contatti');
        } elseif (is_page('glossario-legale')) {
            include SALTELLI_THEME_DIR . '/inc/wave3-glossario.php';
        } elseif (is_page('faq') || is_page('domande-frequenti')) {        // ✏️ aggiunto domande-frequenti slug post-Wave5
            get_template_part('template-parts/page', 'faq');
        } elseif (is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo'])) {
            get_template_part('template-parts/page', 'info-shared');
        } elseif (is_page('costi') || is_page('costi-e-consulenze')) {     // ✏️ aggiunto sub-hub costi-e-consulenze
            get_template_part('template-parts/page', 'costi');
        // ✏️ NUOVI Wave 5 — 4 hub pages
        } elseif (is_page('chi-siamo')) {
            get_template_part('template-parts/page', 'chi-siamo-hub');
        } elseif (is_page('aree-di-pratica')) {
            get_template_part('template-parts/page', 'aree-di-pratica-hub');
        } elseif (is_page('risorse')) {
            get_template_part('template-parts/page', 'risorse-hub');
        } elseif (is_page('costi-e-consulenze') && !is_page('costi')) {
            get_template_part('template-parts/page', 'costi-e-consulenze-hub');
        } else {
            // Default fallback (invariato)
            // ...
        }
        ?>
    </article>
```

I 4 nuovi template-part `template-parts/page-{chi-siamo-hub,aree-di-pratica-hub,risorse-hub,costi-e-consulenze-hub}.php` vanno creati come specificato nel prompt v1.1 Phase 4.2 (esempio markup `.sl-area` rows). Pattern markup corretto per i 4 nuovi hub.

**Rinomina pagina "Lo Studio"** (Phase 5.2):

```bash
# Trova post_id della pagina "Lo Studio" (slug attuale chi-siamo)
LO_STUDIO_ID=$(wp post list --post_type=page --name=chi-siamo --field=ID --posts_per_page=1)

# Verifica pre-rename
wp post get $LO_STUDIO_ID --field=post_title  # atteso: "Lo Studio" o simile

# Rinomina slug
wp post update $LO_STUDIO_ID --post_name=lo-studio

# Verifica post-rename
wp post get $LO_STUDIO_ID --field=post_name  # atteso: "lo-studio"
```

Quindi **rinomina anche il file template-part**:

```bash
mv wp-content/themes/saltelli/template-parts/page-chi-siamo.php \
   wp-content/themes/saltelli/template-parts/page-lo-studio.php
```

Dopo questi 2 step, la nuova pagina hub "Chi Siamo" può prendere lo slug `chi-siamo` senza clash:

```bash
wp post create --post_type=page --post_title="Chi Siamo" --post_name=chi-siamo --post_status=publish --porcelain
```

E in Phase 5 spostala come parent della pagina `lo-studio` (parent_id update):

```bash
CHI_SIAMO_HUB_ID=$(wp post list --post_type=page --name=chi-siamo --field=ID --posts_per_page=1)
wp post update $LO_STUDIO_ID --post_parent=$CHI_SIAMO_HUB_ID
# Risultato: /chi-siamo/lo-studio/ — coerente con sitemap firmata
```

---

## 📍 CAL-06 — `saltelli_option()` non esiste; usa `saltelli_studio_data()` o `get_field('...', 'option')`

**Discrepanza**: il prompt v1.1 Phase 6 e il `pattern-adaptation-map.md` per Wave 6 referenziano `saltelli_option('chiave')` come helper per leggere ACF Theme Options.

**Realtà**: in `inc/helpers.php` esistono:
- `saltelli_field($name, $post_id = null, $default = null)` — wrapper per `get_field` su singolo post
- `saltelli_studio_data()` — restituisce array con tutti i campi Studio Info (`$studio['phone']`, `$studio['email']`, ecc.)
- **NON esiste** `saltelli_option()`

**Action per Wave 5**: nessuno impatto diretto su Wave 5 (Phase 1-7 del prompt v1.1 non chiamano `saltelli_option`). Il prompt v1.1 menziona `saltelli_option` solo in cenni futuri Wave 6.

**Action per Wave 6**: quando arriverai a implementare il `pattern-adaptation-map.md` (Wave 6, prossima sessione), userai uno dei 3 pattern:

```php
// Pattern A — leggi direttamente da ACF Options Page
$signal = get_field('trust_signal_1_label', 'option');

// Pattern B — aggiungi un helper saltelli_option() in helpers.php (raccomandato)
function saltelli_option($name, $default = null) {
    $value = get_field($name, 'option');
    return $value !== '' && $value !== null && $value !== false ? $value : $default;
}

// Pattern C — usa saltelli_studio_data() per i dati Studio Info già aggregati
$studio = saltelli_studio_data();
$phone = $studio['phone'];
```

In Wave 6 raccomanderò Pattern B (aggiungi `saltelli_option` come wrapper coerente con `saltelli_field`). Per Wave 5 non è bloccante.

---

## ✅ Acceptance check post-calibrazione

Prima di iniziare Phase 1 del prompt v1.1, conferma a te stesso:

- [ ] Ho letto questo file di calibrazione (CAL-01 → CAL-06)
- [ ] Aggiungerò Phase 1.5 per discovery slug effettivi competenze (CAL-01)
- [ ] Sostituirò il loop di Phase 3.6.a con la versione che usa slug REALI (CAL-01)
- [ ] Sostituirò il loop di Phase 3.6.b con versione idempotente (CAL-02)
- [ ] In Phase 6, aggiornerò ANCHE i redirect esistenti in `legacy-redirects.php` per Aste immobiliari + Infortunistica + tutti gli altri 19 redirect legacy → audit-aligned (CAL-03)
- [ ] In Phase 6, NON userò `template_redirect` priority 5; estenderò la funzione esistente `saltelli_legacy_redirect()` con `init priority 1` (CAL-04)
- [ ] In Phase 4-5, manterrò la struttura `is_page()` di `page.php`, estendendola con 4 nuovi case + rinomina slug `chi-siamo` → `lo-studio` (CAL-05)
- [ ] Wave 5 NON tocca `saltelli_option` (CAL-06 non bloccante per Wave 5)

---

## 🔗 Riferimenti

- `prompts/PROMPT_AGENT_v1.1_WAVE5_IA_REFACTOR.md` — il prompt principale (~1100 righe)
- `prompts/WAVE5_RUNBOOK.md` — cheat sheet operativo
- `prompts/cluster-mapping-17-areas.csv` — deliverable cliente-firmato (DEC-021)
- `CLAUDE.md` — single source of truth
- `inc/seo/legacy-redirects.php` — file da estendere (CAL-03 + CAL-04)
- `page.php` — file da estendere (CAL-05)
- `inc/cpt-competenza.php` + `inc/cpt-avvocato.php` + `inc/cpt-recovery.php` — file da modificare (Phase 3 prompt v1.1)
