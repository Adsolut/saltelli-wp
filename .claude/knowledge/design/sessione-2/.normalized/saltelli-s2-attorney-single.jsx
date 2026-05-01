/* global React, S2Header, S2Footer */
/* Sessione 2 · /avvocati/{slug}/ — SINGLE LAWYER PROFILE
   Spec key: hero ritratto 1:1 sx + nome dx · sticky CTA mono ·
   bio drop-cap ~250w · 5 spec tag · 6 aree · timeline formazione ·
   3 casi outcome bronze · CTA prenota
   Schema JSON-LD: Person + Attorney + LegalService */

function S2AttorneySingle({ withRealPhoto = false }) {
  const lawyer = {
    name: "Emiliano Saltelli",
    role: "Founding Partner · Tributarista",
    spec: ["Diritto tributario", "Cartelle esattoriali", "Contenzioso fiscale", "Accertamenti AGE", "Reati tributari"],
    aree: [
      { t: "Diritto tributario", h: "/competenze/diritto-tributario/" },
      { t: "Cartelle esattoriali", h: "#" },
      { t: "Contenzioso fiscale", h: "#" },
      { t: "Accertamenti sintetici", h: "#" },
      { t: "Penale dell'economia", h: "#" },
      { t: "Diritto societario", h: "#" },
    ],
    formazione: [
      { y: "1995", t: "Liceo Classico Umberto I", d: "Maturità classica · 60/60" },
      { y: "2000", t: "Università Federico II — Giurisprudenza", d: "110/110 lode · tesi in diritto tributario" },
      { y: "2003", t: "Abilitazione forense", d: "Corte d'Appello di Napoli" },
      { y: "2008", t: "Master in diritto tributario", d: "LUISS Guido Carli · Roma" },
      { y: "2014", t: "Cassazionista", d: "Iscrizione albo speciale Corti Superiori" },
    ],
    casi: [
      { id: "vs. AGE Riscossione · 2024", outcome: "Annullamento", desc: "Annullamento integrale di cartella per oltre 240.000 € a carico di società in liquidazione." },
      { id: "Cassazione · 2023", outcome: "Riforma", desc: "Riforma di accertamento sintetico, riduzione dell'80% del dovuto." },
      { id: "CTR Campania · 2022", outcome: "Vittoria", desc: "Riconoscimento di credito IVA contestato per €87.000." },
    ],
  };

  return (
    <div className="sl-root">
      <S2Header />

      {/* HERO — ritratto sx | nome dx */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "80px clamp(24px, 5vw, 96px) 64px",
        display: "grid", gridTemplateColumns: "1fr 1fr", gap: 64, alignItems: "stretch",
      }}>
        {/* Ritratto */}
        <div style={{
          aspectRatio: "1 / 1",
          background: withRealPhoto
            ? "linear-gradient(135deg, color-mix(in srgb, var(--text) 90%, transparent) 0%, var(--primary) 100%)"
            : "linear-gradient(135deg, color-mix(in srgb, var(--text-muted) 35%, var(--surface)) 0%, var(--text-muted) 100%)",
          border: "1px solid var(--border)",
          position: "relative", overflow: "hidden",
          filter: "grayscale(1) contrast(1.05)",
          transition: "filter 600ms var(--ease-editorial)",
        }}
        onMouseEnter={e => e.currentTarget.style.filter = "grayscale(0)"}
        onMouseLeave={e => e.currentTarget.style.filter = "grayscale(1) contrast(1.05)"}>
          {withRealPhoto ? (
            <div style={{ position: "absolute", inset: 0, display: "flex", alignItems: "center", justifyContent: "center" }}>
              <div style={{ textAlign: "center", color: "rgba(255,255,255,0.7)" }}>
                <div style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 64, marginBottom: 8 }}>ES</div>
                <div className="sl-mono" style={{ color: "rgba(255,255,255,0.55)" }}>Foto DSLR · 1:1 · reale</div>
              </div>
            </div>
          ) : (
            <div style={{ position: "absolute", inset: 0, display: "flex", flexDirection: "column", alignItems: "center", justifyContent: "center", gap: 16 }}>
              <div className="sl-mono" style={{ color: "rgba(255,255,255,0.85)", fontSize: 13 }}>Ritratto · 3:4</div>
              <div style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 80, color: "rgba(255,255,255,0.5)", lineHeight: 1 }}>
                ES
              </div>
              <div className="sl-mono" style={{ color: "rgba(255,255,255,0.55)", fontSize: 10 }}>Placeholder editoriale</div>
            </div>
          )}
          <div className="sl-mono" style={{ position: "absolute", top: 16, left: 16, color: "rgba(255,255,255,0.85)" }}>Plate</div>
        </div>

        {/* Nome + ruolo + tag */}
        <div style={{ display: "flex", flexDirection: "column", justifyContent: "space-between" }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 32 }}>§ Avvocato · Founding Partner</div>
            <h1 style={{
              fontSize: "clamp(56px, 6vw, 88px)",
              lineHeight: 0.98, letterSpacing: "-0.025em", marginBottom: 32,
            }}>
              {lawyer.name.split(" ")[0]}<br/>
              <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>{lawyer.name.split(" ").slice(1).join(" ")}.</em>
            </h1>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 22, fontStyle: "italic", color: "var(--text)", marginBottom: 32 }}>
              {lawyer.role}
            </div>
          </div>

          <div style={{ display: "flex", flexWrap: "wrap", gap: 8, marginTop: 32 }}>
            {lawyer.spec.map(s => (
              <span key={s} style={{
                fontFamily: "var(--font-mono)", fontSize: 11, letterSpacing: "0.06em",
                textTransform: "uppercase", color: "var(--text-muted)",
                border: "1px solid var(--border)", padding: "6px 12px",
              }}>{s}</span>
            ))}
          </div>
        </div>
      </section>

      {/* BODY — sticky sx + bio dx */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px)",
        display: "grid", gridTemplateColumns: "240px 1fr", gap: 96,
      }}>
        {/* Sticky CTA mono sx */}
        <aside style={{ position: "sticky", top: 120, alignSelf: "start", height: "fit-content" }}>
          <div className="sl-mono" style={{ marginBottom: 24 }}>Contatto diretto</div>
          <div style={{ display: "grid", gap: 16, marginBottom: 32 }}>
            {[
              { l: "Telefono", v: "+39 081 245 67 89", h: "tel:+390812456789" },
              { l: "Email", v: "emiliano@saltellipartners.it", h: "mailto:emiliano@saltellipartners.it" },
              { l: "WhatsApp", v: "Scrivi su WhatsApp", h: "#" },
            ].map(c => (
              <a key={c.l} href={c.h} style={{
                display: "block", padding: "16px 0",
                borderTop: "1px solid var(--border)",
                fontFamily: "var(--font-mono)", fontSize: 11,
                letterSpacing: "0.08em", textTransform: "uppercase",
                color: "var(--primary)", textDecoration: "none",
              }}>
                <div style={{ color: "var(--text-muted)", marginBottom: 6 }}>{c.l}</div>
                <div>{c.v} →</div>
              </a>
            ))}
          </div>
          <div style={{ borderTop: "1px solid var(--border)", paddingTop: 16, fontSize: 12, color: "var(--text-muted)", lineHeight: 1.6 }}>
            Riceviamo solo<br/>su appuntamento.<br/>Risposta entro 24h.
          </div>
        </aside>

        {/* Bio + sezioni */}
        <div>
          {/* Bio */}
          <div style={{ marginBottom: 96 }}>
            <div className="sl-mono" style={{ marginBottom: 24 }}>§ 01 — Biografia</div>
            <div style={{ fontSize: 19, lineHeight: 1.75, color: "var(--text)", display: "grid", gap: 20, maxWidth: "60ch" }}>
              <p style={{ textIndent: 0 }}>
                <span style={{
                  fontFamily: "var(--font-display)", fontStyle: "italic",
                  fontSize: 96, float: "left", lineHeight: 0.85,
                  marginRight: 16, marginTop: 8, color: "var(--primary)",
                }}>E</span>
                miliano Saltelli si laurea in Giurisprudenza alla Federico II nel 2000 con una tesi in diritto
                tributario. Apre lo Studio nel 1999, ancora studente del quinto anno, in una stanza al secondo
                piano di un palazzo nobiliare a Chiaia.
              </p>
              <p>
                In oltre vent'anni ha rappresentato imprese, professionisti e privati cittadini in oltre
                quattrocento contenziosi fiscali, con particolare attenzione agli accertamenti sintetici e
                alle cartelle esattoriali. È iscritto all'albo speciale dei Cassazionisti dal 2014.
              </p>
              <p>
                Ha pubblicato saggi su <a href="#" className="sl-link">Diritto.it</a> e sul <a href="#" className="sl-link">Sole 24 Ore</a>.
                È stato relatore in convegni della Camera Avvocati di Napoli e dell'Ordine dei Commercialisti.
                Quando non è in studio, vive a Posillipo con la moglie e due figlie.
              </p>
            </div>
          </div>

          {/* Si occupa di — 6 aree */}
          <div style={{ marginBottom: 96 }}>
            <div className="sl-mono" style={{ marginBottom: 24 }}>§ 02 — Si occupa di</div>
            <h2 style={{ fontSize: "clamp(32px, 3.5vw, 44px)", marginBottom: 32, letterSpacing: "-0.015em" }}>
              Sei aree di competenza.
            </h2>
            <div>
              {lawyer.aree.map((a, i) => (
                <a key={a.t} href={a.h} className="sl-area" style={{ display: "grid" }}>
                  <div className="sl-area__num">0{i + 1} / 06</div>
                  <div className="sl-area__title">{a.t}</div>
                  <div className="sl-area__meta">Approfondisci →</div>
                </a>
              ))}
            </div>
          </div>

          {/* Formazione */}
          <div style={{ marginBottom: 96 }}>
            <div className="sl-mono" style={{ marginBottom: 24 }}>§ 03 — Formazione</div>
            <h2 style={{ fontSize: "clamp(32px, 3.5vw, 44px)", marginBottom: 48, letterSpacing: "-0.015em" }}>
              Formazione &amp; titoli.
            </h2>
            <div style={{ borderLeft: "1px solid var(--border)", paddingLeft: 32 }}>
              {lawyer.formazione.map((f, i) => (
                <div key={i} style={{ display: "grid", gridTemplateColumns: "100px 1fr", gap: 32, paddingBottom: 32, marginBottom: 32, borderBottom: i === lawyer.formazione.length - 1 ? "0" : "1px solid var(--border)" }}>
                  <div style={{
                    fontFamily: "var(--font-display)", fontStyle: "italic",
                    fontSize: 28, color: "var(--accent)", lineHeight: 1,
                  }}>{f.y}</div>
                  <div>
                    <h3 style={{ fontFamily: "var(--font-display)", fontSize: 20, color: "var(--primary)", marginBottom: 4, letterSpacing: "-0.01em" }}>
                      {f.t}
                    </h3>
                    <p style={{ fontSize: 14, color: "var(--text-muted)", lineHeight: 1.6 }}>{f.d}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Casi rappresentativi */}
          <div style={{ marginBottom: 96 }}>
            <div className="sl-mono" style={{ marginBottom: 24 }}>§ 04 — Vittorie recenti</div>
            <h2 style={{ fontSize: "clamp(32px, 3.5vw, 44px)", marginBottom: 48, letterSpacing: "-0.015em" }}>
              Tre casi rappresentativi.
            </h2>
            <div>
              {lawyer.casi.map((c, i) => (
                <div key={i} style={{
                  display: "grid", gridTemplateColumns: "200px 1fr 160px",
                  gap: 32, padding: "28px 0",
                  borderBottom: "1px solid var(--border)", alignItems: "baseline",
                }}>
                  <div className="sl-mono">{c.id}</div>
                  <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 18, color: "var(--primary)", lineHeight: 1.4 }}>
                    {c.desc}
                  </p>
                  <div style={{ fontFamily: "var(--font-display)", fontSize: 22, color: "var(--accent)", textAlign: "right", letterSpacing: "-0.01em" }}>
                    {c.outcome}
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* CTA */}
          <div style={{ paddingTop: 48, borderTop: "1px solid var(--border)" }}>
            <h2 style={{ fontSize: "clamp(40px, 5vw, 64px)", lineHeight: 1, letterSpacing: "-0.02em", marginBottom: 32 }}>
              Prenota un incontro<br/>
              <em style={{ fontStyle: "italic", color: "var(--accent)" }}>con Emiliano.</em>
            </h2>
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

window.S2AttorneySingle = S2AttorneySingle;
