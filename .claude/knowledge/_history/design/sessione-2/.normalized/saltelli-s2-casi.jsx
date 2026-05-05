/* global React, S2Header, S2Footer */
/* Sessione 2 · /casi/ — CASES REPRESENTATIVE
   Spec key: hero + lede · filtro tab tipo-area · lista tipografica
   id mono | desc italic | esito bronze · pull-quote case rilevante ·
   8-10 casi · CTA finale prenota
   Schema JSON-LD: ItemList di LegalService results */

function S2Casi() {
  const [filter, setFilter] = React.useState("Tutti");

  const casi = [
    { id: "vs. AGE Riscossione · 2024", cat: "Imprese", outcome: "€240.000", lbl: "Annullamento", desc: "Annullamento integrale di cartella esattoriale a carico di società in liquidazione, eccezione di prescrizione e vizio di notifica.", featured: true },
    { id: "Cassazione · 2024", cat: "Privati", outcome: "Vittoria", lbl: "Conferma", desc: "Conferma in Cassazione di sentenza favorevole in materia di licenziamento per giusta causa illegittimo." },
    { id: "Tribunale di Napoli · 2023", cat: "Privati", outcome: "Riconoscimento", lbl: "Storica", desc: "Primo riconoscimento in Campania di trascrizione integrale di atto di nascita di minore con due madri." },
    { id: "Corte d'Appello · 2023", cat: "Imprese", outcome: "−80%", lbl: "Riforma", desc: "Riforma di sentenza di primo grado in materia di accertamento sintetico, riduzione dell'80% del dovuto." },
    { id: "CTR Campania · 2022", cat: "Imprese", outcome: "€87.000", lbl: "Vittoria", desc: "Riconoscimento di credito IVA contestato dall'Agenzia delle Entrate per €87.000." },
    { id: "Tribunale di Napoli · 2023", cat: "Contenzioso", outcome: "Vittoria", lbl: "Risarcimento", desc: "Risarcimento del danno per condotta antisindacale di azienda metalmeccanica." },
    { id: "Tribunale Famiglia · 2024", cat: "Privati", outcome: "Affido", lbl: "Condiviso", desc: "Affidamento condiviso e mantenimento adeguato in separazione complessa con immobili in più province." },
    { id: "TAR Campania · 2023", cat: "Altri", outcome: "Annullamento", lbl: "Atto P.A.", desc: "Annullamento di provvedimento amministrativo in materia di edilizia, per difetto di motivazione." },
    { id: "Tribunale di Napoli · 2022", cat: "Contenzioso", outcome: "€156.000", lbl: "Recupero", desc: "Recupero di credito commerciale per società del settore tessile, con esecuzione mobiliare immediata." },
    { id: "Cassazione · 2022", cat: "Imprese", outcome: "Vittoria", lbl: "Soc.", desc: "Conferma di sentenza favorevole in materia di responsabilità solidale di amministratori di S.r.l." },
  ];

  const filters = ["Tutti", "Privati", "Imprese", "Contenzioso", "Altri"];
  const visible = filter === "Tutti" ? casi : casi.filter(c => c.cat === filter);

  return (
    <div className="sl-root">
      <S2Header />

      {/* HERO */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "120px clamp(24px, 5vw, 96px) 80px",
        display: "grid", gridTemplateColumns: "5fr 7fr", gap: 64, alignItems: "end",
      }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 48 }}>§ Risultati · Casi rappresentativi</div>
          <h1 style={{
            fontSize: "clamp(64px, 8vw, 132px)",
            lineHeight: 0.95, letterSpacing: "-0.035em", fontWeight: 400,
          }}>
            Casi<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>rappresentativi.</em>
          </h1>
        </div>
        <div style={{ paddingBottom: 16 }}>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 24, lineHeight: 1.5, color: "var(--text)", maxWidth: "44ch", marginBottom: 32,
          }}>
            Una selezione di vittorie. Identificativi anonimizzati per riservatezza, documentati e verificabili in studio.
          </p>
          <div className="sl-mono">10 casi · 2022 → 2024 · aggiornato Apr 2026</div>
        </div>
      </section>

      {/* PULL-QUOTE — case rilevante */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "0 clamp(24px, 5vw, 96px) 96px" }}>
        <div style={{
          background: "var(--surface)",
          padding: "80px 64px",
          display: "grid", gridTemplateColumns: "1fr 2fr", gap: 64, alignItems: "center",
          borderTop: "1px solid var(--accent)", borderBottom: "1px solid var(--accent)",
        }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 24, color: "var(--accent)" }}>Caso simbolo · 2024</div>
            <div style={{
              fontFamily: "var(--font-display)", fontStyle: "italic",
              fontSize: "clamp(80px, 9vw, 140px)",
              color: "var(--accent)", lineHeight: 0.95, letterSpacing: "-0.03em",
            }}>
              €240k
            </div>
            <div className="sl-mono" style={{ marginTop: 16 }}>Annullamento integrale</div>
          </div>
          <blockquote style={{ margin: 0 }}>
            <p style={{
              fontFamily: "var(--font-display)", fontStyle: "italic",
              fontSize: "clamp(24px, 2.5vw, 32px)", lineHeight: 1.4,
              color: "var(--primary)", letterSpacing: "-0.01em",
              margin: 0, maxWidth: "44ch",
            }}>
              "L'annullamento integrale della cartella, fondato su prescrizione e vizio di notifica, ha permesso alla società di continuare l'attività."
            </p>
            <footer style={{ marginTop: 24 }}>
              <div className="sl-mono">Vs. AGE Riscossione · CTP Napoli · 2024</div>
            </footer>
          </blockquote>
        </div>
      </section>

      {/* FILTRO */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "0 clamp(24px, 5vw, 96px) 48px" }}>
        <div style={{ display: "flex", gap: 32, paddingBottom: 24, borderBottom: "1px solid var(--border)", flexWrap: "wrap" }}>
          {filters.map(f => (
            <button key={f} onClick={() => setFilter(f)} style={{
              background: "none", border: 0, cursor: "pointer", padding: "4px 0",
              color: filter === f ? "var(--primary)" : "var(--text-muted)",
              borderBottom: filter === f ? "1px solid var(--accent)" : "1px solid transparent",
              fontFamily: "var(--font-mono)", fontSize: 12,
              letterSpacing: "0.08em", textTransform: "uppercase",
            }}>{f}{f === "Tutti" ? ` (${casi.length})` : ` (${casi.filter(c => c.cat === f).length})`}</button>
          ))}
        </div>
      </section>

      {/* LISTA CASI */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "0 clamp(24px, 5vw, 96px) 128px" }}>
        <div>
          {visible.map((c, i) => (
            <CaseRow key={i} c={c} />
          ))}
        </div>

        {/* paginazione futura */}
        <div style={{ marginTop: 64, paddingTop: 32, borderTop: "1px solid var(--border)", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
          <div className="sl-mono">Pagina 1 / 1 · {visible.length} casi visibili</div>
          <button className="sl-btn" style={{ opacity: 0.4, cursor: "not-allowed" }}>
            Carica altri casi
            <span className="arrow">→</span>
          </button>
        </div>
      </section>

      {/* CTA */}
      <section style={{ background: "var(--surface)", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ Prossimo caso</div>
          <div>
            <h2 style={{ fontSize: "clamp(56px, 6.5vw, 96px)", letterSpacing: "-0.025em", lineHeight: 0.98, marginBottom: 32 }}>
              Vorresti vincere<br/>
              <em style={{ fontStyle: "italic", color: "var(--accent)" }}>il tuo?</em>
            </h2>
            <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 22, color: "var(--text)", lineHeight: 1.5, maxWidth: "44ch", marginBottom: 48 }}>
              Il primo incontro è gratuito. Diciamo la verità anche quando significa sconsigliare un'azione legale.
            </p>
            <a href="/contatti/" className="sl-btn sl-btn--primary">
              Prenota gratuita
              <span className="arrow">→</span>
            </a>
          </div>
        </div>
      </section>

      <S2Footer />
    </div>
  );
}

