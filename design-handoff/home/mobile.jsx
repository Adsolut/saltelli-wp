/* global React */

function HomepageMobile() {
  const [menuOpen, setMenuOpen] = React.useState(false);
  const [openSect, setOpenSect] = React.useState(null);
  const scrollRef = React.useRef(null);

  const headlineWords = ["Diritto,", "con", "misura."];
  const [revealed, setRevealed] = React.useState(false);
  React.useEffect(() => {
    const t = setTimeout(() => setRevealed(true), 80);
    return () => clearTimeout(t);
  }, []);

  const areas = [
    { n: "01", t: "Diritto tributario", tier1: true },
    { n: "02", t: "Diritto del lavoro", tier1: true },
    { n: "03", t: "Famiglia LGBTQ+", tier1: true },
    { n: "04", t: "Diritto di famiglia" },
    { n: "05", t: "Condominiale" },
    { n: "06", t: "Immobiliare" },
    { n: "07", t: "Societario" },
    { n: "08", t: "Contenzioso civile" },
    { n: "09", t: "Penale dell'economia" },
    { n: "10", t: "Penale" },
    { n: "11", t: "Bancario" },
    { n: "12", t: "Successioni" },
  ];

  const lawyers = [
    { name: "Emiliano Saltelli", role: "Founding Partner · Tributarista", spec: ["Tributario", "Cartelle"] },
    { name: "Fabiana Saltelli", role: "Partner · Giuslavorista", spec: ["Lavoro", "INPS"] },
    { name: "Antonia Battista", role: "Of Counsel · Famiglia LGBTQ+", spec: ["LGBTQ+", "Famiglia"] },
    { name: "Stefano G. Tedesco", role: "Associate · Condominiale", spec: ["Condominiale", "Immobiliare"] },
  ];

  const cases = [
    { id: "vs. AGE Riscossione · 2024", outcome: "Annullamento", desc: "Annullamento di cartella esattoriale per importo superiore a 240.000 €." },
    { id: "Cassazione · 2024", outcome: "Vittoria", desc: "Conferma in Cassazione di sentenza in materia di licenziamento illegittimo." },
    { id: "Tribunale Napoli · 2023", outcome: "Riconoscimento", desc: "Primo riconoscimento in Campania di trascrizione di nascita con due madri." },
  ];

  return (
    <div className="sl-root" ref={scrollRef} style={{ height: 812, width: 375, overflowY: "auto", overflowX: "hidden", position: "relative", margin: "0 auto" }}>
      {/* Header mobile */}
      <header style={{
        position: "sticky", top: 0, zIndex: 50,
        background: "var(--background)",
        borderBottom: "1px solid var(--border)",
        padding: "16px 20px",
        display: "flex", alignItems: "center", justifyContent: "space-between",
      }}>
        <SLLogoHorizontal tone="dark" size="sm" />
        <div style={{ display: "flex", gap: 16, alignItems: "center" }}>
          <a href="tel:+390812456789" className="sl-mono" style={{ color: "var(--primary)", fontSize: 11 }}>Telefono</a>
          <button onClick={() => setMenuOpen(!menuOpen)} style={{ background: "none", border: 0, padding: 4, cursor: "pointer" }}>
            <div style={{ width: 24, height: 1, background: "var(--text)", marginBottom: 6 }} />
            <div style={{ width: 24, height: 1, background: "var(--text)" }} />
          </button>
        </div>
      </header>

      {menuOpen && (
        <div style={{ position: "absolute", inset: "60px 0 0 0", background: "var(--background)", zIndex: 40, padding: "32px 20px", borderTop: "1px solid var(--border)" }}>
          {["Studio", "Avvocati", "Competenze", "Casi", "Editoriale", "Contatti"].map(i => (
            <div key={i} style={{ padding: "16px 0", borderBottom: "1px solid var(--border)", fontFamily: "var(--font-display)", fontSize: 28, color: "var(--primary)", letterSpacing: "-0.01em" }}>
              {i}
            </div>
          ))}
        </div>
      )}

      {/* HERO mobile — 100vh */}
      <section style={{ minHeight: 700, padding: "48px 20px 32px", display: "flex", flexDirection: "column", justifyContent: "space-between" }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 40, fontSize: 10 }}>Studio · Napoli · Chiaia · 1999</div>
          <h1 style={{
            fontSize: 64,
            lineHeight: 0.98,
            letterSpacing: "-0.03em",
            fontWeight: 400,
            marginBottom: 32,
          }}>
            {headlineWords.map((w, i) => (
              <span key={i} style={{
                display: "inline-block",
                marginRight: 12,
                opacity: revealed ? 1 : 0,
                transform: revealed ? "translateY(0)" : "translateY(30px)",
                transition: `opacity 700ms var(--ease-editorial) ${i * 80}ms, transform 700ms var(--ease-editorial) ${i * 80}ms`,
              }}>{w}</span>
            ))}
          </h1>
          <p style={{
            fontFamily: "var(--font-display)",
            fontSize: 17,
            fontStyle: "italic",
            lineHeight: 1.5,
            color: "var(--text)",
            marginBottom: 40,
          }}>
            Quattro avvocati a Chiaia, diciannove aree di pratica, vent'anni accanto a famiglie e imprese di Napoli.
          </p>
          <button className="sl-btn sl-btn--primary">
            Prenota un primo incontro
            <span className="arrow">→</span>
          </button>
        </div>

        <div style={{ borderTop: "1px solid var(--border)", paddingTop: 20, display: "grid", gridTemplateColumns: "1fr 1fr", gap: 20 }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 6, fontSize: 9 }}>Indirizzo</div>
            <div style={{ fontSize: 12, lineHeight: 1.5 }}>Via Vannella Gaetani, 27<br/>80121 Napoli</div>
          </div>
          <div>
            <div className="sl-mono" style={{ marginBottom: 6, fontSize: 9 }}>Orari</div>
            <div style={{ fontSize: 12, lineHeight: 1.5 }}>Lun – Ven<br/>09:30 – 18:30</div>
          </div>
        </div>
      </section>

      {/* AREE */}
      <section style={{ padding: "80px 20px" }}>
        <div className="sl-mono" style={{ marginBottom: 16 }}>§ 01 — Aree di pratica</div>
        <h2 style={{ fontSize: 40, letterSpacing: "-0.02em", marginBottom: 32, lineHeight: 1.05 }}>
          Diciannove aree.<br/>
          <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>Tre presidiate.</em>
        </h2>
        <div>
          {areas.map((a) => (
            <div key={a.n} className={"sl-area" + (a.tier1 ? " sl-area--tier1" : "")} style={{ gridTemplateColumns: "40px 1fr auto", padding: "20px 0", gap: 16 }}>
              <div className="sl-area__num" style={{ fontSize: 10 }}>{a.n}</div>
              <div className="sl-area__title" style={{ fontSize: 22 }}>{a.t}</div>
              <div style={{ fontFamily: "var(--font-mono)", fontSize: 12, color: a.tier1 ? "var(--accent)" : "var(--text-muted)" }}>→</div>
            </div>
          ))}
          <div style={{ paddingTop: 32 }}>
            <button className="sl-btn">
              Vedi tutte le 19 aree
              <span className="arrow">→</span>
            </button>
          </div>
        </div>
      </section>

      {/* LO STUDIO */}
      <section style={{ padding: "80px 20px" }}>
        <div className="sl-mono" style={{ marginBottom: 16 }}>§ 02 — Lo studio</div>
        <h2 style={{ fontSize: 36, letterSpacing: "-0.02em", marginBottom: 32, lineHeight: 1.1 }}>
          Una bottega,<br/>in senso napoletano.
        </h2>
        <div style={{ fontSize: 16, lineHeight: 1.7, display: "grid", gap: 16 }}>
          <p>
            <span style={{ fontFamily: "var(--font-display)", fontSize: 60, float: "left", lineHeight: 0.85, marginRight: 12, marginTop: 6, color: "var(--primary)" }}>L</span>
            o Studio Saltelli &amp; Partners nasce nel 1999 per iniziativa di Emiliano Saltelli, allora giovane tributarista formatosi alla Federico II.
          </p>
          <p>
            Crediamo che il diritto sia, prima di tutto, un'arte di ascolto. Le carte vengono dopo. Per questo non offriamo pacchetti né formule: ogni cliente è una storia.
          </p>
        </div>
        <div style={{ marginTop: 40, height: 240, background: "var(--surface)", border: "1px solid var(--border)", display: "flex", alignItems: "center", justifyContent: "center", position: "relative" }}>
          <div style={{ position: "absolute", top: 12, left: 16 }} className="sl-mono">Plate I</div>
          <div style={{ textAlign: "center" }}>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 18, fontStyle: "italic", color: "var(--text-muted)" }}>Via Vannella Gaetani, 27</div>
            <div className="sl-mono" style={{ marginTop: 4 }}>Foto B/N · placeholder</div>
          </div>
        </div>
      </section>

      {/* AVVOCATI */}
      <section style={{ padding: "80px 20px" }}>
        <div className="sl-mono" style={{ marginBottom: 16 }}>§ 03 — Avvocati</div>
        <h2 style={{ fontSize: 40, letterSpacing: "-0.02em", marginBottom: 48, lineHeight: 1.05 }}>
          Quattro<br/><em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>professionisti.</em>
        </h2>
        <div style={{ display: "grid", gap: 56 }}>
          {lawyers.map((l, i) => (
            <div key={i} style={{ marginLeft: i % 2 === 1 ? 32 : 0, marginRight: i % 2 === 0 ? 32 : 0 }}>
              <div style={{
                aspectRatio: "3 / 4",
                background: "linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%)",
                marginBottom: 16,
                position: "relative",
                border: "1px solid var(--border)",
              }}>
                <div style={{ position: "absolute", bottom: 12, left: 12 }} className="sl-mono" >Ritratto · 3:4</div>
              </div>
              <div className="sl-mono" style={{ marginBottom: 6, fontSize: 10 }}>{l.role}</div>
              <h3 style={{ fontFamily: "var(--font-display)", fontSize: 26, lineHeight: 1.1, color: "var(--primary)", marginBottom: 12, letterSpacing: "-0.02em" }}>
                {l.name}
              </h3>
              <div style={{ display: "flex", gap: 6, flexWrap: "wrap" }}>
                {l.spec.map(s => (
                  <span key={s} style={{
                    fontFamily: "var(--font-mono)", fontSize: 10, letterSpacing: "0.06em",
                    textTransform: "uppercase", color: "var(--text-muted)",
                    border: "1px solid var(--border)", padding: "4px 8px",
                  }}>{s}</span>
                ))}
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* CASI */}
      <section style={{ padding: "80px 20px" }}>
        <div className="sl-mono" style={{ marginBottom: 16 }}>§ 04 — Vittorie recenti</div>
        <h2 style={{ fontSize: 36, letterSpacing: "-0.02em", marginBottom: 32, lineHeight: 1.1 }}>
          Casi<br/>rappresentativi.
        </h2>
        {cases.map((c, i) => (
          <div key={i} style={{ padding: "24px 0", borderBottom: "1px solid var(--border)" }}>
            <div className="sl-mono" style={{ marginBottom: 12, fontSize: 10 }}>{c.id}</div>
            <p style={{ fontSize: 17, fontFamily: "var(--font-display)", lineHeight: 1.4, color: "var(--primary)", fontStyle: "italic", marginBottom: 12 }}>
              {c.desc}
            </p>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 18, color: "var(--accent)" }}>
              {c.outcome} →
            </div>
          </div>
        ))}
      </section>

      {/* PRESS */}
      <section style={{ padding: "64px 20px", background: "var(--surface)" }}>
        <div className="sl-mono" style={{ marginBottom: 24, color: "var(--primary)" }}>§ 05 — Parlano di noi</div>
        <div style={{ display: "grid", gap: 12 }}>
          {["Il Mattino", "La Repubblica · Napoli", "Il Sole 24 Ore", "Diritto.it", "Altalex"].map(p => (
            <div key={p} style={{ fontFamily: "var(--font-display)", fontSize: 22, color: "var(--text)", fontStyle: "italic", borderBottom: "1px solid var(--border)", paddingBottom: 12 }}>
              {p}
            </div>
          ))}
        </div>
      </section>

      {/* CONTATTI */}
      <section style={{ padding: "96px 20px 64px" }}>
        <div className="sl-mono" style={{ marginBottom: 16 }}>§ 06 — Contatti</div>
        <h2 style={{ fontSize: 56, letterSpacing: "-0.025em", lineHeight: 0.95, marginBottom: 48 }}>
          Prenoti<br/>un primo<br/>
          <em style={{ fontStyle: "italic", color: "var(--accent)" }}>incontro.</em>
        </h2>
        <div style={{ display: "grid", gap: 32 }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 8, fontSize: 10 }}>Indirizzo</div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 22, lineHeight: 1.3, color: "var(--primary)" }}>
              Via Vannella Gaetani, 27<br/>Napoli — Chiaia
            </div>
          </div>
          <div>
            <div className="sl-mono" style={{ marginBottom: 8, fontSize: 10 }}>Telefono</div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 22, lineHeight: 1.3, color: "var(--primary)" }}>
              +39 081 245 67 89
            </div>
          </div>
          <div>
            <div className="sl-mono" style={{ marginBottom: 8, fontSize: 10 }}>Email</div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 22, lineHeight: 1.3, color: "var(--primary)", wordBreak: "break-word" }}>
              studio@saltellipartners.it
            </div>
          </div>
        </div>
        <div style={{ marginTop: 48 }}>
          <button className="sl-btn sl-btn--primary">
            Prenota un primo incontro
            <span className="arrow">→</span>
          </button>
        </div>
      </section>

      {/* FOOTER */}
      <footer style={{ background: "var(--primary)", color: "var(--background)", padding: "48px 20px 24px" }}>
        <div style={{ marginBottom: 24 }}>
          <SLLogoStack tone="light" size="sm" />
        </div>
        <div className="sl-mono" style={{ color: "rgba(255,255,255,0.6)", marginBottom: 12 }}>Studio</div>
        <div style={{ fontSize: 12, lineHeight: 2, opacity: 0.85, marginBottom: 24 }}>
          Lo studio · Avvocati · Casi · Editoriale · Contatti
        </div>
        <div className="sl-mono" style={{ color: "rgba(255,255,255,0.6)", marginBottom: 12 }}>Contatti</div>
        <div style={{ fontSize: 12, lineHeight: 1.85, opacity: 0.85, marginBottom: 32 }}>
          Via Vannella Gaetani, 27<br/>80121 Napoli — Chiaia<br/>+39 081 245 67 89<br/>studio@saltellipartners.it
        </div>
        <div style={{ paddingTop: 16, borderTop: "1px solid rgba(255,255,255,0.15)", fontSize: 9, fontFamily: "var(--font-mono)", letterSpacing: "0.08em", textTransform: "uppercase", opacity: 0.55 }}>
          © 2026 Saltelli &amp; Partners · Privacy · Cookie
        </div>
      </footer>

      {/* WhatsApp mini editorial — solo mobile */}
      <a href="#" style={{
        position: "absolute", bottom: 16, right: 16, zIndex: 30,
        background: "var(--background)", border: "1px solid var(--border)",
        padding: "8px 12px", fontFamily: "var(--font-mono)", fontSize: 10,
        letterSpacing: "0.08em", textTransform: "uppercase", color: "var(--primary)",
      }}>
        WhatsApp →
      </a>
    </div>
  );
}

window.HomepageMobile = HomepageMobile;
