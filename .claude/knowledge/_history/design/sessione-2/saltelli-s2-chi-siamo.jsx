/* global React, S2Header, S2Footer */
/* Sessione 2 · /chi-siamo/ — ABOUT EDITORIAL
   Spec key: hero asimmetrico drop-cap "U" · Plate I facciata · 4 lawyer card mini ·
   timeline 1999→2026 · CTA prenota
   Schema JSON-LD: AboutPage + Organization (vedi commento <head>) */

function S2ChiSiamo() {
  const lawyers = [
    { slug: "emiliano-saltelli", name: "Emiliano Saltelli", role: "Founding Partner · Tributarista", spec: "Tributario · Cartelle · Contenzioso" },
    { slug: "fabiana-saltelli", name: "Fabiana Saltelli", role: "Partner · Giuslavorista", spec: "Lavoro · Contenzioso INPS" },
    { slug: "antonia-battista", name: "Antonia Battista", role: "Of Counsel · Famiglia LGBTQ+", spec: "Unioni civili · Genitorialità" },
    { slug: "stefano-tedesco", name: "Stefano G. Tedesco", role: "Associate", spec: "Condominiale · Immobiliare" },
  ];

  const timeline = [
    { y: "1999", t: "Fondazione", d: "Emiliano Saltelli apre lo studio in Via Vannella Gaetani, focalizzato sul contenzioso tributario." },
    { y: "2007", t: "Ingresso di Fabiana", d: "Si aggiunge la prima associate — area diritto del lavoro." },
    { y: "2014", t: "Apertura LGBTQ+", d: "Antonia Battista inaugura una pratica dedicata, prima a Napoli sud." },
    { y: "2019", t: "Vent'anni", d: "Lo studio passa da 2 a 4 professionisti stabili. Bottega a tutti gli effetti." },
    { y: "2024", t: "Cassazione + AGE", d: "Annullamento cartella €240k. Conferma in Cassazione su licenziamento illegittimo." },
    { y: "2026", t: "Oggi", d: "19 aree presidiate, 4 professionisti, una sola bottega." },
  ];

  return (
    <div className="sl-root" style={{ background: "var(--background)" }}>
      <S2Header />

      {/* HERO — asimmetrico, drop-cap U */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "120px clamp(24px, 5vw, 96px) 80px",
        display: "grid", gridTemplateColumns: "5fr 7fr", gap: 64, alignItems: "end",
      }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 48 }}>§ Lo studio · Chi siamo</div>
          <div style={{
            fontFamily: "var(--font-mono)", fontSize: 11,
            letterSpacing: "0.08em", textTransform: "uppercase",
            color: "var(--text-muted)", lineHeight: 2,
          }}>
            Una bottega<br/>
            di quattro avvocati<br/>
            in Via Vannella Gaetani 27<br/>
            Chiaia · Napoli<br/>
            Dal 1999
          </div>
        </div>
        <h1 style={{
          fontSize: "clamp(56px, 7vw, 104px)",
          lineHeight: 0.98, letterSpacing: "-0.03em", fontWeight: 400,
        }}>
          Una atelier<br/>
          di quattro<br/>
          <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>professionisti.</em>
        </h1>
      </section>

      {/* LEDE editoriale — drop-cap U */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "0 clamp(24px, 5vw, 96px) 128px",
        display: "grid", gridTemplateColumns: "3fr 7fr 2fr", gap: 64,
      }}>
        <div className="sl-mono" style={{ paddingTop: 12 }}>§ 01 — Lede</div>
        <div style={{ fontSize: 19, lineHeight: 1.75, color: "var(--text)", display: "grid", gap: 24 }}>
          <p style={{ textIndent: 0 }}>
            <span style={{
              fontFamily: "var(--font-display)", fontStyle: "italic",
              fontSize: 96, float: "left", lineHeight: 0.85,
              marginRight: 18, marginTop: 10, color: "var(--primary)",
            }}>U</span>
            n atelier di quattro professionisti che da oltre vent'anni accompagna famiglie e imprese di Napoli
            attraverso le materie di cui si occupa: il diritto tributario di Emiliano, il diritto del lavoro di
            Fabiana, la tutela LGBTQ+ in materia di famiglia di Antonia, il condominiale e immobiliare di Stefano.
          </p>
          <p>
            Crediamo che il diritto sia, prima di tutto, un'arte di ascolto. Le carte vengono dopo. Per questo
            non offriamo pacchetti né formule: ogni cliente è una storia, e ogni storia merita il tempo di essere capita.
          </p>
        </div>
        <div />
      </section>

      {/* PLATE I — Facciata Vannella Gaetani */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "0 clamp(24px, 5vw, 96px) 128px" }}>
        <div style={{
          height: 560, background: "var(--surface)",
          border: "1px solid var(--border)",
          display: "flex", alignItems: "center", justifyContent: "center",
          position: "relative", overflow: "hidden",
        }}>
          <div style={{
            position: "absolute", inset: 0,
            background: "linear-gradient(180deg, #d4d0c5 0%, #9c958a 60%, #6b6660 100%)",
            opacity: 0.55,
          }} />
          <div style={{ position: "absolute", top: 24, left: 32 }} className="sl-mono">Plate I · Facciata studio</div>
          <div style={{ position: "absolute", bottom: 24, right: 32 }} className="sl-mono">Foto B/N · 1440 × 560 · placeholder</div>
          <div style={{ position: "relative", textAlign: "center" }}>
            <div style={{
              fontFamily: "var(--font-display)", fontSize: 40, fontStyle: "italic",
              color: "var(--background)", marginBottom: 8,
            }}>
              Via Vannella Gaetani, 27
            </div>
            <div className="sl-mono" style={{ color: "rgba(255,255,255,0.85)" }}>
              Palazzo nobiliare · Chiaia · Napoli
            </div>
          </div>
        </div>
      </section>

      {/* SECTION 1 — Founding 1999 */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "0 clamp(24px, 5vw, 96px) 128px", display: "grid", gridTemplateColumns: "3fr 7fr 2fr", gap: 64 }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 16 }}>§ 02 — 1999</div>
          <div style={{
            fontFamily: "var(--font-display)", fontSize: 64, fontStyle: "italic",
            color: "var(--accent)", lineHeight: 1, letterSpacing: "-0.02em",
          }}>1999.</div>
        </div>
        <div>
          <h2 style={{ fontSize: "clamp(32px, 3.5vw, 48px)", marginBottom: 32, letterSpacing: "-0.015em" }}>
            Una bottega, in senso napoletano.
          </h2>
          <div style={{ fontSize: 18, lineHeight: 1.75, color: "var(--text)", display: "grid", gap: 20, maxWidth: "62ch" }}>
            <p>
              Lo Studio Saltelli &amp; Partners nasce per iniziativa di Emiliano Saltelli, giovane tributarista
              formatosi alla <a href="#" className="sl-link">Federico II</a>, che apre una stanza al secondo piano
              di un palazzo nobiliare a Chiaia.
            </p>
            <p>
              Nel quarto di secolo successivo, lo Studio è cresciuto come si cresce a Napoli — per accumulazione
              paziente, una pratica alla volta, un avvocato alla volta — fino a diventare oggi una bottega di
              quattro professionisti.
            </p>
          </div>
        </div>
        <div />
      </section>

      {/* SECTION 2 — I nostri quattro */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "0 clamp(24px, 5vw, 96px) 128px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 64 }}>
          <div className="sl-mono">§ 03 — I nostri quattro</div>
          <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em", lineHeight: 1.05 }}>
            Quattro avvocati,<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>diciannove aree.</em>
          </h2>
        </div>

        <div style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: 32 }}>
          {lawyers.map((l, i) => (
            <a key={l.slug} href={`/avvocati/${l.slug}/`} style={{ color: "inherit", textDecoration: "none", marginTop: i % 2 === 1 ? 64 : 0 }}>
              <div style={{
                aspectRatio: "3 / 4",
                background: "linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%)",
                marginBottom: 20, position: "relative",
                border: "1px solid var(--border)",
                filter: "grayscale(1) contrast(1.05)",
                transition: "filter 600ms var(--ease-editorial)",
              }}
              onMouseEnter={e => e.currentTarget.style.filter = "grayscale(0)"}
              onMouseLeave={e => e.currentTarget.style.filter = "grayscale(1) contrast(1.05)"}>
                <div style={{ position: "absolute", bottom: 16, left: 16, color: "rgba(255,255,255,0.85)" }} className="sl-mono">
                  Ritratto · 3:4
                </div>
              </div>
              <div className="sl-mono" style={{ marginBottom: 6, fontSize: 10 }}>{l.role}</div>
              <h3 style={{
                fontFamily: "var(--font-display)", fontSize: 26, lineHeight: 1.1,
                color: "var(--primary)", marginBottom: 8, letterSpacing: "-0.02em",
              }}>
                {l.name}
              </h3>
              <div style={{ fontSize: 13, color: "var(--text-muted)", lineHeight: 1.5 }}>{l.spec}</div>
            </a>
          ))}
        </div>
      </section>

      {/* SECTION 3 — Come lavoriamo */}
      <section style={{ background: "var(--surface)", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ 04 — Come lavoriamo</div>
          <div>
            <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em", marginBottom: 56 }}>
              Tre <em style={{ fontStyle: "italic", color: "var(--accent)" }}>principi.</em>
            </h2>
            <div style={{ display: "grid", gap: 0 }}>
              {[
                { n: "01", t: "Ascoltiamo prima", d: "Il primo incontro è gratuito e dura il tempo necessario. Capire la storia viene sempre prima delle carte." },
                { n: "02", t: "Lavoriamo in bottega", d: "Ogni pratica è seguita personalmente da uno dei quattro avvocati. Niente call center, niente passaggi." },
                { n: "03", t: "Diciamo la verità", d: "Anche quando significa sconsigliare un'azione legale. La nostra reputazione vale più di un mandato." },
              ].map(p => (
                <div key={p.n} style={{
                  display: "grid", gridTemplateColumns: "80px 1fr", gap: 32,
                  padding: "40px 0", borderBottom: "1px solid var(--border)",
                  alignItems: "baseline",
                }}>
                  <div className="sl-mono">{p.n}</div>
                  <div>
                    <h3 style={{ fontFamily: "var(--font-display)", fontSize: 32, color: "var(--primary)", marginBottom: 12, letterSpacing: "-0.015em" }}>
                      {p.t}
                    </h3>
                    <p style={{ fontSize: 17, lineHeight: 1.65, color: "var(--text)", maxWidth: "60ch" }}>{p.d}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* TIMELINE 1999 → 2026 */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 64 }}>
          <div className="sl-mono">§ 05 — Cronologia</div>
          <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em" }}>
            1999 → 2026.
          </h2>
        </div>
        <div style={{ paddingLeft: "25%" }}>
          {timeline.map((e, i) => (
            <div key={e.y} style={{
              display: "grid", gridTemplateColumns: "120px 1fr",
              gap: 48, padding: "32px 0", borderBottom: "1px solid var(--border)",
              alignItems: "baseline",
              opacity: i === timeline.length - 1 ? 1 : 0.92,
            }}>
              <div style={{
                fontFamily: "var(--font-display)", fontStyle: "italic",
                fontSize: 36, color: i === timeline.length - 1 ? "var(--accent)" : "var(--primary)",
                lineHeight: 1, letterSpacing: "-0.015em",
              }}>{e.y}</div>
              <div>
                <h3 style={{ fontFamily: "var(--font-display)", fontSize: 22, color: "var(--primary)", marginBottom: 8, letterSpacing: "-0.01em" }}>
                  {e.t}
                </h3>
                <p style={{ fontSize: 16, lineHeight: 1.6, color: "var(--text)", maxWidth: "62ch" }}>{e.d}</p>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* CTA finale */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "96px clamp(24px, 5vw, 96px) 160px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ 06 — Primo incontro</div>
          <div>
            <h2 style={{ fontSize: "clamp(56px, 6.5vw, 96px)", letterSpacing: "-0.025em", lineHeight: 0.98, marginBottom: 48 }}>
              Prenoti<br/>
              <em style={{ fontStyle: "italic", color: "var(--accent)" }}>una consulenza<br/>gratuita.</em>
            </h2>
            <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 22, color: "var(--text)", lineHeight: 1.5, maxWidth: "44ch", marginBottom: 48 }}>
              Il primo incontro è gratuito e dura il tempo necessario. Riceviamo solo su appuntamento.
            </p>
            <a href="/contatti/" className="sl-btn sl-btn--primary">
              Prenota un primo incontro
              <span className="arrow">→</span>
            </a>
          </div>
        </div>
      </section>

      <S2Footer />
    </div>
  );
}

window.S2ChiSiamo = S2ChiSiamo;