function CaseRow({ c }) {
  const [hover, setHover] = React.useState(false);
  return (
    <div
      onMouseEnter={() => setHover(true)}
      onMouseLeave={() => setHover(false)}
      style={{
        display: "grid", gridTemplateColumns: "240px 1fr 200px",
        gap: 48, padding: "32px 0",
        borderBottom: hover ? "1px solid var(--accent)" : "1px solid var(--border)",
        alignItems: "baseline",
        transform: hover ? "translateX(8px)" : "translateX(0)",
        transition: "transform 200ms var(--ease-editorial), border-color 300ms var(--ease-editorial)",
        cursor: "pointer",
      }}>
      <div>
        <div className="sl-mono" style={{ marginBottom: 6 }}>{c.id}</div>
        <div className="sl-mono" style={{ color: hover ? "var(--accent)" : "var(--text-muted)", transition: "color 200ms" }}>
          {c.cat} →
        </div>
      </div>
      <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 20, color: "var(--primary)", lineHeight: 1.45, letterSpacing: "-0.005em" }}>
        {c.desc}
      </p>
      <div style={{ textAlign: "right" }}>
        <div style={{
          fontFamily: "var(--font-display)", fontSize: 28,
          color: "var(--accent)", letterSpacing: "-0.015em", lineHeight: 1, marginBottom: 4,
        }}>
          {c.outcome}
        </div>
        <div className="sl-mono">{c.lbl}</div>
      </div>
    </div>
  );
}

window.S2Casi = S2Casi;
