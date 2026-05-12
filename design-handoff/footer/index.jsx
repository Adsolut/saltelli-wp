/* global React */
/* Saltelli & Partners — Sessione 2 R3 · saltelli-s2-footer.jsx
   Footer standalone editoriale denso · 4 fasce verticali.
   Sostituirà la sezione footer dentro chrome.jsx + diventa template footer.php WP.

   Schema markup hints (commenti, generati lato Yoast/PHP):
   · <footer role="contentinfo">                       → WPFooter landmark
   · COL 1 brand + NAP + tel + mail + hours            → Organization + LegalService.address + ContactPoint + openingHours
   · COL 1 social row Instagram/LinkedIn/Twitter       → Organization.sameAs
   · Logo stack                                        → Organization.logo
   · Newsletter form (Brevo, FIRSTNAME + email)        → Action.SubscribeAction (opzionale)
   · /llms.txt /sitemap.xml /robots.txt                → AI-friendly endpoints (no schema, plain links)

   Props:
     newsletterState ∈ "idle" | "loading" | "success"   (per demo states su canvas)
     hoverDemo: boolean                                  (forza un paio di link in stato hover bronze)
*/

function S2Footer({ newsletterState = "idle", hoverDemo = false } = {}) {
  const [state, setState] = React.useState(newsletterState);
  React.useEffect(() => { setState(newsletterState); }, [newsletterState]);

  const onSubmit = (e) => {
    e.preventDefault();
    setState("loading");
    setTimeout(() => setState("success"), 1200);
  };

  // Aree tier-1 (bronze numerate) + tier-2 (2-col)
  const tier1 = [
    { n: "01", t: "Diritto tributario" },
    { n: "02", t: "Diritto del lavoro" },
    { n: "03", t: "Diritto di famiglia LGBTQ+" },
  ];
  const tier2 = [
    "Cartelle esattoriali", "Recupero crediti",
    "Diritto di famiglia", "Responsabilità medica",
    "Diritto bancario", "Diritto condominiale",
    "Diritto immigrazione", "Diritto penale",
    "Diritto previdenziale", "Assicurazioni",
    "Successioni", "Risarcimento danni",
    "Responsabilità civile", "Domiciliazione",
    "Consulenze online", "Diritto amministrativo",
  ];

  const cream = "var(--background)";
  const muted = "rgba(255,255,255,0.55)";
  const dim   = "rgba(255,255,255,0.4)";
  const hair  = "1px solid rgba(255,255,255,0.15)";
  const hairThin = "1px solid rgba(255,255,255,0.10)";

  // Inietta una sola volta il CSS hover/focus per i link footer + checkbox + spinner
  React.useEffect(() => {
    if (document.getElementById("s2-footer-css")) return;
    const css = document.createElement("style");
    css.id = "s2-footer-css";
    css.textContent = `
      .sl-foot-link {
        position: relative;
        color: var(--background);
        text-decoration: none;
        transition: color 200ms cubic-bezier(0.25,0.46,0.45,0.94);
        display: inline-block;
      }
      .sl-foot-link::after {
        content: "";
        position: absolute; left: 0; bottom: -2px;
        width: 100%; height: 1px;
        background: var(--accent);
        transform: scaleX(0); transform-origin: left;
        transition: transform 220ms cubic-bezier(0.25,0.46,0.45,0.94);
      }
      .sl-foot-link:hover, .sl-foot-link.is-hover { color: var(--accent); }
      .sl-foot-link:hover::after, .sl-foot-link.is-hover::after { transform: scaleX(1); }
      .sl-foot-link:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 4px;
      }
      .sl-foot-link:active { color: var(--accent); opacity: 0.85; }

      .sl-newsletter__input {
        width: 100%;
        border: 0;
        border-bottom: 1px solid rgba(255,255,255,0.3);
        background: transparent;
        padding: 16px 0 12px;
        font-family: var(--font-body);
        font-size: 15px;
        color: var(--background);
        outline: none;
        transition: border-color 200ms;
      }
      .sl-newsletter__input::placeholder { color: rgba(255,255,255,0.5); }
      .sl-newsletter__input:focus {
        border-bottom-color: var(--accent);
        outline: 2px solid var(--accent);
        outline-offset: 4px;
      }

      .sl-newsletter__check {
        appearance: none; -webkit-appearance: none;
        width: 16px; height: 16px;
        border: 1px solid rgba(255,255,255,0.4);
        background: transparent;
        cursor: pointer; flex-shrink: 0;
        margin-top: 2px;
        position: relative;
        transition: all 200ms;
      }
      .sl-newsletter__check:checked {
        background: var(--accent);
        border-color: var(--accent);
      }
      .sl-newsletter__check:checked::after {
        content: ""; position: absolute;
        left: 4px; top: 1px;
        width: 5px; height: 9px;
        border: solid white;
        border-width: 0 1.5px 1.5px 0;
        transform: rotate(45deg);
      }
      .sl-newsletter__check:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 3px;
      }

      @keyframes sl-spin { to { transform: rotate(360deg); } }
      .sl-spinner {
        display: inline-block; width: 12px; height: 12px;
        border: 1.5px solid rgba(255,255,255,0.3);
        border-top-color: var(--background);
        border-radius: 50%;
        animation: sl-spin 700ms linear infinite;
        vertical-align: -2px;
      }

      @media (max-width: 1023px) {
        .sl-foot-main { grid-template-columns: 1fr 1fr !important; gap: 64px !important; }
        .sl-foot-newsletter { grid-template-columns: 1fr !important; gap: 48px !important; }
        .sl-foot-cta { grid-template-columns: 1fr !important; gap: 32px !important; }
      }
      @media (max-width: 767px) {
        .sl-foot-main { grid-template-columns: 1fr !important; gap: 56px !important; }
        .sl-foot-fields { grid-template-columns: 1fr !important; }
        .sl-foot-bottom { flex-direction: column !important; gap: 16px !important; align-items: flex-start !important; }
      }
    `;
    document.head.appendChild(css);
  }, []);

  return (
    <footer role="contentinfo" style={{ fontFamily: "var(--font-body)" }}>

      {/* ═══ FASCIA 1 · CTA EDITORIALE pre-footer ═══ */}
      <section style={{
        background: "var(--surface)",
        borderBottom: "1px solid var(--primary)",
        padding: "clamp(64px, 9vw, 128px) clamp(24px, 5vw, 96px)",
      }}>
        <div className="sl-foot-cta" style={{
          maxWidth: 1440, margin: "0 auto",
          display: "grid", gridTemplateColumns: "8fr 4fr",
          gap: 96, alignItems: "end",
        }}>
          <div>
            <div className="sl-mono" style={{ color: "var(--accent)", marginBottom: 32 }}>§ Ultima chiamata</div>
            <h2 style={{
              fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
              fontSize: "clamp(36px, 4.5vw, 56px)",
              lineHeight: 1.05, letterSpacing: "-0.02em",
              color: "var(--primary)", marginBottom: 24,
            }}>
              Vorresti raccontarci<br/>la tua pratica?
            </h2>
            <p style={{
              fontSize: 18, lineHeight: 1.6, color: "var(--text)",
              maxWidth: "50ch", margin: 0,
            }}>
              Trenta minuti di prima consulenza conoscitiva gratuita. In studio o online. Risposta entro 24 ore.
            </p>
          </div>
          <div style={{ paddingBottom: 8 }}>
            <a href="/contatti/" className="sl-btn sl-btn--primary">
              Prenota un incontro
              <span className="arrow">→</span>
            </a>
            <p className="sl-mono" style={{
              marginTop: 20,
              fontStyle: "italic", color: "var(--text-muted)",
              fontSize: 11,
            }}>
              Nessun obbligo · Nessun costo · Riservatezza assoluta
            </p>
          </div>
        </div>
      </section>

      {/* ═══ FASCIA 2 · MAIN FOOTER 4-col ═══ */}
      <section style={{
        background: "var(--primary)", color: cream,
        padding: "clamp(64px, 9vw, 128px) clamp(24px, 5vw, 96px) 64px",
      }}>
        <div style={{ maxWidth: 1440, margin: "0 auto" }}>
          <div className="sl-foot-main" style={{
            display: "grid",
            gridTemplateColumns: "3fr 4fr 3fr 3fr",
            gap: 64,
          }}>

            {/* COL 1 — BRAND IDENTITY */}
            <div>
              {/* Logo stack v1.1 */}
              <div style={{ display: "grid", justifyItems: "start", gap: 6, marginBottom: 32 }}>
                <span className="sl-mono" style={{
                  fontSize: 11, letterSpacing: "0.42em", color: cream,
                }}>Studio Legale</span>
                <span style={{
                  fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
                  fontSize: 52, lineHeight: 0.95, letterSpacing: "-0.02em", color: cream,
                }}>
                  <span style={{ color: "var(--accent)" }}>S</span>altelli
                </span>
                <span style={{
                  fontFamily: "var(--font-mono)", fontSize: 10,
                  letterSpacing: "0.24em", textTransform: "uppercase",
                  color: muted, marginTop: 4,
                }}>Napoli · Dal 1999</span>
              </div>

              {/* Brand statement */}
              <p style={{
                fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
                fontSize: 16, lineHeight: 1.6, color: cream, opacity: 0.9,
                margin: "0 0 32px",
              }}>
                Un atelier editoriale italiano.<br/>
                Quattro avvocati a Chiaia.<br/>
                Vent'anni di pratica accanto a famiglie e imprese.
              </p>

              {/* Contact mini */}
              <div className="sl-mono" style={{
                fontSize: 12, lineHeight: 1.85, color: muted,
                display: "grid", gap: 4, marginBottom: 24,
              }}>
                <a href="tel:+390818131119" className="sl-foot-link" style={{ color: cream }}>
                  +39 081 1813 1119
                </a>
                <a href="mailto:info@studiolegalesaltelli.it" className="sl-foot-link" style={{ color: cream }}>
                  info@studiolegalesaltelli.it
                </a>
                <span>Lun–Ven · 09:30–18:30</span>
              </div>

              {/* Social row */}
              <div style={{ display: "flex", gap: 24, flexWrap: "wrap" }}>
                {["Instagram", "LinkedIn", "Twitter", "WhatsApp"].map((s, i) => (
                  <a key={s} href="#" className={"sl-foot-link" + (hoverDemo && i === 0 ? " is-hover" : "")}
                     style={{
                       fontFamily: "var(--font-mono)", fontSize: 11,
                       letterSpacing: "0.32em", textTransform: "uppercase",
                       color: cream,
                     }}>{s}</a>
                ))}
              </div>
            </div>

            {/* COL 2 — AREE DI PRATICA */}
            <div>
              <div className="sl-mono" style={{
                fontSize: 11, letterSpacing: "0.32em", color: cream,
                marginBottom: 24,
              }}>Diciannove aree</div>

              {/* Tier 1 */}
              <div style={{ display: "grid", gap: 12 }}>
                {tier1.map(a => (
                  <a key={a.n} href="#" className="sl-foot-link" style={{
                    display: "grid", gridTemplateColumns: "32px 1fr", gap: 12, alignItems: "baseline",
                    fontSize: 14, color: cream,
                  }}>
                    <span className="sl-mono" style={{ color: "var(--accent)", fontSize: 10 }}>{a.n}</span>
                    <span>{a.t}</span>
                  </a>
                ))}
              </div>

              <div style={{ borderTop: hair, margin: "20px 0" }} />

              {/* Tier 2 — 2 col */}
              <div style={{
                columnCount: 2, columnGap: 32,
                fontSize: 12, lineHeight: 2, color: "rgba(255,255,255,0.78)",
              }}>
                {tier2.map((a, i) => (
                  <a key={a} href="#" className={"sl-foot-link" + (hoverDemo && i === 1 ? " is-hover" : "")}
                     style={{ display: "block", breakInside: "avoid", color: "inherit" }}>{a}</a>
                ))}
              </div>

              <div style={{ borderTop: hair, margin: "20px 0" }} />

              <a href="/competenze/" className="sl-foot-link sl-mono" style={{
                color: "var(--accent)", fontSize: 11,
              }}>Tutte le aree →</a>
            </div>

            {/* COL 3 — STUDIO + EDITORIAL */}
            <div>
              <div className="sl-mono" style={{
                fontSize: 11, letterSpacing: "0.32em", color: cream, marginBottom: 24,
              }}>Studio</div>

              <nav style={{ display: "grid", gap: 8, fontSize: 13, lineHeight: 2 }}>
                <a href="/chi-siamo/" className="sl-foot-link" style={{ color: cream }}>Lo studio</a>
                <a href="/avvocati/" className="sl-foot-link" style={{ color: cream }}>Avvocati</a>
                <a href="/casi/" className="sl-foot-link" style={{ color: cream }}>Casi rappresentativi</a>
                <a href="/costi/" className="sl-foot-link" style={{ color: cream }}>Costi e prima consulenza</a>
              </nav>

              <div style={{ borderTop: hair, margin: "24px 0 16px" }} />

              <div className="sl-mono" style={{ fontSize: 10, color: muted, marginBottom: 12 }}>Risorse</div>
              <nav style={{ display: "grid", gap: 8, fontSize: 13, lineHeight: 1.85 }}>
                <a href="/blog/" className="sl-foot-link" style={{ color: cream }}>Editoriale / Blog</a>
                <a href="/glossario-legale/" className="sl-foot-link" style={{ color: cream }}>Glossario legale</a>
                <a href="/guide-gratuite/" className="sl-foot-link" style={{ color: cream }}>Guide gratuite</a>
              </nav>

              <div style={{ borderTop: hair, margin: "24px 0 16px" }} />

              <div className="sl-mono" style={{ fontSize: 10, color: muted, marginBottom: 12 }}>Servizio</div>
              <nav style={{ display: "grid", gap: 8, fontSize: 13, lineHeight: 1.85 }}>
                <a href="/contatti/" className="sl-foot-link" style={{ color: cream }}>Contatti</a>
                <a href="/prima-consulenza/" className="sl-foot-link" style={{ color: cream }}>Prima consulenza</a>
                <a href="/come-lavoriamo/" className="sl-foot-link" style={{ color: cream }}>Come lavoriamo</a>
              </nav>
            </div>

            {/* COL 4 — INFO ISTITUZIONALI + AI-FRIENDLY */}
            <div>
              <div className="sl-mono" style={{
                fontSize: 11, letterSpacing: "0.32em", color: cream, marginBottom: 24,
              }}>Studio professionale</div>

              <div className="sl-mono" style={{
                fontSize: 12, lineHeight: 1.85, color: muted,
                display: "grid", gap: 6,
              }}>
                <span>Iscritto Ordine Avvocati Napoli</span>
                <span>P.IVA 06685101211</span>
                <a href="mailto:emilianosaltelli@avvocatinapoli.legalmail.it"
                   className="sl-foot-link" style={{ color: muted, fontSize: 11, wordBreak: "break-all" }}>
                  PEC emilianosaltelli@<br/>avvocatinapoli.legalmail.it
                </a>
              </div>

              <div style={{ borderTop: hair, margin: "24px 0 16px" }} />

              <div className="sl-mono" style={{ fontSize: 10, color: muted, marginBottom: 12 }}>AI-friendly</div>
              <div style={{ display: "grid", gap: 6, fontSize: 12, fontFamily: "var(--font-mono)" }}>
                <a href="/llms.txt" className="sl-foot-link" style={{ color: muted }}>/llms.txt</a>
                <a href="/sitemap.xml" className="sl-foot-link" style={{ color: muted }}>/sitemap.xml</a>
                <a href="/robots.txt" className="sl-foot-link" style={{ color: muted }}>/robots.txt</a>
                <span style={{
                  fontFamily: "var(--font-display)", fontStyle: "italic",
                  fontSize: 12, color: muted, marginTop: 4,
                }}>Citazione consentita</span>
              </div>

              <div style={{ borderTop: hair, margin: "24px 0 16px" }} />

              <div style={{
                display: "inline-block",
                border: "1px solid rgba(255,255,255,0.3)",
                padding: "8px 14px",
                fontFamily: "var(--font-mono)", fontStyle: "italic",
                fontSize: 10, letterSpacing: "0.16em", textTransform: "uppercase",
                color: cream,
              }}>
                Verificabile in studio
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ═══ FASCIA 3 · NEWSLETTER EDITORIALE ═══ */}
      <section style={{
        background: "var(--primary)", color: cream,
        borderTop: hair,
        padding: "clamp(48px, 6vw, 80px) clamp(24px, 5vw, 96px)",
      }}>
        <div className="sl-foot-newsletter" style={{
          maxWidth: 1440, margin: "0 auto",
          display: "grid", gridTemplateColumns: "5fr 7fr", gap: 96, alignItems: "start",
        }}>
          {/* Sx — Pitch editoriale */}
          <div>
            <div className="sl-mono" style={{ color: "var(--accent)", marginBottom: 24 }}>
              § Newsletter · Dal 2026
            </div>
            <h3 style={{
              fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
              fontSize: "clamp(32px, 3.2vw, 40px)",
              lineHeight: 1.05, letterSpacing: "-0.02em",
              color: cream, margin: "0 0 24px",
            }}>
              L'editoriale del giovedì.
            </h3>
            <p style={{
              fontSize: 14, lineHeight: 1.6, color: "rgba(255,255,255,0.7)",
              maxWidth: "38ch", margin: "0 0 20px",
            }}>
              Una mail al mese. Sentenze recenti, novità giurisprudenziali, case study reali dello Studio. Mai promozioni, mai spam.
            </p>
            <p className="sl-mono" style={{ fontSize: 11, color: muted, margin: 0 }}>
              Una al mese · No spam · Cancellazione 1 click
            </p>
          </div>

          {/* Dx — Form Brevo / Success */}
          <div style={{ paddingTop: 8, position: "relative", minHeight: 220 }}>
            {state === "success" ? (
              <div style={{ animation: "none" }}>
                <div className="sl-mono" style={{ color: "var(--accent)", marginBottom: 16 }}>§ Iscritto</div>
                <h3 style={{
                  fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
                  fontSize: "clamp(28px, 2.8vw, 36px)",
                  lineHeight: 1.1, letterSpacing: "-0.02em",
                  color: cream, margin: "0 0 16px",
                }}>
                  Bene. Ti diamo il benvenuto.
                </h3>
                <p className="sl-mono" style={{
                  fontSize: 12, lineHeight: 1.7, color: muted, maxWidth: "44ch", margin: 0,
                }}>
                  Riceverai l'editoriale ogni giovedì del mese.<br/>
                  Puoi cancellarti in qualsiasi momento.
                </p>
              </div>
            ) : (
              <form
                id="sib_signup_form_1"
                onSubmit={onSubmit}
                aria-busy={state === "loading"}
                style={{
                  opacity: state === "loading" ? 0.5 : 1,
                  pointerEvents: state === "loading" ? "none" : "auto",
                  transition: "opacity 200ms",
                }}
              >
                <div className="sl-foot-fields" style={{
                  display: "grid", gridTemplateColumns: "1fr 2fr", gap: 32, marginBottom: 24,
                }}>
                  <label style={{ display: "block" }}>
                    <span className="sl-mono" style={{ display: "block", fontSize: 10, color: muted, marginBottom: 4 }}>
                      Nome
                    </span>
                    <input type="text" name="FIRSTNAME" placeholder="Il tuo nome"
                      className="sl-newsletter__input" />
                  </label>
                  <label style={{ display: "block" }}>
                    <span className="sl-mono" style={{ display: "block", fontSize: 10, color: muted, marginBottom: 4 }}>
                      Email
                    </span>
                    <input type="email" name="email" placeholder="indirizzo@email.it" required
                      className="sl-newsletter__input" />
                  </label>
                </div>

                <label style={{
                  display: "grid", gridTemplateColumns: "auto 1fr", gap: 12,
                  alignItems: "start", cursor: "pointer", marginBottom: 32,
                }}>
                  <input type="checkbox" name="terms" required className="sl-newsletter__check" />
                  <span style={{
                    fontSize: 12, color: "rgba(255,255,255,0.55)", lineHeight: 1.5,
                  }}>
                    Accetto la{" "}
                    <a href="/privacy/" className="sl-foot-link" style={{ color: cream, textDecoration: "underline", textUnderlineOffset: 3 }}>privacy policy</a>
                    {" "}e voglio ricevere l'editoriale.
                  </span>
                </label>

                <button type="submit" className="sl-btn sl-btn--primary" style={{
                  background: "var(--accent)", borderColor: "var(--accent)", color: "white",
                  display: "inline-flex", alignItems: "center", gap: 12,
                }}>
                  {state === "loading" && <span className="sl-spinner" aria-hidden="true" />}
                  {state === "loading" ? "Invio in corso…" : "Iscriviti all'editoriale"}
                  {state !== "loading" && <span className="arrow">→</span>}
                </button>
              </form>
            )}
          </div>
        </div>
      </section>

      {/* ═══ FASCIA 4 · BOTTOM LEGAL ═══ */}
      <section style={{
        background: "var(--primary)", color: cream,
        borderTop: hairThin,
        padding: "28px clamp(24px, 5vw, 96px)",
      }}>
        <div className="sl-foot-bottom" style={{
          maxWidth: 1440, margin: "0 auto",
          display: "flex", justifyContent: "space-between", alignItems: "center",
          fontFamily: "var(--font-mono)", fontSize: 11,
          letterSpacing: "0.08em",
        }}>
          <div style={{ color: dim }}>
            © 2026 Studio Legale Emiliano Saltelli &amp; Partners
          </div>
          <div style={{ display: "flex", gap: 28 }}>
            <a href="/privacy/" className="sl-foot-link" style={{ color: muted }}>Privacy</a>
            <a href="/cookie/" className="sl-foot-link" style={{ color: muted }}>Cookie</a>
            <a href="/note-legali/" className="sl-foot-link" style={{ color: muted }}>Note legali</a>
          </div>
          <div style={{ color: dim, fontStyle: "italic" }}>
            Sito by{" "}
            <a href="https://adsolut.it" className="sl-foot-link" style={{ color: dim, fontStyle: "italic" }}>Adsolut</a>
          </div>
        </div>
      </section>
    </footer>
  );
}

window.S2Footer = S2Footer;
