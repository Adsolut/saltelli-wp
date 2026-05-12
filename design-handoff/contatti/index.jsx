/* global React, S2Header, S2Footer */
/* Sessione 2 · /contatti/ — CONVERSION BOTTOM FUNNEL
   Spec key: hero · 2-col asimmetrico (form 8fr | NAP+map 4fr) ·
   field underline-only · OSM map 320px · come arrivare ·
   trust signal "risposta entro 24h"
   Schema JSON-LD: LegalService + ContactPoint + LocalBusiness */

function S2Contatti() {
  const [submitted, setSubmitted] = React.useState(false);
  const [form, setForm] = React.useState({
    nome: "", email: "", telefono: "",
    area: "", data: "", messaggio: "", gdpr: false,
  });

  const aree = [
    "Diritto tributario", "Diritto del lavoro", "Famiglia LGBTQ+",
    "Diritto di famiglia", "Condominiale", "Immobiliare",
    "Societario", "Contenzioso civile", "Penale dell'economia",
    "Penale", "Bancario", "Successioni",
    "Amministrativo", "Recupero crediti", "Risarcimento danni",
    "Privacy & GDPR", "Domiciliazioni", "Volontaria giurisdizione", "Esecuzione",
  ];

  const onSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

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
          <div className="sl-mono" style={{ marginBottom: 48 }}>§ Contatti · Primo incontro gratuito</div>
          <h1 style={{
            fontSize: "clamp(72px, 9vw, 140px)",
            lineHeight: 0.95, letterSpacing: "-0.035em", fontWeight: 400,
          }}>
            Contatti.
          </h1>
        </div>
        <div style={{ paddingBottom: 24 }}>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 28, lineHeight: 1.4, color: "var(--text)", maxWidth: "32ch",
          }}>
            Chiedi qualsiasi cosa.<br/>
            <span style={{ color: "var(--accent)" }}>In qualsiasi momento.</span>
          </p>
        </div>
      </section>

      {/* 2-col asimmetrico: form sx 8fr | NAP+map dx 4fr */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "64px clamp(24px, 5vw, 96px) 128px",
        display: "grid", gridTemplateColumns: "8fr 4fr", gap: 96, alignItems: "start",
      }}>
        {/* FORM */}
        <div>
          <div className="sl-mono" style={{ marginBottom: 24 }}>§ 01 — Modulo</div>
          <h2 style={{ fontSize: "clamp(36px, 4vw, 56px)", marginBottom: 48, letterSpacing: "-0.02em" }}>
            Prenota un primo<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>incontro gratuito.</em>
          </h2>

          {submitted ? (
            <div style={{
              padding: "64px 0",
              borderTop: "1px solid var(--accent)", borderBottom: "1px solid var(--accent)",
            }}>
              <div className="sl-mono" style={{ color: "var(--accent)", marginBottom: 24 }}>Richiesta ricevuta</div>
              <h3 style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 40, color: "var(--primary)", lineHeight: 1.2, marginBottom: 24, letterSpacing: "-0.015em" }}>
                Grazie. Ci sentiamo entro 24 ore.
              </h3>
              <p style={{ fontSize: 17, lineHeight: 1.65, color: "var(--text)", maxWidth: "50ch" }}>
                Lo Studio risponde personalmente a ogni richiesta. Per urgenze tributarie con scadenze imminenti,
                può chiamare direttamente Emiliano al <a href="tel:+390812456789" className="sl-link">+39 081 245 67 89</a>.
              </p>
            </div>
          ) : (
            <form onSubmit={onSubmit} style={{ display: "grid", gap: 40 }}>
              <FormField label="Nome e cognome *" required>
                <input type="text" required value={form.nome} onChange={e => setForm({ ...form, nome: e.target.value })} className="sl-input" />
              </FormField>

              <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 40 }}>
                <FormField label="Email *" required>
                  <input type="email" required value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} className="sl-input" />
                </FormField>
                <FormField label="Telefono">
                  <input type="tel" value={form.telefono} onChange={e => setForm({ ...form, telefono: e.target.value })} className="sl-input" />
                </FormField>
              </div>

              <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 40 }}>
                <FormField label="Area di interesse">
                  <select value={form.area} onChange={e => setForm({ ...form, area: e.target.value })} className="sl-input">
                    <option value="">— Seleziona —</option>
                    {aree.map(a => <option key={a} value={a}>{a}</option>)}
                  </select>
                </FormField>
                <FormField label="Data preferita">
                  <input type="date" value={form.data} onChange={e => setForm({ ...form, data: e.target.value })} className="sl-input" />
                </FormField>
              </div>

              <FormField label="Messaggio *" required>
                <textarea required rows={5} value={form.messaggio} onChange={e => setForm({ ...form, messaggio: e.target.value })} className="sl-input" style={{ resize: "vertical" }} />
              </FormField>

              <label style={{ display: "flex", gap: 16, alignItems: "flex-start", cursor: "pointer", paddingTop: 8 }}>
                <input type="checkbox" required checked={form.gdpr} onChange={e => setForm({ ...form, gdpr: e.target.checked })} style={{ marginTop: 4, accentColor: "var(--accent)" }} />
                <span style={{ fontSize: 13, color: "var(--text-muted)", lineHeight: 1.65, maxWidth: "60ch" }}>
                  Consento il trattamento dei dati personali ai sensi del Reg. UE 2016/679 (GDPR), per le finalità descritte nell'<a href="/privacy" className="sl-link">informativa privacy</a>. *
                </span>
              </label>

              <div style={{ paddingTop: 16 }}>
                <button type="submit" className="sl-btn sl-btn--primary">
                  Prenota gratuita
                  <span className="arrow">→</span>
                </button>
              </div>
            </form>
          )}

          <style>{`
            .sl-input {
              width: 100%;
              border: 0;
              border-bottom: 1px solid var(--border);
              padding: 12px 0;
              font-family: var(--font-body);
              font-size: 17px;
              color: var(--primary);
              background: transparent;
              outline: none;
              transition: border-color 200ms var(--ease-editorial);
            }
            .sl-input:focus {
              border-bottom-color: var(--accent);
            }
            select.sl-input {
              appearance: none;
              cursor: pointer;
              background-image: linear-gradient(45deg, transparent 50%, var(--text-muted) 50%), linear-gradient(135deg, var(--text-muted) 50%, transparent 50%);
              background-position: calc(100% - 16px) 50%, calc(100% - 11px) 50%;
              background-size: 5px 5px, 5px 5px;
              background-repeat: no-repeat;
              padding-right: 32px;
            }
          `}</style>
        </div>

        {/* NAP + MAP + ORARI dx */}
        <aside style={{ display: "grid", gap: 32 }}>
          <div className="sl-mono">§ 02 — Studio</div>

          {/* NAP */}
          <div>
            <div className="sl-mono" style={{ marginBottom: 12 }}>Indirizzo</div>
            <div style={{ fontFamily: "var(--font-display)", fontSize: 22, lineHeight: 1.3, color: "var(--primary)", letterSpacing: "-0.01em" }}>
              Via Vannella<br/>Gaetani, 27<br/>80121 Napoli — Chiaia
            </div>
          </div>

          {/* MAP — OpenStreetMap embed */}
          <div style={{
            height: 320,
            border: "1px solid var(--border)",
            position: "relative",
            background: "var(--surface)",
            overflow: "hidden",
          }}>
            <iframe
              title="Studio Saltelli — Via Vannella Gaetani 27"
              width="100%" height="100%" frameBorder="0" scrolling="no"
              style={{ filter: "grayscale(0.85) contrast(1.05)", border: 0 }}
              src="https://www.openstreetmap.org/export/embed.html?bbox=14.235%2C40.829%2C14.245%2C40.834&layer=mapnik&marker=40.8316%2C14.2400"
            />
            <div style={{
              position: "absolute", top: 12, left: 12,
              background: "var(--background)", padding: "6px 10px",
              border: "1px solid var(--border)",
            }} className="sl-mono">
              Chiaia · Napoli
            </div>
          </div>

          {/* CTA dirette */}
          <div style={{ display: "grid", gap: 0 }}>
            {[
              { l: "Telefono", v: "+39 081 245 67 89", h: "tel:+390812456789" },
              { l: "Email", v: "studio@saltellipartners.it", h: "mailto:studio@saltellipartners.it" },
              { l: "WhatsApp", v: "Scrivi su WhatsApp", h: "#" },
            ].map(c => (
              <a key={c.l} href={c.h} style={{
                display: "block", padding: "16px 0",
                borderTop: "1px solid var(--border)",
                color: "var(--primary)", textDecoration: "none",
              }}>
                <div className="sl-mono" style={{ marginBottom: 4 }}>{c.l}</div>
                <div style={{ fontFamily: "var(--font-display)", fontSize: 18, fontStyle: "italic", letterSpacing: "-0.005em" }}>
                  {c.v} →
                </div>
              </a>
            ))}
          </div>

          {/* Orari */}
          <div style={{ paddingTop: 16, borderTop: "1px solid var(--border)" }}>
            <div className="sl-mono" style={{ marginBottom: 12 }}>Orari</div>
            <div style={{ fontSize: 14, lineHeight: 1.85, color: "var(--text)" }}>
              Lun – Ven · 09:30 – 18:30<br/>
              Sabato su appuntamento
            </div>
          </div>
        </aside>
      </section>

      {/* COME ARRIVARE */}
      <section style={{ background: "var(--surface)", padding: "96px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ 03 — Come arrivare</div>
          <div>
            <h2 style={{ fontSize: "clamp(36px, 4vw, 56px)", marginBottom: 48, letterSpacing: "-0.02em" }}>
              Come arrivare.
            </h2>
            <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 48 }}>
              {[
                { l: "Metro", t: "Linea 6 · Mergellina", d: "8 minuti a piedi lungo la Riviera di Chiaia" },
                { l: "Auto", t: "Parcheggio Mergellina", d: "Sosta a pagamento, 5 minuti a piedi" },
                { l: "Treno", t: "Napoli Mergellina", d: "Stazione FS, 10 minuti a piedi" },
              ].map(x => (
                <div key={x.l} style={{ borderTop: "1px solid var(--border)", paddingTop: 24 }}>
                  <div className="sl-mono" style={{ marginBottom: 16 }}>{x.l}</div>
                  <h3 style={{ fontFamily: "var(--font-display)", fontSize: 22, color: "var(--primary)", marginBottom: 8, letterSpacing: "-0.01em" }}>
                    {x.t}
                  </h3>
                  <p style={{ fontSize: 14, color: "var(--text-muted)", lineHeight: 1.6 }}>{x.d}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* TRUST SIGNAL */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "96px clamp(24px, 5vw, 96px) 160px" }}>
        <div style={{
          padding: "64px 0", borderTop: "1px solid var(--border)", borderBottom: "1px solid var(--border)",
          textAlign: "center",
        }}>
          <div className="sl-mono" style={{ marginBottom: 24, color: "var(--accent)" }}>Promessa di servizio</div>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: "clamp(28px, 3.5vw, 48px)", lineHeight: 1.3,
            color: "var(--primary)", letterSpacing: "-0.015em",
            maxWidth: "32ch", margin: "0 auto",
          }}>
            Riceviamo solo<br/>su appuntamento.<br/>
            <span style={{ color: "var(--text-muted)" }}>Risposta entro 24 ore.</span>
          </p>
        </div>
      </section>

      <S2Footer />
    </div>
  );
}

function FormField({ label, children }) {
  return (
    <label style={{ display: "block" }}>
      <span style={{
        display: "block",
        fontFamily: "var(--font-mono)", fontSize: 11,
        letterSpacing: "0.08em", textTransform: "uppercase",
        color: "var(--text-muted)", marginBottom: 8,
      }}>{label}</span>
      {children}
    </label>
  );
}

window.S2Contatti = S2Contatti;
