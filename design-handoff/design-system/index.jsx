/* global React */

function DesignSystem() {
  const swatches = [
    { name: "Background", val: "#FAFAF8", role: "Crema base — rompe con #FFF puro" },
    { name: "Surface", val: "#F2F0EA", role: "Crema scura — superfici secondarie" },
    { name: "Primary", val: "#1B2B4B", role: "Navy profondo — MAI #000" },
    { name: "Accent", val: "#B8860B", role: "Oro/bronzo — parsimonia massima" },
    { name: "Text", val: "#2D2D2D", role: "Grigio scuro — MAI #000 puro" },
    { name: "Text muted", val: "#6B6B6B", role: "Metadati, didascalie" },
    { name: "Border", val: "#E5E0D5", role: "Linee, divisori" },
  ];

  const typeRows = [
    { token: "--fs-display", clamp: "clamp(48px, 8vw, 120px)", sample: "Diritto, con misura.", font: "var(--font-display)", weight: 400, ls: "-0.02em", lh: 1.05, size: 96 },
    { token: "--fs-h1", clamp: "clamp(36px, 5vw, 64px)", sample: "Aree di pratica", font: "var(--font-display)", weight: 400, ls: "-0.02em", lh: 1.1, size: 56 },
    { token: "--fs-h2", clamp: "clamp(28px, 3.5vw, 44px)", sample: "Lo studio", font: "var(--font-display)", weight: 400, ls: "-0.01em", lh: 1.15, size: 40 },
    { token: "--fs-h3", clamp: "clamp(20px, 2vw, 28px)", sample: "Diritto tributario", font: "var(--font-display)", weight: 400, ls: "-0.01em", lh: 1.2, size: 26 },
    { token: "--fs-body", clamp: "clamp(16px, 1.1vw, 18px)", sample: "Quattro avvocati a Chiaia, diciannove aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli.", font: "var(--font-body)", weight: 400, ls: "0", lh: 1.65, size: 18 },
    { token: "--fs-small", clamp: "14px fixed", sample: "Studio Legale Saltelli & Partners — Via Vannella Gaetani 27, Chiaia.", font: "var(--font-body)", weight: 400, ls: "0", lh: 1.55, size: 14 },
    { token: "--fs-micro / mono", clamp: "12px · letter-spacing 0.08em", sample: "AREA · TRIBUTARIO · 04 / 19", font: "var(--font-mono)", weight: 400, ls: "0.08em", lh: 1.4, size: 12, mono: true },
  ];

  const spacing = [
    { t: "s-1", v: 4 }, { t: "s-2", v: 8 }, { t: "s-3", v: 16 },
    { t: "s-4", v: 24 }, { t: "s-5", v: 32 }, { t: "s-6", v: 48 },
    { t: "s-7", v: 64 }, { t: "s-8", v: 96 }, { t: "s-9", v: 128 }, { t: "s-10", v: 192 },
  ];

  const areas = [
    { num: "01", title: "Diritto tributario", meta: "Tier 1 · 12 articoli · Saltelli", tier1: true },
    { num: "02", title: "Diritto del lavoro", meta: "Tier 1 · 9 articoli · F. Saltelli", tier1: true },
    { num: "03", title: "Diritto di famiglia LGBTQ+", meta: "Tier 1 · 6 articoli · Battista", tier1: true },
    { num: "04", title: "Condominiale e immobiliare", meta: "Tier 2 · 4 articoli · Tedesco" },
  ];

  const [openIdx, setOpenIdx] = React.useState(0);
  const faqs = [
    { q: "In quanto tempo posso ottenere un primo incontro?", a: "Solitamente entro 3-5 giorni lavorativi. Per urgenze tributarie con scadenze imminenti, lo Studio garantisce un primo confronto telefonico entro 24 ore." },
    { q: "È possibile un consulto a distanza?", a: "Sì, lo Studio offre videoconsulti programmati per clienti fuori Napoli. Il primo incontro resta consigliato in presenza, in Via Vannella Gaetani 27." },
    { q: "Quali documenti portare al primo appuntamento?", a: "Tutta la corrispondenza ricevuta dall'Agenzia delle Entrate o dalla controparte, contratti pertinenti, e una linea del tempo essenziale dei fatti." },
  ];

  return (
    <div className="sl-root" style={{ padding: "96px clamp(48px, 6vw, 96px)", maxWidth: 1440, margin: "0 auto" }}>
      {/* Colophon header */}
      <header style={{ display: "grid", gridTemplateColumns: "1fr auto", alignItems: "end", gap: 32, paddingBottom: 32, borderBottom: "1px solid var(--border)" }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 24 }}>Design System · v1.0 · Sessione 1</div>
          <h1 style={{ fontSize: "clamp(48px, 6vw, 88px)", lineHeight: 1.05 }}>
            Legal Luxury<br/>Minimal.
          </h1>
        </div>
        <div className="sl-mono" style={{ textAlign: "right", lineHeight: 1.8 }}>
          Studio Legale<br/>Saltelli &amp; Partners<br/>Napoli · Chiaia
        </div>
      </header>

      <p style={{ maxWidth: "60ch", marginTop: 48, fontSize: 18, color: "var(--text-muted)", lineHeight: 1.65 }}>
        Boutique editoriale italiana. Pulizia anglosassone reinterpretata in chiave napoletana contemporanea.
        Tipografia dominante, spazio bianco aggressivo, asimmetria deliberata. Movimento sottile, mai esibito.
      </p>

      {/* COLORS */}
      <Section num="01" title="Colore" subtitle="Crema, navy, bronzo. Niente nero puro, niente bianco puro.">
        <div style={{ display: "grid", gridTemplateColumns: "repeat(7, 1fr)", gap: 16 }}>
          {swatches.map((s) => (
            <div key={s.name}>
              <div style={{
                aspectRatio: "3 / 4",
                background: s.val,
                border: s.val === "#FAFAF8" ? "1px solid var(--border)" : "none",
                marginBottom: 12,
              }} />
              <div className="sl-mono" style={{ marginBottom: 4 }}>{s.name}</div>
              <div style={{ fontFamily: "var(--font-mono)", fontSize: 12, color: "var(--text)", marginBottom: 6 }}>{s.val}</div>
              <div style={{ fontSize: 12, color: "var(--text-muted)", lineHeight: 1.5 }}>{s.role}</div>
            </div>
          ))}
        </div>

        <div style={{ marginTop: 48, padding: 24, background: "var(--surface)", display: "grid", gridTemplateColumns: "1fr 1fr", gap: 32 }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 8, color: "var(--primary)" }}>Vietati</div>
            <ul style={{ margin: 0, padding: 0, listStyle: "none", fontSize: 14, lineHeight: 1.8, color: "var(--text)" }}>
              <li>· #000 puro come testo</li>
              <li>· #FFF puro come background</li>
              <li>· Rossi aggressivi, viola, magenta</li>
            </ul>
          </div>
          <div>
            <div className="sl-mono" style={{ marginBottom: 8, color: "var(--primary)" }}>Uso accent</div>
            <ul style={{ margin: 0, padding: 0, listStyle: "none", fontSize: 14, lineHeight: 1.8, color: "var(--text)" }}>
              <li>· Hover stati lista &amp; link</li>
              <li>· Iniziali aree tier-1</li>
              <li>· Mai grandi superfici, mai CTA</li>
            </ul>
          </div>
        </div>
      </Section>

      {/* TYPOGRAPHY */}
      <Section num="02" title="Tipografia" subtitle="Playfair Display + DM Sans + JetBrains Mono. Pairing serif display + sans body — leva massima.">
        <div style={{ display: "grid", gridTemplateColumns: "auto 1fr", gap: "48px 64px", alignItems: "baseline" }}>
          {typeRows.map((r) => (
            <React.Fragment key={r.token}>
              <div style={{ minWidth: 200 }}>
                <div className="sl-mono" style={{ marginBottom: 6 }}>{r.token}</div>
                <div style={{ fontFamily: "var(--font-mono)", fontSize: 11, color: "var(--text-muted)" }}>{r.clamp}</div>
              </div>
              <div style={{
                fontFamily: r.font,
                fontSize: r.size,
                fontWeight: r.weight,
                letterSpacing: r.ls,
                lineHeight: r.lh,
                color: r.mono ? "var(--text-muted)" : "var(--primary)",
                textTransform: r.mono ? "uppercase" : "none",
                maxWidth: r.size <= 18 ? "60ch" : "none",
              }}>
                {r.sample}
              </div>
            </React.Fragment>
          ))}
        </div>
      </Section>

      {/* SPACING */}
      <Section num="03" title="Spacing" subtitle="Scala 8-base, da 4 a 192. Gerarchia respiratoria.">
        <div style={{ display: "flex", alignItems: "flex-end", gap: 24, height: 220, paddingTop: 40 }}>
          {spacing.map((s) => (
            <div key={s.t} style={{ display: "flex", flexDirection: "column", alignItems: "center", gap: 12 }}>
              <div style={{
                width: 32,
                height: s.v,
                background: "var(--primary)",
              }} />
              <div className="sl-mono">{s.t}</div>
              <div style={{ fontFamily: "var(--font-mono)", fontSize: 11, color: "var(--text-muted)" }}>{s.v}</div>
            </div>
          ))}
        </div>
        <div style={{ marginTop: 56, display: "grid", gridTemplateColumns: "1fr 1fr", gap: 48 }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 12 }}>Grid</div>
            <p style={{ fontSize: 14, lineHeight: 1.7, color: "var(--text)" }}>
              Container max 1440px · padding clamp(24, 5vw, 96)<br/>
              12 col desktop · 6 col tablet · 4 col mobile<br/>
              Gap 32 / 24 / 16
            </p>
          </div>
          <div>
            <div className="sl-mono" style={{ marginBottom: 12 }}>Breakpoints</div>
            <p style={{ fontSize: 14, lineHeight: 1.7, color: "var(--text)" }}>
              375 · 768 · 1024 · 1440<br/>
              Mobile-first<br/>
              Animazioni semplificate &lt; 768
            </p>
          </div>
        </div>
      </Section>

      {/* COMPONENTS */}
      <Section num="04" title="Componenti base" subtitle="Quattro elementi sufficienti per costruire l'intero sito.">
        {/* Buttons */}
        <ComponentBlock label="Button minimal — testo + linea sotto, mai filled">
          <div style={{ display: "flex", gap: 64, alignItems: "center", flexWrap: "wrap" }}>
            <button className="sl-btn">
              Prenota un primo incontro
              <span className="arrow">→</span>
            </button>
            <button className="sl-btn sl-btn--primary">
              Scopri lo studio
              <span className="arrow">→</span>
            </button>
            <span className="sl-mono">Hover: linea diventa bronzo, freccia trasla 6px</span>
          </div>
        </ComponentBlock>

        {/* Links */}
        <ComponentBlock label="Link tipografico — hover bronzo">
          <p style={{ fontSize: 18, lineHeight: 1.7, color: "var(--text)", maxWidth: "62ch" }}>
            Lo Studio si occupa di <a href="#" className="sl-link">diritto tributario</a>, di{" "}
            <a href="#" className="sl-link">tutela LGBTQ+</a> in materia di famiglia, e accompagna imprese e professionisti
            nei contenziosi con l'<a href="#" className="sl-link">Agenzia delle Entrate</a>.
          </p>
        </ComponentBlock>

        {/* Area list item */}
        <ComponentBlock label="List item · Area di pratica — hover translateX 8px + linea bronzo">
          <div>
            {areas.map((a) => (
              <div key={a.num} className={"sl-area" + (a.tier1 ? " sl-area--tier1" : "")}>
                <div className="sl-area__num">{a.num} / 19</div>
                <div className="sl-area__title">{a.title}</div>
                <div className="sl-area__meta">{a.meta}</div>
              </div>
            ))}
          </div>
        </ComponentBlock>

        {/* Accordion */}
        <ComponentBlock label="Accordion FAQ — domande in serif, risposte in sans, niente shadow">
          <div className="sl-acc" style={{ maxWidth: 760 }}>
            {faqs.map((f, i) => (
              <div key={i} className="sl-acc__item" data-open={openIdx === i}>
                <button className="sl-acc__btn" onClick={() => setOpenIdx(openIdx === i ? -1 : i)}>
                  <span>{f.q}</span>
                  <span className="sl-acc__icon">+</span>
                </button>
                <div className="sl-acc__panel">
                  <div className="sl-acc__inner">{f.a}</div>
                </div>
              </div>
            ))}
          </div>
        </ComponentBlock>
      </Section>

      <footer style={{ marginTop: 128, paddingTop: 32, borderTop: "1px solid var(--border)", display: "flex", justifyContent: "space-between" }}>
        <div className="sl-mono">Sessione 1 · Design System completo</div>
        <div className="sl-mono">Prossimo: Frame 1 — Homepage</div>
      </footer>
    </div>
  );
}

function Section({ num, title, subtitle, children }) {
  return (
    <section style={{ marginTop: 128 }}>
      <div style={{ display: "grid", gridTemplateColumns: "120px 1fr", gap: 32, alignItems: "baseline", marginBottom: 56 }}>
        <div className="sl-mono">{num} / 04</div>
        <div>
          <h2 style={{ fontSize: "clamp(36px, 4vw, 56px)", marginBottom: 12 }}>{title}</h2>
          <p style={{ fontSize: 16, color: "var(--text-muted)", maxWidth: "60ch" }}>{subtitle}</p>
        </div>
      </div>
      <div style={{ paddingLeft: 152 }}>{children}</div>
    </section>
  );
}

function ComponentBlock({ label, children }) {
  return (
    <div style={{ marginBottom: 64, paddingBottom: 64, borderBottom: "1px solid var(--border)" }}>
      <div className="sl-mono" style={{ marginBottom: 32 }}>{label}</div>
      {children}
    </div>
  );
}

window.DesignSystem = DesignSystem;
