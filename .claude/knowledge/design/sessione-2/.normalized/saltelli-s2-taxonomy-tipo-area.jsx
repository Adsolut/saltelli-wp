/* global React, S2Header, S2Footer */
/* Sessione 2 R2 · /tipo-area/{slug}/ — TAXONOMY ARCHIVE
   Variabili term: privati / imprese / contenzioso / altri.
   Spec key: hero 8/4 con lawyer di riferimento dx · 3-col "Quando rivolgersi" ·
   lista .sl-area · 3 casi cluster · CTA editoriale
   Schema JSON-LD: CollectionPage + ItemList di LegalService */

function S2TaxonomyTipoArea({ term = "privati" }) {
  const dataset = {
    privati: {
      label: "Per i Privati",
      bread: "Home / Competenze / Per i Privati",
      lede: "Diritto di famiglia, eredità, lavoro, risarcimento danni, immigrazione, penale. Sei aree presidiate da quattro avvocati a Chiaia.",
      count: "9 aree di pratica",
      avvocati: [
        { name: "Antonia Battista", role: "Of Counsel · Famiglia LGBTQ+", slug: "antonia-battista" },
        { name: "Fabiana Saltelli", role: "Partner · Giuslavorista", slug: "fabiana-saltelli" },
      ],
      scenari: [
        { sym: "§", t: "Famiglia", d: "Separazioni, divorzi, affidamenti, unioni civili e tutela LGBTQ+." },
        { sym: "¶", t: "Eredità", d: "Successioni testate e legittime, divisioni, pubblicazione testamenti." },
        { sym: "†", t: "Risarcimento", d: "Danni da circolazione, malasanità, mobbing e responsabilità civile." },
      ],
      aree: [
        { num: "01", title: "Diritto di famiglia LGBTQ+", meta: "Tier 1 · 6 articoli · Battista", tier1: true },
        { num: "02", title: "Diritto di famiglia", meta: "Tier 2 · 4 articoli · Battista" },
        { num: "03", title: "Successioni ed eredità", meta: "Tier 2 · 3 articoli · Saltelli" },
        { num: "04", title: "Risarcimento danni", meta: "Tier 2 · 5 articoli · Saltelli" },
        { num: "05", title: "Diritto del lavoro", meta: "Tier 1 · 9 articoli · F. Saltelli", tier1: true },
        { num: "06", title: "Diritto immobiliare", meta: "Tier 2 · 2 articoli · Tedesco" },
        { num: "07", title: "Diritto condominiale", meta: "Tier 2 · 4 articoli · Tedesco" },
        { num: "08", title: "Penale", meta: "Tier 2 · 2 articoli · Saltelli" },
        { num: "09", title: "Immigrazione e cittadinanza", meta: "Tier 3 · 1 articolo · Battista" },
      ],
      casi: [
        { id: "Tribunale Famiglia · 2024", outcome: "Affido condiviso", desc: "Affidamento condiviso e mantenimento adeguato in separazione complessa con immobili in più province." },
        { id: "Tribunale di Napoli · 2023", outcome: "Riconoscimento", desc: "Primo riconoscimento in Campania di trascrizione integrale di atto di nascita di minore con due madri." },
        { id: "Cassazione · 2024", outcome: "Conferma", desc: "Conferma in Cassazione di sentenza favorevole in materia di licenziamento per giusta causa illegittimo." },
      ],
    },
  };
  const d = dataset[term] || dataset.privati;

  return (
    <div className="sl-root">
      <S2Header />

      {/* HERO 8/4 */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px) 80px",
        display: "grid", gridTemplateColumns: "8fr 4fr", gap: 96, alignItems: "end",
      }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 48 }}>{d.bread}</div>
          <h1 style={{
            fontSize: "clamp(64px, 8vw, 132px)",
            lineHeight: 0.95, letterSpacing: "-0.035em", fontWeight: 400, marginBottom: 32,
          }}>
            {d.label}.
          </h1>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 24, lineHeight: 1.5, color: "var(--text)", maxWidth: "48ch", marginBottom: 24,
          }}>
            {d.lede}
          </p>
          <div className="sl-mono">{d.count}</div>
        </div>

        <aside style={{ paddingBottom: 16 }}>
          <div className="sl-mono" style={{ marginBottom: 32, color: "var(--accent)" }}>Avvocati di riferimento</div>
          <div style={{ display: "grid", gap: 24 }}>
            {d.avvocati.map(a => (
              <a key={a.slug} href={`/avvocati/${a.slug}/`} style={{
                display: "grid", gridTemplateColumns: "80px 1fr", gap: 20, alignItems: "center",
                padding: "16px 0", borderTop: "1px solid var(--border)", textDecoration: "none", color: "inherit",
              }}>
                <div style={{
                  width: 80, height: 80, borderRadius: "50%",
                  background: "linear-gradient(135deg, color-mix(in srgb, var(--text-muted) 35%, var(--surface)) 0%, var(--text-muted) 100%)",
                  filter: "grayscale(1) contrast(1.05)",
                }} />
                <div>
                  <div style={{ fontFamily: "var(--font-display)", fontSize: 20, color: "var(--primary)", letterSpacing: "-0.01em", marginBottom: 4 }}>
                    {a.name}
                  </div>
                  <div className="sl-mono">{a.role} →</div>
                </div>
              </a>
            ))}
          </div>
        </aside>
      </section>

      {/* QUANDO RIVOLGERSI */}
      <section style={{ background: "var(--surface)", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto" }}>
          <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 64 }}>
            <div className="sl-mono">§ 01 — Quando rivolgersi</div>
            <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em", lineHeight: 1.05 }}>
              Tre scenari<br/>
              <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>tipici.</em>
            </h2>
          </div>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 32 }}>
            {d.scenari.map((s, i) => (
              <a key={i} href="#" style={{
                display: "block", padding: "32px 0",
                borderTop: "1px solid var(--accent)",
                color: "inherit", textDecoration: "none",
              }}>
                <div style={{
                  fontFamily: "var(--font-display)", fontStyle: "italic",
                  fontSize: 56, color: "var(--accent)", lineHeight: 1, marginBottom: 16,
                }}>{s.sym}</div>
                <h3 style={{ fontFamily: "var(--font-display)", fontSize: 28, color: "var(--primary)", marginBottom: 16, letterSpacing: "-0.015em" }}>
                  {s.t}
                </h3>
                <p style={{ fontSize: 16, lineHeight: 1.6, color: "var(--text)", marginBottom: 24 }}>
                  {s.d}
                </p>
                <span className="sl-mono" style={{ color: "var(--primary)" }}>Leggi →</span>
              </a>
            ))}
          </div>
        </div>
      </section>

      {/* LISTA AREE */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 56 }}>
          <div className="sl-mono">§ 02 — Aree di pratica</div>
          <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em" }}>
            Nove aree.
          </h2>
        </div>
        <div>
          {d.aree.map(a => (
            <a key={a.num} href="#" className={"sl-area" + (a.tier1 ? " sl-area--tier1" : "")}>
              <div className="sl-area__num">{a.num} / {String(d.aree.length).padStart(2, "0")}</div>
              <div className="sl-area__title">{a.title}</div>
              <div className="sl-area__meta">{a.meta}</div>
            </a>
          ))}
        </div>
      </section>

      {/* CASI CLUSTER */}
      <section style={{ background: "var(--surface)", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto" }}>
          <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 56 }}>
            <div className="sl-mono">§ 03 — Casi rappresentativi</div>
            <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em" }}>
              Tre vittorie per i privati.
            </h2>
          </div>
          <div>
            {d.casi.map((c, i) => (
              <div key={i} style={{
                display: "grid", gridTemplateColumns: "240px 1fr 200px",
                gap: 48, padding: "32px 0",
                borderBottom: "1px solid var(--border)", alignItems: "baseline",
              }}>
                <div className="sl-mono">{c.id}</div>
                <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 19, color: "var(--primary)", lineHeight: 1.45 }}>
                  {c.desc}
                </p>
                <div style={{ fontFamily: "var(--font-display)", fontSize: 24, color: "var(--accent)", textAlign: "right", letterSpacing: "-0.015em" }}>
                  {c.outcome}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA FINALE */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px) 160px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ 04 — Primo incontro</div>
          <div>
            <h2 style={{ fontSize: "clamp(56px, 6.5vw, 96px)", letterSpacing: "-0.025em", lineHeight: 0.98, marginBottom: 32 }}>
              Hai una pratica<br/>
              <em style={{ fontStyle: "italic", color: "var(--accent)" }}>simile?</em>
            </h2>
            <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 22, color: "var(--text)", lineHeight: 1.5, maxWidth: "44ch", marginBottom: 48 }}>
              Il primo incontro è gratuito. Riceviamo solo su appuntamento. Risposta entro 24 ore.
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

window.S2TaxonomyTipoArea = S2TaxonomyTipoArea;
