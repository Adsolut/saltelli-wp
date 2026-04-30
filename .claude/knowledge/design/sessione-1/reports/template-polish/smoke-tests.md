# Smoke Test 20 URL — Step E v2

**Data:** 2026-04-30
**Pre-fix base:** v0.7.0 + M1+M2+M3 mobile fix applicati

---

## Risultati

### Homepage & Root
| URL | HTTP | Bytes | H1 | Schema | sl-* hits | Status |
|---|:---:|---:|:---:|:---:|:---:|:---:|
| `/` | 200 | 84 996 | 1 | 2 | 361 | ✅ |

### 4 single-avvocato + archive
| URL | HTTP | Bytes | H1 | Schema | sl-* hits | Status |
|---|:---:|---:|:---:|:---:|:---:|:---:|
| `/avvocati/emiliano-saltelli/` | 200 | 60 613 | 1 | 3 | 106 | ✅ (foto reale) |
| `/avvocati/fabiana-saltelli/` | 200 | 58 095 | 1 | 3 | 101 | ✅ (placeholder) |
| `/avvocati/antonia-battista/` | 200 | 58 587 | 1 | 3 | 102 | ✅ (placeholder) |
| `/avvocati/stefano-gaetano-tedesco/` | 200 | 57 813 | 1 | 3 | 100 | ✅ (placeholder) |
| `/avvocati/` | 200 | 56 822 | 1 | 2 | 120 | ✅ |

### 5 single-competenza + archive
| URL | HTTP | Bytes | H1 | Schema | sl-* hits | Status |
|---|:---:|---:|:---:|:---:|:---:|:---:|
| `/competenze/diritto-tributario/` | 200 | 64 730 | 1 | 3 | 111 | ✅ (tier-1) |
| `/competenze/diritto-del-lavoro/` | 200 | 61 964 | 1 | 3 | 106 | ✅ (tier-1) |
| `/competenze/diritto-di-famiglia-lgbtq/` | 200 | 65 452 | 1 | 3 | 111 | ✅ (tier-1) |
| `/competenze/recupero-crediti/` | 200 | 61 408 | 1 | 3 | 90 | ✅ (tier-2) |
| `/competenze/responsabilita-medica/` | 200 | 59 311 | 1 | 3 | 90 | ✅ (tier-2) |
| `/competenze/` | 200 | 66 206 | 1 | 2 | 190 | ✅ |

### 4 taxonomy tipo-area
| URL | HTTP | Bytes | H1 | Schema | sl-* hits | Status |
|---|:---:|---:|:---:|:---:|:---:|:---:|
| `/tipo-area/privati/` | 200 | 55 904 | 1 | 2 | 116 | ✅ (fallback archive.php) |
| `/tipo-area/imprese/` | 200 | 53 184 | 1 | 2 | 86 | ✅ |
| `/tipo-area/contenzioso/` | 200 | 53 283 | 1 | 2 | 86 | ✅ |
| `/tipo-area/altri/` | 200 | 51 944 | 1 | 2 | 74 | ✅ |

### Pages
| URL | HTTP | Bytes | H1 | Schema | sl-* hits | Status |
|---|:---:|---:|:---:|:---:|:---:|:---:|
| `/chi-siamo/` | 200 | 58 604 | **2** | 2 | 62 | ❌ **2 H1** |
| `/contatti/` | 200 | 53 071 | **2** | 2 | 62 | ❌ **2 H1** |
| `/costi/` | 200 | 56 920 | 1 | 2 | 88 | ✅ |

### Blog
| URL | HTTP | Bytes | H1 | Schema | sl-* hits | Status |
|---|:---:|---:|:---:|:---:|:---:|:---:|
| `/intimazione-tari-annullata-.../` | 200 | 73 917 | 1 | 2 | 106 | ✅ |

### System
| URL | HTTP | Bytes | H1 | Schema | sl-* hits | Status |
|---|:---:|---:|:---:|:---:|:---:|:---:|
| `/non-esiste-404/` | **404** | 49 919 | 1 | 2 | 66 | ✅ (404 atteso) |
| `/?s=tributario` | 200 | 59 979 | 1 | 2 | 120 | ✅ |

---

## Score

**18/20 PASS · 2/20 FAIL** (entrambi `2 H1`)

### Issue trovati (per priorità)

**P0 — Hard rule violation:**
- `/chi-siamo/` e `/contatti/` hanno 2 `<h1>`. Il template `page.php` emette `.sl-page__title` H1 + `the_content()` ne emette un secondo dal post_content (editor ha incluso H1 "Chi siamo" e "Contattaci" nel body).
  → Fix in Task 3.G: script PHP demote H1 in post_content delle 2 pages a H2.

**P1 — Note minori (non blocker):**
- `/tipo-area/*/` usa fallback `archive.php` → da sostituire con `taxonomy-tipo-area.php` dedicato (Task 3.E).
- Tier-2 ha ~90 sl-* hits vs tier-1 ~110 (meno markup per treatment minimal — by design).
