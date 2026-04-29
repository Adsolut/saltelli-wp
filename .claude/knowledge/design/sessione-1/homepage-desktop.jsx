/* global React */

function HomepageDesktop() {
  const [scrolled, setScrolled] = React.useState(false);
  const [filter, setFilter] = React.useState("Tutte");
  const [hoveredArea, setHoveredArea] = React.useState(null);
  const scrollRef = React.useRef(null);

  React.useEffect(() => {
    const el = scrollRef.current;
    if (!el) return;
    const onScroll = () => setScrolled(el.scrollTop > 80);
    el.addEventListener("scroll", onScroll);
    return () => el.removeEventListener("scroll", onScroll);
  }, []);

  // SplitText reveal — riga per riga delle parole della headline
  const headlineWords = ["Diritto,", "con", "misura."];
  const [revealed, setRevealed] = React.useState(false);
  React.useEffect(() => {
    const t = setTimeout(() => setRevealed(true), 80);
    return () => clearTimeout(t);
  }, []);

  const areas = [
    { n: "01", t: "Diritto tributario", cat: "Tributario", tier1: true, lead: "Cartelle esattoriali, contenzioso fiscale, accertamenti." },
    { n: "02", t: "Diritto del lavoro", cat: "Lavoro", tier1: true, lead: "Licenziamenti, mobbing, contrattualistica, contenzioso INPS." },
    { n: "03", t: "Diritto di famiglia LGBTQ+", cat: "Famiglia", tier1: true, lead: "Unioni civili, genitorialità, separazioni, tutela patrimoniale." },
    { n: "04", t: "Diritto di famiglia", cat: "Famiglia", lead: "Separazioni, divorzi, affido, regimi patrimoniali." },
    { n: "05", t: "Diritto condominiale", cat: "Civile", lead: "Liti condominiali, delibere assembleari, regolamenti." },
    { n: "06", t: "Diritto immobiliare", cat: "Civile", lead: "Compravendite, locazioni, servitù, usucapione." },
    { n: "07", t: "Diritto societario", cat: "Civile", lead: "Costituzione, governance, patti parasociali." },
    { n: "08", t: "Contenzioso civile", cat: "Civile", lead: "Liti contrattuali, responsabilità civile, recupero crediti." },
    { n: "09", t: "Diritto penale dell'economia", cat: "Penale", lead: "Reati tributari, fallimentari, societari." },
    { n: "10", t: "Diritto penale", cat: "Penale", lead: "Difesa in tutti i gradi di giudizio." },
    { n: "11", t: "Diritto bancario", cat: "Civile", lead: "Anatocismo, usura, contratti di finanziamento." },
    { n: "12", t: "Diritto delle successioni", cat: "Civile", lead: "Testamenti, successioni, divisioni ereditarie." },
    { n: "13", t: "Diritto amministrativo", cat: "Civile", lead: "Ricorsi al TAR, contenzioso con la P.A." },
    { n: "14", t: "Recupero crediti", cat: "Civile", lead: "Decreti ingiuntivi, esecuzioni mobiliari e immobiliari." },
    { n: "15", t: "Risarcimento danni", cat: "Civile", lead: "Sinistri stradali, responsabilità medica." },
    { n: "16", t: "Diritto della privacy", cat: "Civile", lead: "GDPR, compliance, contenzioso Garante." },
    { n: "17", t: "Domiciliazioni", cat: "Civile", lead: "Servizio domiciliazioni in Napoli e provincia." },
    { n: "18", t: "Volontaria giurisdizione", cat: "Civile", lead: "Amministrazioni di sostegno, tutele, curatele." },
    { n: "19", t: "Diritto dell'esecuzione", cat: "Civile", lead: "Pignoramenti, opposizioni, procedure esecutive." },
  ];

  const filters = ["Tutte", "Civile", "Penale", "Tributario", "Lavoro", "Famiglia"];
  const visibleAreas = filter === "Tutte" ? areas : areas.filter(a => a.cat === filter);

  const lawyers = [
    { name: "Emiliano Saltelli", role: "Founding Partner · Tributarista", spec: ["Diritto tributario", "Cartelle esattoriali", "Contenzioso fiscale"], col: 1, span: 5, offset: 0 },
    { name: "Fabiana Saltelli", role: "Partner · Giuslavorista", spec: ["Diritto del lavoro", "Contenzioso INPS"], col: 7, span: 5, offset: 96 },
    { name: "Antonia Battista", role: "Of Counsel · Famiglia LGBTQ+", spec: ["Famiglia LGBTQ+", "Unioni civili", "Genitorialità"], col: 2, span: 5, offset: 64 },
    { name: "Stefano G. Tedesco", role: "Associate · Condominiale", spec: ["Condominiale", "Immobiliare"], col: 8, span: 4, offset: 32 },
  ];

  const cases = [
    { id: "vs. AGE Riscossione · 2024", outcome: "Annullamento", desc: "Annullamento di cartella esattoriale per importo superiore a 240.000 € a carico di società in liquidazione." },
    { id: "Cassazione · 2024", outcome: "Vittoria", desc: "Conferma in Cassazione di sentenza favorevole in materia di licenziamento per giusta causa illegittimo." },
    { id: "Tribunale di Napoli · 2023", outcome: "Riconoscimento", desc: "Primo riconoscimento in Campania di trascrizione integrale di nascita di minore con due madri." },
    { id: "Corte d'Appello · 2023", outcome: "Riforma", desc: "Riforma di sentenza di primo grado in materia di accertamento sintetico, con riduzione dell'80% del dovuto." },
  ];

  const press = [
    "Il Mattino", "La Repubblica · Napoli", "Il Sole 24 Ore", "Diritto.it", "Altalex", "Camera Avvocati Napoli",
  ];

  return (
    <div className="sl-root" ref={scrollRef} style={{ height: 900, overflowY: "auto", position: "relative", scrollBehavior: "smooth" }}>
      {/* Header — transparent → solid on scroll */}
      <header style={{
        position: "sticky", top: 0, zIndex: 50,
        background: scrolled ? "var(--background)" : "transparent",
        borderBottom: scrolled ? "1px solid var(--border)" : "1px solid transparent",
        transition: "all 300ms var(--ease-editorial)",
      }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", padding: "24px 96px", display: "grid", gridTemplateColumns: "auto 1fr auto", gap: 48, alignItems: "center" }}>
          <div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 22, color: "var(--primary)", letterSpacing: "-0.01em", lineHeight: 1.1 }}>
              Saltelli &amp; Partners
            </div>
            <div className="sl-mono" style={{ fontSize: 10, marginTop: 2 }}>Studio Legale · Napoli</div>
          </div>
          <nav style={{ display: "flex", gap: 40, justifyContent: "center", fontSize: 14, fontWeight: 500 }}>
            {["Studio", "Avvocati", "Competenze", "Casi", "Editoriale", "Contatti"].map(i => (
              <a key={i} href="#" className="sl-link" style={{ borderBottomColor: "transparent" }}>{i}</a>
            ))}
          </nav>
          <a href="tel:+390812345678" className="sl-mono" style={{ color: "var(--primary)", fontSize: 12 }}>
            +39 081 245 67 89
          </a>
        </div>
      </header>

      {/* HERO — 100vh */}
      <section style={{ minHeight: 820, padding: "120px 96px 80px", maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "8fr 4fr", gap: 64, position: "relative" }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 64 }}>Studio Legale · Napoli · Chiaia · Dal 1999</div>
          <h1 style={{
            fontSize: "clamp(80px, 9vw, 132px)",
            lineHeight: 0.98,
            letterSpacing: "-0.035em",
            fontWeight: 400,
            marginBottom: 56,
          }}>
            {headlineWords.map((w, i) => (
              <span key={i} style={{
                display: "inline-block",
                marginRight: 24,
                opacity: revealed ? 1 : 0,
                transform: revealed ? "translateY(0)" : "translateY(40px)",
                transition: `opacity 700ms var(--ease-editorial) ${i * 80}ms, transform 700ms var(--ease-editorial) ${i * 80}ms`,
              }}>{w}</span>
            ))}
          </h1>
          <p style={{
            fontFamily: "var(--font-display)",
            fontSize: 22,
            fontStyle: "italic",
            fontWeight: 400,
            lineHeight: 1.5,
            color: "var(--text)",
            maxWidth: "44ch",
            marginBottom: 64,
          }}>
            Studio Legale Saltelli &amp; Partners. Quattro avvocati a Chiaia, diciannove aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli.
          </p>
          <button className="sl-btn sl-btn--primary">
            Prenota un primo incontro
            <span className="arrow">→</span>
          </button>
        </div>

        {/* Right column — colophon */}
        <div style={{ alignSelf: "end", borderLeft: "1px solid var(--border)", paddingLeft: 32 }}>
          <div className="sl-mono" style={{ marginBottom: 16 }}>Colophon</div>
          <div style={{ display: "grid", gap: 24, fontSize: 13, color: "var(--text)", lineHeight: 1.7 }}>
            <div>
              <div className="sl-mono" style={{ marginBottom: 6 }}>Indirizzo</div>
              Via Vannella Gaetani, 27<br/>80121 Napoli — Chiaia
            </div>
            <div>
              <div className="sl-mono" style={{ marginBottom: 6 }}>Orari</div>
              Lun – Ven · 09:30 – 18:30<br/>Sabato su appuntamento
            </div>
            <div>
              <div className="sl-mono" style={{ marginBottom: 6 }}>Contatti</div>
              <a href="#" className="sl-link">studio@saltellipartners.it</a><br/>
              <span style={{ fontFamily: "var(--font-mono)", fontSize: 12 }}>+39 081 245 67 89</span>
            </div>
          </div>
        </div>

        {/* Scroll indicator */}
        <div style={{ position: "absolute", bottom: 32, left: 96, display: "flex", alignItems: "center", gap: 12 }}>
          <div style={{ width: 1, height: 32, background: "var(--text-muted)" }} />
          <div className="sl-mono">Scorri</div>
        </div>
      </section>

      {/* AREE DI PRATICA */}
      <section style={{ padding: "128px 96px", maxWidth: 1440, margin: "0 auto" }}>
        <div style={{ display: "grid", gridTemplateColumns: "auto 1fr", gap: 32, alignItems: "baseline", marginBottom: 64 }}>
          <div className="sl-mono">§ 01 — Aree di pratica</div>
          <h2 style={{ fontSize: "clamp(48px, 5vw, 72px)", letterSpacing: "-0.02em" }}>
            Diciannove aree.<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>Tre presidiate in profondità.</em>
          </h2>
        </div>

        {/* Filter pillole */}
        <div style={{ display: "flex", gap: 32, marginBottom: 48, paddingBottom: 24, borderBottom: "1px solid var(--border)" }}>
          {filters.map(f => (
            <button key={f} onClick={() => setFilter(f)} className="sl-mono" style={{
              background: "none", border: 0, cursor: "pointer", padding: "4px 0",
              color: filter === f ? "var(--primary)" : "var(--text-muted)",
              borderBottom: filter === f ? "1px solid var(--accent)" : "1px solid transparent",
              fontFamily: "var(--font-mono)", fontSize: 12, letterSpacing: "0.08em", textTransform: "uppercase",
            }}>{f}</button>
          ))}
        </div>

        <div style={{ display: "grid", gridTemplateColumns: "8fr 4fr", gap: 64, alignItems: "start" }}>
          <div>
            {visibleAreas.map((a) => (
              <div key={a.n}
                className={"sl-area" + (a.tier1 ? " sl-area--tier1" : "")}
                onMouseEnter={() => setHoveredArea(a)}
                onMouseLeave={() => setHoveredArea(null)}>
                <div className="sl-area__num">{a.n} / 19</div>
                <div className="sl-area__title">{a.t}</div>
                <div className="sl-area__meta">{a.tier1 ? "Tier 1 · approfondimento" : a.cat} →</div>
              </div>
            ))}
          </div>
          <div style={{ position: "sticky", top: 120, paddingTop: 28 }}>
            {hoveredArea ? (
              <div>
                <div className="sl-mono" style={{ marginBottom: 16 }}>{hoveredArea.cat} · {hoveredArea.tier1 ? "Tier 1" : "Tier 2"}</div>
                <div style={{ fontFamily: "var(--font-display)", fontSize: 28, lineHeight: 1.25, color: "var(--primary)", marginBottom: 16, fontStyle: "italic" }}>
                  {hoveredArea.lead}
                </div>
                <button className="sl-btn">
                  Approfondisci
                  <span className="arrow">→</span>
                </button>
              </div>
            ) : (
              <div style={{ color: "var(--text-muted)", fontSize: 14, lineHeight: 1.7, fontStyle: "italic", fontFamily: "var(--font-display)" }}>
                Passa il cursore su un'area per leggerne la sintesi.
              </div>
            )}
          </div>
        </div>
      </section>

      {/* LO STUDIO */}
      <section style={{ padding: "128px 96px", maxWidth: 1440, margin: "0 auto" }}>
        <div style={{ display: "grid", gridTemplateColumns: "auto 1fr", gap: 32, marginBottom: 56 }}>
          <div className="sl-mono">§ 02 — Lo studio</div>
          <h2 style={{ fontSize: "clamp(48px, 5vw, 72px)", letterSpacing: "-0.02em" }}>
            Una bottega, in senso napoletano.
          </h2>
        </div>
        <div style={{ maxWidth: 640, marginLeft: "20%", fontSize: 19, lineHeight: 1.75, color: "var(--text)", display: "grid", gap: 24 }}>
          <p style={{ textIndent: 0 }}>
            <span style={{ fontFamily: "var(--font-display)", fontSize: 84, float: "left", lineHeight: 0.85, marginRight: 16, marginTop: 8, color: "var(--primary)" }}>L</span>
            o Studio Saltelli &amp; Partners nasce nel 1999 per iniziativa di Emiliano Saltelli, allora giovane tributarista formatosi alla Federico II.
            Nel quarto di secolo successivo, lo Studio è cresciuto come si cresce a Napoli — per accumulazione paziente, una pratica alla volta,
            un avvocato alla volta — fino a diventare oggi una bottega di quattro professionisti.
          </p>
          <p>
            Crediamo che il diritto sia, prima di tutto, un'arte di ascolto. Le carte vengono dopo. Per questo non offriamo pacchetti
            né formule: ogni cliente è una storia, e ogni storia merita il tempo di essere capita.
          </p>
          <p>
            Lavoriamo in <a href="#" className="sl-link">Via Vannella Gaetani 27</a>, in un palazzo nobiliare a due passi dal lungomare di
            Chiaia. È qui che riceviamo, è qui che si tengono i nostri primi colloqui, ed è qui — quando possibile — che torniamo a vedersi
            anche per le pratiche più semplici.
          </p>
        </div>

        {/* Foto facciata — placeholder editoriale */}
        <div style={{ marginTop: 96, height: 480, background: "var(--surface)", display: "flex", alignItems: "center", justifyContent: "center", border: "1px solid var(--border)", position: "relative" }}>
          <div style={{ position: "absolute", top: 24, left: 32 }} className="sl-mono">Plate I · Facciata</div>
          <div style={{ textAlign: "center" }}>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 32, fontStyle: "italic", color: "var(--text-muted)", marginBottom: 8 }}>
              Via Vannella Gaetani, 27
            </div>
            <div className="sl-mono">Fotografia in B/N · 1440 × 480 · placeholder</div>
          </div>
          <div style={{ position: "absolute", bottom: 24, right: 32 }} className="sl-mono">Napoli · Chiaia</div>
        </div>
      </section>

      {/* AVVOCATI — asimmetrico */}
      <section style={{ padding: "128px 96px", maxWidth: 1440, margin: "0 auto" }}>
        <div style={{ display: "grid", gridTemplateColumns: "auto 1fr", gap: 32, marginBottom: 96 }}>
          <div className="sl-mono">§ 03 — Avvocati</div>
          <h2 style={{ fontSize: "clamp(48px, 5vw, 72px)", letterSpacing: "-0.02em" }}>
            Quattro<br/><em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>professionisti.</em>
          </h2>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(12, 1fr)", gap: 32, rowGap: 96 }}>
          {lawyers.map((l, i) => (
            <Lawyer key={i} l={l} />
          ))}
        </div>
      </section>

      {/* CASI E RISULTATI */}
      <section style={{ padding: "128px 96px", maxWidth: 1440, margin: "0 auto" }}>
        <div style={{ display: "grid", gridTemplateColumns: "auto 1fr", gap: 32, marginBottom: 64 }}>
          <div className="sl-mono">§ 04 — Vittorie recenti</div>
          <h2 style={{ fontSize: "clamp(48px, 5vw, 72px)", letterSpacing: "-0.02em" }}>
            Casi rappresentativi.
          </h2>
        </div>
        <div>
          {cases.map((c, i) => (
            <div key={i} style={{ display: "grid", gridTemplateColumns: "200px 1fr 200px", gap: 48, padding: "32px 0", borderBottom: "1px solid var(--border)", alignItems: "baseline" }}>
              <div className="sl-mono">{c.id}</div>
              <p style={{ fontSize: 20, fontFamily: "var(--font-display)", lineHeight: 1.4, color: "var(--primary)", fontStyle: "italic" }}>
                {c.desc}
              </p>
              <div style={{ textAlign: "right" }}>
                <div style={{ fontFamily: "var(--font-display)", fontSize: 24, color: "var(--accent)", letterSpacing: "-0.01em" }}>
                  {c.outcome}
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* EARNED MEDIA */}
      <section style={{ padding: "96px 96px", maxWidth: 1440, margin: "0 auto", background: "var(--surface)" }}>
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", flexWrap: "wrap", gap: 48 }}>
          <div className="sl-mono" style={{ color: "var(--primary)" }}>§ 05 — Parlano di noi</div>
          <div style={{ display: "flex", gap: 48, flexWrap: "wrap", alignItems: "center" }}>
            {press.map(p => (
              <div key={p} style={{ fontFamily: "var(--font-display)", fontSize: 18, color: "var(--text)", fontStyle: "italic" }}>{p}</div>
            ))}
          </div>
        </div>
      </section>

      {/* CONTATTI */}
      <section style={{ padding: "160px 96px 128px", maxWidth: 1440, margin: "0 auto" }}>
        <div style={{ display: "grid", gridTemplateColumns: "auto 1fr", gap: 32, marginBottom: 80 }}>
          <div className="sl-mono">§ 06 — Contatti</div>
          <h2 style={{ fontSize: "clamp(56px, 6vw, 96px)", letterSpacing: "-0.025em", lineHeight: 1 }}>
            Prenoti<br/>un primo<br/>
            <em style={{ fontStyle: "italic", color: "var(--accent)" }}>incontro.</em>
          </h2>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr 1fr", gap: 64, paddingLeft: "20%" }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 16 }}>Indirizzo</div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 24, lineHeight: 1.3, color: "var(--primary)" }}>
              Via Vannella<br/>Gaetani, 27<br/>Napoli — Chiaia
            </div>
          </div>
          <div>
            <div className="sl-mono" style={{ marginBottom: 16 }}>Telefono</div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 24, lineHeight: 1.3, color: "var(--primary)" }}>
              +39 081<br/>245 67 89
            </div>
          </div>
          <div>
            <div className="sl-mono" style={{ marginBottom: 16 }}>Email</div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 24, lineHeight: 1.3, color: "var(--primary)" }}>
              studio@<br/>saltelli<br/>partners.it
            </div>
          </div>
        </div>
        <div style={{ marginTop: 96, paddingLeft: "20%" }}>
          <button className="sl-btn sl-btn--primary">
            Prenota un primo incontro
            <span className="arrow">→</span>
          </button>
        </div>
      </section>

      {/* FOOTER */}
      <footer style={{ background: "var(--primary)", color: "var(--background)", padding: "96px 96px 32px" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto" }}>
          <div style={{ display: "grid", gridTemplateColumns: "2fr 4fr 2fr", gap: 64, paddingBottom: 64, borderBottom: "1px solid rgba(255,255,255,0.15)" }}>
            <div>
              <div style={{ fontFamily: "var(--font-display)", fontSize: 28, marginBottom: 24, lineHeight: 1.1 }}>
                Saltelli<br/>&amp; Partners
              </div>
              <div style={{ fontSize: 13, lineHeight: 1.8, opacity: 0.7 }}>
                <a href="#" style={{ color: "inherit", display: "block" }}>Lo studio</a>
                <a href="#" style={{ color: "inherit", display: "block" }}>Avvocati</a>
                <a href="#" style={{ color: "inherit", display: "block" }}>Casi</a>
                <a href="#" style={{ color: "inherit", display: "block" }}>Editoriale</a>
                <a href="#" style={{ color: "inherit", display: "block" }}>Contatti</a>
              </div>
            </div>
            <div>
              <div className="sl-mono" style={{ color: "rgba(255,255,255,0.6)", marginBottom: 24 }}>Diciannove aree</div>
              <div style={{ columnCount: 3, columnGap: 32, fontSize: 13, lineHeight: 2, opacity: 0.85 }}>
                {areas.map(a => (
                  <a key={a.n} href="#" style={{ color: "inherit", display: "block", breakInside: "avoid" }}>{a.t}</a>
                ))}
              </div>
            </div>
            <div>
              <div className="sl-mono" style={{ color: "rgba(255,255,255,0.6)", marginBottom: 24 }}>Contatti</div>
              <div style={{ fontSize: 13, lineHeight: 1.85, opacity: 0.85 }}>
                Via Vannella Gaetani, 27<br/>
                80121 Napoli — Chiaia<br/><br/>
                +39 081 245 67 89<br/>
                studio@saltellipartners.it<br/>
                pec@pec.saltellipartners.it<br/><br/>
                Ordine Avv. Napoli<br/>
                P.IVA 04123456789
              </div>
            </div>
          </div>
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", paddingTop: 32, fontSize: 11, fontFamily: "var(--font-mono)", letterSpacing: "0.08em", textTransform: "uppercase", opacity: 0.55 }}>
            <div>© 2026 Studio Legale Saltelli &amp; Partners</div>
            <div style={{ display: "flex", gap: 32 }}>
              <a href="#" style={{ color: "inherit" }}>Privacy</a>
              <a href="#" style={{ color: "inherit" }}>Cookie</a>
              <a href="#" style={{ color: "inherit" }}>Instagram</a>
              <a href="#" style={{ color: "inherit" }}>LinkedIn</a>
            </div>
          </div>
        </div>
      </footer>


    </div>
  );
}

