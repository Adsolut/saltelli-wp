/* global React, S2Header, S2Footer */
/* Sessione 2 · /competenze/{tier1}/ — PILLAR DEEP CLUSTER
   Esempio: diritto-tributario. Funziona per tutti i tier-1.
   Spec key: hero gigante eyebrow tier-1 · answer capsule serif italic 50w ·
   body editoriale drop-cap · avvocati riferimento · 3 casi outcome bronze ·
   FAQ accordion 5 dom · 3 articoli correlati · CTA finale
   Schema JSON-LD: LegalService + FAQPage + Article */

function S2PracticeTier1() {
  const [openFaq, setOpenFaq] = React.useState(0);

  const data = {
    eyebrow: "TIER 1 · Approfondimento · Per le imprese",
    h1: "Diritto tributario.",
    sub: "Cartelle, accertamenti, contenzioso.",
    capsule: "Lo Studio Saltelli rappresenta imprese, professionisti e privati nei contenziosi tributari di ogni ordine e grado, con particolare attenzione agli accertamenti sintetici, alle cartelle esattoriali e ai reati tributari connessi.",
    avvocato: { name: "Emiliano Saltelli", role: "Founding Partner · Tributarista · Cassazionista", slug: "emiliano-saltelli" },
    casi: [
      { id: "vs. AGE Riscossione · 2024", outcome: "€240.000", lbl: "Annullamento", desc: "Annullamento integrale di cartella esattoriale a carico di società in liquidazione." },
      { id: "Cassazione · 2023", outcome: "−80%", lbl: "Riforma", desc: "Riforma di accertamento sintetico in Corte di Cassazione." },
      { id: "CTR Campania · 2022", outcome: "€87.000", lbl: "Vittoria", desc: "Riconoscimento di credito IVA contestato dall'Agenzia." },
    ],
    faq: [
      { q: "Quando conviene impugnare una cartella esattoriale?", a: "Le cartelle vanno impugnate entro 60 giorni dalla notifica davanti alla Commissione Tributaria competente. Lo Studio valuta gratuitamente la fondatezza dell'impugnazione nel primo incontro." },
      { q: "Cosa fare se l'Agenzia delle Entrate avvia un accertamento sintetico?", a: "Prima dell'accertamento si apre un contraddittorio preventivo: è la fase più delicata. Documentare correttamente la propria posizione in questa sede può evitare il contenzioso." },
      { q: "Quali sono i tempi medi di un contenzioso tributario?", a: "Primo grado in CTP: 12-18 mesi. Appello in CTR: ulteriori 18-24 mesi. Cassazione: 24-36 mesi. La sospensione cautelare è quasi sempre concedibile." },
      { q: "Si possono rateizzare le somme dovute?", a: "Sì, fino a 72 rate mensili (120 in casi di grave difficoltà). Lo Studio assiste anche nella negoziazione dei piani di rateizzazione con AGE Riscossione." },
      { q: "Quanto costa un contenzioso tributario?", a: "Il primo incontro è gratuito. Il preventivo è scritto, fisso o a percentuale del beneficio. Le parcelle seguono i parametri ministeriali, sempre concordate prima del mandato." },
    ],
    correlati: [
      { d: "16 Apr 2026", t: "Cartelle esattoriali: cosa cambia con la riforma 2026", t2: "Tributario · 8 min" },
      { d: "02 Apr 2026", t: "Accertamento sintetico: la sentenza che ribalta tutto", t2: "Tributario · 12 min" },
      { d: "21 Mar 2026", t: "Reati tributari: il ravvedimento operoso oltre soglia", t2: "Tributario · Penale · 6 min" },
    ],
  };

  return (
    <div className="sl-root">
      <S2Header />

      {/* HERO gigante */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "120px clamp(24px, 5vw, 96px) 80px",
      }}>
        <div className="sl-mono" style={{ marginBottom: 64, color: "var(--accent)" }}>
          {data.eyebrow}
        </div>
        <h1 style={{
          fontSize: "clamp(72px, 10vw, 160px)",
          lineHeight: 0.95, letterSpacing: "-0.035em", fontWeight: 400, marginBottom: 32,
        }}>
          {data.h1}
        </h1>
        <h2 style={{
          fontFamily: "var(--font-display)", fontStyle: "italic",
          fontSize: "clamp(28px, 3vw, 44px)", color: "var(--text-muted)",
          fontWeight: 400, letterSpacing: "-0.015em", marginBottom: 64,
        }}>
          {data.sub}
        </h2>

        {/* Answer capsule — primi 100 parole, serif italic, GEO-ready */}
        <div style={{
          maxWidth: "60ch", marginLeft: "20%",
          padding: "32px 0", borderTop: "1px solid var(--accent)", borderBottom: "1px solid var(--accent)",
        }}>
          <div className="sl-mono" style={{ marginBottom: 16, color: "var(--accent)" }}>Risposta in 50 parole</div>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 24, lineHeight: 1.5, color: "var(--primary)",
            letterSpacing: "-0.005em",
          }}>
            {data.capsule}
          </p>
        </div>
      </section>

      {/* BODY editoriale con drop-cap */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "80px clamp(24px, 5vw, 96px) 128px",
        display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64,
      }}>
        <div className="sl-mono" style={{ position: "sticky", top: 120, alignSelf: "start" }}>§ 01 — Materia</div>
        <article style={{ maxWidth: "62ch" }}>
          <div style={{ fontSize: 19, lineHeight: 1.75, color: "var(--text)", display: "grid", gap: 24 }}>
            <p style={{ textIndent: 0 }}>
              <span style={{
                fontFamily: "var(--font-display)", fontStyle: "italic",
                fontSize: 96, float: "left", lineHeight: 0.85,
                marginRight: 16, marginTop: 10, color: "var(--primary)",
              }}>I</span>
              l diritto tributario è la materia di formazione di Emiliano Saltelli, fondatore dello Studio.
              In oltre vent'anni di pratica, lo Studio ha rappresentato imprese, professionisti e privati cittadini
              in oltre quattrocento contenziosi davanti alle Commissioni Tributarie e alla Corte di Cassazione.
            </p>
            <p>
              Ci occupiamo di tutta la filiera del contenzioso: dalla fase pre-accertamento (contraddittorio preventivo,
              istanze di autotutela, accertamento con adesione) alle impugnazioni in primo e secondo grado, fino al
              giudizio di legittimità in Cassazione. Seguiamo anche le procedure esecutive — cartelle, ipoteche,
              fermi amministrativi, pignoramenti.
            </p>
          </div>

          <h2 style={{ marginTop: 80, marginBottom: 24, fontSize: "clamp(28px, 3vw, 36px)", letterSpacing: "-0.015em" }}>
            Cartelle esattoriali.
          </h2>
          <p style={{ fontSize: 18, lineHeight: 1.75, color: "var(--text)", marginBottom: 16 }}>
            Le cartelle vanno impugnate entro sessanta giorni dalla notifica. Lo Studio valuta gratuitamente la
            fondatezza dell'impugnazione, redige il ricorso, lo deposita in CTP e segue il giudizio in tutti i suoi gradi.
          </p>
          <p style={{ fontSize: 18, lineHeight: 1.75, color: "var(--text)" }}>
            Quando l'impugnazione non è fondata, lo diciamo. Quando lo è, otteniamo l'annullamento o la sospensione
            cautelare immediata.
          </p>

          <h2 style={{ marginTop: 80, marginBottom: 24, fontSize: "clamp(28px, 3vw, 36px)", letterSpacing: "-0.015em" }}>
            Accertamenti sintetici.
          </h2>
          <p style={{ fontSize: 18, lineHeight: 1.75, color: "var(--text)" }}>
            Il redditometro è uno strumento delicato: presunzione legale relativa, ma con onere probatorio invertito.
            Documentare correttamente la propria posizione nel contraddittorio preventivo è quasi sempre la differenza
            fra la chiusura immediata della pratica e anni di contenzioso.
          </p>
        </article>
      </section>

      {/* AVVOCATO DI RIFERIMENTO */}
      <section style={{
        background: "var(--surface)",
        padding: "96px clamp(24px, 5vw, 96px)",
      }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ 02 — Avvocato di riferimento</div>
          <a href={`/avvocati/${data.avvocato.slug}/`} style={{
            display: "grid", gridTemplateColumns: "200px 1fr",
            gap: 32, color: "inherit", textDecoration: "none",
            padding: "32px", border: "1px solid var(--border)", background: "var(--background)",
          }}>
            <div style={{
              aspectRatio: "1 / 1",
              background: "linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%)",
              filter: "grayscale(1) contrast(1.05)",
            }} />
            <div>
              <div className="sl-mono" style={{ marginBottom: 12 }}>{data.avvocato.role}</div>
              <h3 style={{ fontFamily: "var(--font-display)", fontSize: 40, color: "var(--primary)", marginBottom: 16, letterSpacing: "-0.02em" }}>
                {data.avvocato.name}
              </h3>
              <p style={{ fontSize: 15, color: "var(--text-muted)", lineHeight: 1.65, marginBottom: 16, maxWidth: "50ch" }}>
                Cassazionista dal 2014. Oltre 400 contenziosi tributari, riconoscimenti su Diritto.it e Sole 24 Ore.
              </p>
              <span className="sl-mono" style={{ color: "var(--primary)" }}>Profilo completo →</span>
            </div>
          </a>
        </div>
      </section>

      {/* CASI TIER-1 */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 64 }}>
          <div className="sl-mono">§ 03 — Casi tier-1</div>
          <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em", lineHeight: 1.05 }}>
            Tre vittorie<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>recenti.</em>
          </h2>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 32 }}>
          {data.casi.map((c, i) => (
            <div key={i} style={{ borderTop: "1px solid var(--accent)", paddingTop: 24 }}>
              <div className="sl-mono" style={{ marginBottom: 16 }}>{c.id}</div>
              <div style={{
                fontFamily: "var(--font-display)", fontStyle: "italic",
                fontSize: 56, color: "var(--accent)", lineHeight: 1, letterSpacing: "-0.02em",
                marginBottom: 8,
              }}>{c.outcome}</div>
              <div className="sl-mono" style={{ marginBottom: 24 }}>{c.lbl}</div>
              <p style={{ fontSize: 16, lineHeight: 1.6, color: "var(--text)" }}>{c.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* FAQ accordion */}
      <section style={{ background: "var(--surface)", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ 04 — Domande frequenti</div>
          <div>
            <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em", marginBottom: 48 }}>
              Cinque domande.
            </h2>
            <div className="sl-acc">
              {data.faq.map((f, i) => (
                <div key={i} className="sl-acc__item" data-open={openFaq === i}>
                  <button className="sl-acc__btn" onClick={() => setOpenFaq(openFaq === i ? -1 : i)}>
                    <span>{f.q}</span>
                    <span className="sl-acc__icon">+</span>
                  </button>
                  <div className="sl-acc__panel">
                    <div className="sl-acc__inner">{f.a}</div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* CORRELATI */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 64 }}>
          <div className="sl-mono">§ 05 — Articoli correlati</div>
          <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em" }}>
            Editoriale.
          </h2>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 32 }}>
          {data.correlati.map((a, i) => (
            <a key={i} href="#" style={{ color: "inherit", textDecoration: "none", borderTop: "1px solid var(--border)", paddingTop: 24 }}>
              <div className="sl-mono" style={{ marginBottom: 24 }}>{a.d}</div>
              <h3 style={{ fontFamily: "var(--font-display)", fontSize: 26, color: "var(--primary)", marginBottom: 24, letterSpacing: "-0.015em", lineHeight: 1.2 }}>
                {a.t}
              </h3>
              <div className="sl-mono">{a.t2} →</div>
            </a>
          ))}
        </div>
      </section>

      {/* CTA finale */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "96px clamp(24px, 5vw, 96px) 160px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ 06 — Hai una pratica simile?</div>
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

window.S2PracticeTier1 = S2PracticeTier1;
