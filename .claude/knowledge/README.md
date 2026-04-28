# `.claude/knowledge/` — Knowledge Base Saltelli WP

Questa è la knowledge base che Claude Code legge per il progetto **Studio Legale Saltelli — WordPress Custom Theme**.

## File principali

| File | Cosa contiene |
|---|---|
| `project-context.json` | **Source of truth.** Dati cliente, team, contatti, contratto, decisioni strategiche, blockers, requisiti GEO, design system. Letto da tutti gli agent. |
| `wordpress/` | Reference WordPress generica: coding standards, API, theme structure, troubleshooting |
| `database/` | Backup strategy, query templates riutilizzabili |
| `security/` | Hardening checklist, compliance, vulnerabilities reference |

## File archiviati (in `/.archive-template-residui/`)

I file con riferimenti a Torres, WPML, WooCommerce, booking system, e i template generici (`QUICKSTART.md`, `TROUBLESHOOTING.md`, `setup.sh`) sono stati spostati lì il 2026-04-28. Possono essere cancellati definitivamente quando confermato che nulla di utile va recuperato.

## Note importanti per Claude Code

I file `wordpress/`, `database/`, `security/` arrivano dal template `wordpress-claude-template/` di Adsolut e contengono ancora **placeholder come `{{SITE_NAME}}`, `{{WP_PATH}}`, `jtlb_*` (prefix di un cliente precedente)**. Trattare questi file come **reference generica WordPress**, non come fonte di verità su Saltelli. La fonte di verità è `project-context.json`.

Quando il lead agent inizializza il progetto, deve:

1. Leggere `project-context.json` per i dati Saltelli
2. Leggere `../../BRIEF_Saltelli_WordPress.md` e `../PROMPT_LEAD_AGENT.md` per scope e direttive
3. Leggere `../../CLAUDE.md` per le hard constraints
4. Leggere `../../geo-assets/schema/README.md` per l'integrazione schema markup
5. Usare `wordpress/`, `database/`, `security/` solo come reference quando serve un pattern WordPress generico

## Cosa NON è qui (e per quale motivo)

| Cartella rimossa | Motivo |
|---|---|
| `historical/` | Erano template vuoti di un altro cliente |
| `woocommerce/` | Studio Saltelli non è ecommerce |
| `wpml/` | Saltelli per ora è monolingua. Da rivedere in Fase 5 se si decide di internazionalizzare l'area immigrazione (EN/FR) |

## Aggiornamento

Ogni volta che si chiude una milestone (es. design approvato, dump cliente importato, theme scaffold creato), aggiornare `project-context.json` aggiornando il campo `current_phase` e `last_updated`.

---
*Ultimo aggiornamento: 2026-04-28*
