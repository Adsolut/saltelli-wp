/* global React */
/* Saltelli & Partners — Sessione 2 · Chrome condiviso
   Header (variante A logo orizzontale v1.1) + Footer (variante C stack)
   Importato da ognuna delle 5 pagine. */

function S2Header() {
  const [scrolled, setScrolled] = React.useState(false);
  React.useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40);
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  const nav = [
    { l: "Studio", h: "/chi-siamo/" },
    { l: "Avvocati", h: "/avvocati/" },
    { l: "Competenze", h: "/competenze/" },
    { l: "Casi", h: "/casi/" },
    { l: "Editoriale", h: "/editoriale/" },
    { l: "Contatti", h: "/contatti/" },
  ];

  return (
    <header style={{
      position: "sticky", top: 0, zIndex: 50,
      background: scrolled ? "var(--background)" : "transparent",
      borderBottom: scrolled ? "1px solid var(--border)" : "1px solid transparent",
      transition: "all 300ms var(--ease-editorial)",
    }}>
      <div style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "20px clamp(24px, 5vw, 96px)",
        display: "grid", gridTemplateColumns: "auto 1fr auto", gap: 48, alignItems: "center",
      }}>
        {/* Logo orizzontale v1.1 */}
        <a href="/" style={{
          display: "inline-grid",
          gridTemplateColumns: "auto 1px auto",
          alignItems: "center", gap: 16, color: "var(--primary)",
          textDecoration: "none", whiteSpace: "nowrap",
        }}>
          <div style={{ display: "grid", justifyItems: "end", gap: 2 }}>
            <span style={{
              fontFamily: "var(--font-body)", fontWeight: 500,
              fontSize: 9, letterSpacing: "0.32em", textTransform: "uppercase",
            }}>Studio Legale</span>
            <span style={{
              fontFamily: "var(--font-mono)", fontSize: 8.5,
              letterSpacing: "0.24em", textTransform: "uppercase",
              color: "var(--text-muted)",
            }}>Napoli · 1999</span>
          </div>
          <div style={{ width: 1, height: 32, background: "var(--primary)" }} />
          <div style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 28, lineHeight: 1, letterSpacing: "-0.02em",
          }}>
            <span style={{ color: "var(--accent)" }}>S</span>altelli
          </div>
        </a>

        <nav style={{ display: "flex", gap: 36, justifyContent: "center", fontSize: 14, fontWeight: 500 }}>
          {nav.map(i => (
            <a key={i.l} href={i.h} className="sl-link" style={{ borderBottomColor: "transparent" }}>{i.l}</a>
          ))}
        </nav>

        <a href="tel:+390812456789" className="sl-mono" style={{ color: "var(--primary)", fontSize: 11 }}>
          +39 081 245 67 89
        </a>
      </div>
    </header>
  );
}

function S2Footer() {
  const areas = [
    "Diritto tributario", "Diritto del lavoro", "Famiglia LGBTQ+",
    "Diritto di famiglia", "Condominiale", "Immobiliare",
    "Societario", "Contenzioso civile", "Penale dell'economia",
    "Penale", "Bancario", "Successioni",
    "Amministrativo", "Recupero crediti", "Risarcimento danni",
    "Privacy & GDPR", "Domiciliazioni", "Volontaria giurisdizione", "Esecuzione",
  ];

  return (
    <footer style={{ background: "var(--primary)", color: "var(--background)", padding: "96px clamp(24px, 5vw, 96px) 32px" }}>
      <div style={{ maxWidth: 1440, margin: "0 auto" }}>
        <div style={{ display: "grid", gridTemplateColumns: "2fr 4fr 2fr", gap: 64, paddingBottom: 64, borderBottom: "1px solid rgba(255,255,255,0.15)" }}>
          {/* Variante C logo stack */}
          <div>
            <div style={{ display: "grid", justifyItems: "start", gap: 4, marginBottom: 32 }}>
              <span style={{
                fontFamily: "var(--font-body)", fontWeight: 500,
                fontSize: 11, letterSpacing: "0.42em", textTransform: "uppercase",
                color: "var(--background)",
              }}>Studio Legale</span>
              <span style={{
                fontFamily: "var(--font-display)", fontWeight: 400,
                fontStyle: "italic", fontSize: 48, lineHeight: 0.95,
                letterSpacing: "-0.02em",
              }}>
                <span style={{ color: "var(--accent)" }}>S</span>altelli
              </span>
              <span style={{
                fontFamily: "var(--font-mono)", fontSize: 10,
                letterSpacing: "0.24em", textTransform: "uppercase",
                color: "rgba(255,255,255,0.55)", marginTop: 4,
              }}>Napoli · Dal 1999</span>
            </div>
            <div style={{ fontSize: 13, lineHeight: 1.85, opacity: 0.7, display: "grid", gap: 4 }}>
              <a href="/chi-siamo/" style={{ color: "inherit" }}>Lo studio</a>
              <a href="/avvocati/" style={{ color: "inherit" }}>Avvocati</a>
              <a href="/casi/" style={{ color: "inherit" }}>Casi</a>
              <a href="/editoriale/" style={{ color: "inherit" }}>Editoriale</a>
              <a href="/contatti/" style={{ color: "inherit" }}>Contatti</a>
            </div>
          </div>

          <div>
            <div className="sl-mono" style={{ color: "rgba(255,255,255,0.6)", marginBottom: 24 }}>Diciannove aree</div>
            <div style={{ columnCount: 3, columnGap: 32, fontSize: 13, lineHeight: 2, opacity: 0.85 }}>
              {areas.map(a => (
                <a key={a} href="#" style={{ color: "inherit", display: "block", breakInside: "avoid", transition: "color 200ms" }}>{a}</a>
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

        <div style={{
          display: "flex", justifyContent: "space-between", alignItems: "center",
          paddingTop: 32, fontSize: 11, fontFamily: "var(--font-mono)",
          letterSpacing: "0.08em", textTransform: "uppercase", opacity: 0.55,
        }}>
          <div>© 2026 Studio Legale Saltelli &amp; Partners</div>
          <div style={{ display: "flex", gap: 32 }}>
            <a href="/privacy" style={{ color: "inherit" }}>Privacy</a>
            <a href="/cookie" style={{ color: "inherit" }}>Cookie</a>
            <a href="#" style={{ color: "inherit" }}>Instagram</a>
            <a href="#" style={{ color: "inherit" }}>LinkedIn</a>
          </div>
        </div>
      </div>
    </footer>
  );
}

window.S2Header = S2Header;
window.S2Footer = S2Footer;
