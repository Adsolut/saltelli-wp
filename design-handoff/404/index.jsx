/* global React, S2Header, S2Footer */
/* Sessione 2 R2 · 404.php — NOT FOUND EDITORIAL
   Spec key: hero "errore quietato" + drop-cap L · 3 card recovery ·
   5-7 aree mini · 3 articoli recenti · CTA prenota · tono calmo
   Schema JSON-LD: WebPage + isPartOf homepage */

function S2NotFound() {
  const [q, setQ] = React.useState("");

  const aree = [
    { num: "01", title: "Diritto tributario", meta: "Tier 1" },
    { num: "02", title: "Diritto del lavoro", meta: "Tier 1" },
    { num: "03", title: "Famiglia LGBTQ+", meta: "Tier 1" },
    { num: "04", title: "Cartelle esattoriali", meta: "Tier 2" },
    { num: "05", title: "Successioni ed eredità", meta: "Tier 2" },
    { num: "06", title: "Risarcimento danni", meta: "Tier 2" },
  ];

  const articoli = [
    { cat: "Diritto Tributario", title: "Cartelle esattoriali: cosa cambia con la riforma 2026", date: "16 Apr 2026", read: "8 min" },
    { cat: "Famiglia", title: "Trascrizione integrale di atto di nascita: il caso napoletano", date: "28 Mar 2026", read: "9 min" },
    { cat: "Cassazione", title: "Reati tributari: il ravvedimento operoso oltre soglia", date: "21 Mar 2026", read: "6 min" },
  ];

  return (
    <div className="sl-root">
      <S2Header />

      {/* HERO errore quietato */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "120px clamp(24px, 5vw, 96px) 96px",
      }}>
        <div className="sl-mono" style={{ marginBottom: 48, color: "var(--accent)" }}>
          Errore 404 · Pagina non trovata
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "5fr 7fr", gap: 96, alignItems: "end" }}>
          <h1 style={{
            fontSize: "clamp(72px, 9vw, 140px)",
            lineHeight: 0.95, letterSpacing: "-0.035em", fontWeight: 400,
          }}>
            La pagina<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>non esiste.</em>
          </h1>
          <div style={{ paddingBottom: 16, fontSize: 19, lineHeight: 1.7, color: "var(--text)", maxWidth: "44ch" }}>
            <p style={{ textIndent: 0 }}>
              <span style={{
                fontFamily: "var(--font-display)", fontStyle: "italic",
                fontSize: 96, float: "left", lineHeight: 0.85,
                marginRight: 16, marginTop: 8, color: "var(--primary)",
              }}>L</span>
              <span style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 22, lineHeight: 1.5 }}>
                a pagina che cercavi non esiste, o forse era qui e l'abbiamo spostata. Il diritto italiano è in continua evoluzione, anche le pagine.
              </span>
            </p>
          </div>
        </div>
      </section>

      {/* RECOVERY 3-col */}
      <section style={{ background: "var(--surface)", padding: "96px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto" }}>
          <div className="sl-mono" style={{ marginBottom: 48 }}>§ 01 — Cosa puoi fare</div>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 32 }}>
            {/* Card 1 — Home */}
            <div style={{ borderTop: "1px solid var(--accent)", paddingTop: 32 }}>
              <div className="sl-mono" style={{ marginBottom: 16 }}>01 · Torna alla home</div>
              <h3 style={{ fontFamily: "var(--font-display)", fontSize: 28, color: "var(--primary)", marginBottom: 16, letterSpacing: "-0.015em", lineHeight: 1.2 }}>
                Riparti dall'inizio.
              </h3>
              <p style={{ fontSize: 15, lineHeight: 1.6, color: "var(--text)", marginBottom: 32 }}>
                La homepage raccoglie tutte le 19 aree di pratica, gli avvocati e i casi rappresentativi.
              </p>
              <a href="/" className="sl-btn">
                Vai alla home<span className="arrow">→</span>
              </a>
            </div>

            {/* Card 2 — Search */}
            <div style={{ borderTop: "1px solid var(--accent)", paddingTop: 32 }}>
              <div className="sl-mono" style={{ marginBottom: 16 }}>02 · Cerca</div>
              <h3 style={{ fontFamily: "var(--font-display)", fontSize: 28, color: "var(--primary)", marginBottom: 16, letterSpacing: "-0.015em", lineHeight: 1.2 }}>
                Cerca quello<br/>che ti serviva.
              </h3>
              <form onSubmit={e => e.preventDefault()} style={{ marginTop: 32 }}>
                <input type="search" value={q} onChange={e => setQ(e.target.value)}
                  placeholder="Es. cartella, separazione, lavoro…"
                  style={{
                    width: "100%", border: 0,
                    borderBottom: "1px solid var(--primary)",
                    background: "transparent", padding: "8px 0",
                    fontFamily: "var(--font-body)", fontSize: 16,
                    color: "var(--primary)", outline: "none", marginBottom: 16,
                  }} />
                <button type="submit" className="sl-btn">
                  Cerca<span className="arrow">→</span>
                </button>
              </form>
            </div>

            {/* Card 3 — Contatti diretti */}
            <div style={{ borderTop: "1px solid var(--accent)", paddingTop: 32 }}>
              <div className="sl-mono" style={{ marginBottom: 16 }}>03 · Contatto diretto</div>
              <h3 style={{ fontFamily: "var(--font-display)", fontSize: 28, color: "var(--primary)", marginBottom: 16, letterSpacing: "-0.015em", lineHeight: 1.2 }}>
                Scrivici, chiamaci.
              </h3>
              <div style={{ display: "grid", gap: 0, marginTop: 32 }}>
                <a href="tel:+390812456789" style={{ display: "block", padding: "12px 0", borderTop: "1px solid var(--border)", textDecoration: "none", color: "var(--primary)" }}>
                  <div className="sl-mono">Telefono</div>
                  <div style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 17 }}>+39 081 245 67 89 →</div>
                </a>
                <a href="#" style={{ display: "block", padding: "12px 0", borderTop: "1px solid var(--border)", textDecoration: "none", color: "var(--primary)" }}>
                  <div className="sl-mono">WhatsApp</div>
                  <div style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 17 }}>Scrivi su WhatsApp →</div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* FORSE CERCAVI — aree */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px) 64px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 56 }}>
          <div className="sl-mono">§ 02 — Forse cercavi</div>
          <h2 style={{ fontSize: "clamp(36px, 4vw, 56px)", letterSpacing: "-0.02em", lineHeight: 1.05 }}>
            Una di queste<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>diciannove aree?</em>
          </h2>
        </div>
        <div>
          {aree.map(a => (
            <a key={a.num} href="#" className={"sl-area" + (a.meta === "Tier 1" ? " sl-area--tier1" : "")}>
              <div className="sl-area__num">{a.num} / 19</div>
              <div className="sl-area__title">{a.title}</div>
              <div className="sl-area__meta">{a.meta} →</div>
            </a>
          ))}
        </div>
        <div style={{ marginTop: 48, textAlign: "right" }}>
          <a href="/competenze/" className="sl-btn">
            Tutte le 19 aree<span className="arrow">→</span>
          </a>
        </div>
      </section>

      {/* ARTICOLI RECENTI */}
      <section style={{ background: "var(--surface)", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto" }}>
          <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 56 }}>
            <div className="sl-mono">§ 03 — Articoli recenti</div>
            <h2 style={{ fontSize: "clamp(36px, 4vw, 56px)", letterSpacing: "-0.02em" }}>
              Editoriale.
            </h2>
          </div>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 64 }}>
            {articoli.map((a, i) => (
              <a key={i} href="#" style={{ textDecoration: "none", color: "inherit", display: "grid", gap: 16 }}>
                <div style={{
                  aspectRatio: "4 / 3",
                  background: "linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%)",
                  filter: "grayscale(0.85) contrast(1.05)",
                  border: "1px solid var(--border)",
                }} />
                <div className="sl-mono">{a.cat}</div>
                <h3 style={{ fontFamily: "var(--font-display)", fontSize: 22, color: "var(--primary)", letterSpacing: "-0.015em", lineHeight: 1.25 }}>
                  {a.title}
                </h3>
                <div className="sl-mono" style={{ paddingTop: 8, borderTop: "1px solid var(--border)" }}>
                  {a.date} · {a.read}
                </div>
              </a>
            ))}
          </div>
        </div>
      </section>

      {/* CTA finale */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px) 160px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ Sempre presente</div>
          <div>
            <h2 style={{ fontSize: "clamp(56px, 6.5vw, 96px)", letterSpacing: "-0.025em", lineHeight: 0.98, marginBottom: 32 }}>
              Prenota una<br/>
              <em style={{ fontStyle: "italic", color: "var(--accent)" }}>consulenza gratuita.</em>
            </h2>
            <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 22, color: "var(--text)", lineHeight: 1.5, maxWidth: "44ch", marginBottom: 48 }}>
              Anche se sei capitato qui per errore: il primo incontro resta gratuito.
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

window.S2NotFound = S2NotFound;