function Lawyer({ l }) {
  const [hover, setHover] = React.useState(false);
  return (
    <div style={{
      gridColumn: `${l.col} / span ${l.span}`,
      marginTop: l.offset,
    }} onMouseEnter={() => setHover(true)} onMouseLeave={() => setHover(false)}>
      <div style={{
        aspectRatio: "3 / 4",
        background: "var(--surface)",
        marginBottom: 24,
        position: "relative",
        overflow: "hidden",
        filter: hover ? "grayscale(0)" : "grayscale(1) contrast(1.05)",
        transition: "filter 600ms var(--ease-editorial)",
        border: "1px solid var(--border)",
      }}>
        <div style={{
          position: "absolute", inset: 0,
          background: hover
            ? "linear-gradient(135deg, #d4c8b0 0%, #8a7a5e 100%)"
            : "linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%)",
          transition: "background 600ms var(--ease-editorial)",
        }} />
        <div style={{
          position: "absolute", inset: 0, display: "flex", alignItems: "flex-end", padding: 24,
        }}>
          <div className="sl-mono" style={{ color: "rgba(255,255,255,0.85)" }}>Ritratto · 3:4</div>
        </div>
      </div>
      <div className="sl-mono" style={{ marginBottom: 8 }}>{l.role}</div>
      <h3 style={{ fontFamily: "var(--font-display)", fontSize: 36, lineHeight: 1.1, color: "var(--primary)", marginBottom: 16, letterSpacing: "-0.02em" }}>
        {l.name}
      </h3>
      <div style={{ display: "flex", gap: 8, flexWrap: "wrap" }}>
        {l.spec.map(s => (
          <span key={s} style={{
            fontFamily: "var(--font-mono)", fontSize: 11, letterSpacing: "0.06em",
            textTransform: "uppercase", color: "var(--text-muted)",
            border: "1px solid var(--border)", padding: "5px 10px",
          }}>{s}</span>
        ))}
      </div>
    </div>
  );
}

window.HomepageDesktop = HomepageDesktop;
