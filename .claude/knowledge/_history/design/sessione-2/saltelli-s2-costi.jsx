/* global React, S2Header, S2Footer */
/* Sessione 2 · /costi/ — COSTI E PRIMA CONSULENZA · CRO STRATEGIC
   Spec key: hero asimmetrico 8fr/4fr trust sticky · 3-col come funziona ·
   3-scenari dopo i 30 minuti · 6/6 calcoliamo + cards · FAQ accordion 5 Q ·
   trust-grid 4-col · CTA finale editoriale.
   Audit CRO Quick Win #1: "Prima consulenza conoscitiva gratuita" è il
   gancio che genera +10-20% conversioni.

   Schema JSON-LD hint:
     - WebPage type
     - LegalService → priceRange "€800–€3500" + offerCatalog
     - FAQPage (Q1-Q5 mainEntity Question)
     - BreadcrumbList
     - LocalBusiness con address + openingHours
*/

function S2Costi() {
  const [openFaq, setOpenFaq] = React.useState(3); // Q4 default open per CRO demo
  const [hoverDemo, setHoverDemo] = React.useState(false); // CTA hero hover state

  const scenari = [
    {
      n: "01",
      label: "MODALITÀ CLASSICA",
      title: "Vieni a Chiaia",
      body: "Via Vannella Gaetani 27, sala riunioni del nostro studio. Lunedì-venerdì 09:30-18:30, su appuntamento.",
      trust: "Caffè incluso",
    },
    {
      n: "02",
      label: "MODALITÀ REMOTA",
      title: "Videocall riservata",
      body: "Google Meet, Zoom o piattaforma a tua scelta. Ideale se vivi fuori Napoli o per pratiche urgenti.",
      trust: "Stesso valore, zero spostamento",
    },
    {
      n: "03",
      label: "MODALITÀ RAPIDA",
      title: "Per casi semplici",
      body: "Per situazioni che richiedono solo un primo orientamento o verifica di percorribilità.",
      trust: "Massimo 30 minuti",
    },
  ];

  const dopoTrenta = [
    {
      n: "01",
      label: "NON PROCEDIAMO",
      body: "Se la pratica non ha solidi presupposti, te lo diciamo subito. Ti suggeriamo un percorso alternativo o ti rimandiamo a un professionista più indicato.",
      trust: "Risparmio: 100% costi inutili",
    },
    {
      n: "02",
      label: "PRATICA SEMPLICE — TARIFFA FORFETTARIA",
      body: "Se la complessità è prevedibile, ti proponiamo un preventivo a forfait. Tutto incluso, nessuna sorpresa successiva.",
      trust: "Trasparenza: tariffa fissa concordata",
    },
    {
      n: "03",
      label: "PRATICA COMPLESSA — TARIFFA ORARIA",
      body: "Se richiede analisi approfondita o iter giudiziale lungo, formuliamo preventivo orario con stima totale + check-in ogni 10 ore lavorate.",
      trust: "Controllo: budget capped + reportistica",
    },
  ];

  const fattori = [
    { n: "Fattore 1", h: "Analisi della pratica", body: "Tipologia atti, normativa applicabile, giurisprudenza di riferimento e perizie tecniche eventuali." },
    { n: "Fattore 2", h: "Ore stimate",          body: "Redazione atti, partecipazione a udienze, comunicazioni con controparte, contraddittorio." },
    { n: "Fattore 3", h: "Probabilità",          body: "Incide sulla strategia consigliata e sul timing. Influenza la scelta forfait vs orario." },
  ];

  const faq = [
    {
      q: "Quanto costa una pratica di diritto tributario?",
      a: (
        <>
          Range orientativo <strong>800–3500€</strong> a seconda di:
          <ul style={{ margin: "12px 0 16px 0", paddingLeft: 20, listStyle: "none" }}>
            <li style={liStyle}>— Tipologia atto (cartella semplice → ricorso CTP/CGT)</li>
            <li style={liStyle}>— Importo contestato</li>
            <li style={liStyle}>— Necessità di periti tecnici</li>
          </ul>
          <em>Esempio reale</em>: opposizione cartella esattoriale 5.000€ → forfait 1.200€ + 200€ contributo unificato.
        </>
      ),
    },
    {
      q: "Pagamento dilazionato è possibile?",
      a: "Sì per pratiche oltre 1.500€. Concordiamo rate trimestrali in funzione del flusso atti. Trasparenza totale: nessun interesse, solo dilazione fisica.",
    },
    {
      q: "Se non vinco, devo comunque pagare?",
      a: "Sì. Le tariffe forensi prevedono onorari per il lavoro svolto, indipendentemente dall'esito (è regola del Codice deontologico). Quello che possiamo fare: valutare seriamente in prima consulenza se la causa è effettivamente percorribile.",
    },
    {
      q: "Il primo incontro è davvero gratuito?",
      a: "Sì, sempre. Trenta minuti senza costi né obblighi. Se decidi di non procedere, abbiamo solo investito tempo. Il nostro tempo costa solo se decidiamo insieme di procedere.",
    },
    {
      q: "Recupero crediti: solo se vinciamo?",
      a: "Per pratiche specifiche di recupero crediti < 5.000€ proponiamo success fee (X% sul recuperato + spese vive). Da concordare in prima consulenza in base alla concretezza del credito.",
    },
  ];

  return (
    <div className="sl-root sl-costi-w4">
      <S2Header />

      {/* ═══════════════════════════════════════════════════════════
          HERO ASIMMETRICO 8fr/4fr — h1 + lede SX, trust sticky DX
          ═══════════════════════════════════════════════════════════ */}
      <section className="sl-costi-w4__hero" style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "120px clamp(24px, 5vw, 96px) 96px",
        display: "grid", gridTemplateColumns: "8fr 4fr", gap: 80, alignItems: "start",
      }}>
        {/* SX 8fr */}
        <div>
          <nav className="sl-mono" style={{ marginBottom: 48, color: "var(--text-muted)" }}
               aria-label="Breadcrumb">
            <a href="/" style={{ color: "inherit" }}>Home</a>
            <span aria-hidden="true"> / </span>
            <span>Costi e prima consulenza</span>
          </nav>
          <div className="sl-mono" style={{ marginBottom: 24, color: "var(--text-muted)" }}>
            § Trasparenza · Costi e tariffe
          </div>
          <h1 style={{
            fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
            fontSize: "clamp(48px, 7vw, 96px)", lineHeight: 1.02, letterSpacing: "-0.025em",
            color: "var(--primary)", margin: "0 0 40px 0", maxWidth: "14ch",
          }}>
            {/* JSX hint: in PHP wrap each word in <span class="sl-word"> per stagger */}
            <span className="sl-word">Costi</span>{' '}
            <span className="sl-word">e</span>{' '}
            <span className="sl-word">prima</span>{' '}
            <span className="sl-word">consulenza.</span>
          </h1>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 22, lineHeight: 1.55, color: "var(--text)",
            maxWidth: "56ch", margin: 0,
          }}>
            Trenta minuti gratuiti per ascoltarci, valutare insieme, decidere se procedere.
            Solo dopo, un preventivo personalizzato basato su complessità, tempi e probabilità di esito.
          </p>
        </div>

        {/* DX 4fr — Trust signal sticky-style */}
        <aside className="sl-costi-w4__hero-trust" style={{
          position: "sticky", top: 96,
          border: "1px solid var(--primary)", padding: "32px 28px", background: "var(--background)",
        }}>
          <div className="sl-mono" style={{ marginBottom: 24, color: "var(--primary)" }}>
            § Prima consulenza
          </div>
          <div style={{
            fontFamily: "var(--font-mono)", fontSize: 11, letterSpacing: "0.18em",
            textTransform: "uppercase", color: "var(--primary)",
            paddingBottom: 24, borderBottom: "1px solid var(--border)", marginBottom: 24,
            lineHeight: 1.8,
          }}>
            Gratuita · 30 minuti<br/>In studio o online
          </div>
          <ul style={{ listStyle: "none", padding: 0, margin: "0 0 32px 0" }}>
            {["Nessun obbligo", "Nessun costo nascosto", "Riservatezza assoluta"].map((t, i) => (
              <li key={i} style={{
                fontFamily: "var(--font-mono)", fontSize: 12, letterSpacing: "0.04em",
                color: "var(--text)", padding: "8px 0", display: "flex", gap: 12, alignItems: "baseline",
              }}>
                <span aria-hidden="true" style={{ color: "var(--accent)", fontFamily: "var(--font-display)" }}>✓</span>
                <span>{t}</span>
              </li>
            ))}
          </ul>
          {/* CTA primary — DEMO hover state per orchestrator review */}
          <a href="#cta-finale"
             onMouseEnter={() => setHoverDemo(true)}
             onMouseLeave={() => setHoverDemo(false)}
             className="sl-btn sl-btn--primary"
             style={{
                display: "inline-flex", alignItems: "baseline", gap: 12,
                fontFamily: "var(--font-body)", fontSize: 16, fontWeight: 500,
                color: "var(--primary)",
                padding: "4px 0", textDecoration: "none", position: "relative",
                transition: "transform 200ms cubic-bezier(0.25,1,0.5,1)",
                transform: hoverDemo ? "translateY(-1px)" : "translateY(0)",
              }}>
            <span>Prenota un incontro</span>
            <span aria-hidden="true" style={{
              transition: "transform 200ms cubic-bezier(0.25,1,0.5,1)",
              transform: hoverDemo ? "translateX(6px)" : "translateX(0)",
            }}>→</span>
            <span aria-hidden="true" style={{
              position: "absolute", left: 0, right: 0, bottom: 0, height: 1,
              background: hoverDemo ? "var(--accent)" : "var(--primary)",
              transition: "background 200ms cubic-bezier(0.25,1,0.5,1)",
            }} />
          </a>
        </aside>
      </section>

      {/* ═══════════════════════════════════════════════════════════
          SECTION 1 — § 01 · Come funziona la prima consulenza
          3-col scenari (lista tipografica, NO box card)
          ═══════════════════════════════════════════════════════════ */}
      <section className="sl-costi-w4__come-funziona" style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px)",
        borderTop: "1px solid var(--border)",
      }}>
        <header style={{ marginBottom: 80 }}>
          <div className="sl-mono" style={{ marginBottom: 24, color: "var(--text-muted)" }}>
            § 01 · Come funziona
          </div>
          <h2 style={{
            fontFamily: "var(--font-display)", fontWeight: 400,
            fontSize: "clamp(40px, 5vw, 72px)", lineHeight: 1.1, letterSpacing: "-0.02em",
            color: "var(--primary)", margin: 0, maxWidth: "20ch",
          }}>
            La prima consulenza, tre modalità.
          </h2>
        </header>

        <div style={{
          display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 64,
        }}>
          {scenari.map((s) => (
            <article key={s.n} style={{ paddingTop: 32, borderTop: "1px solid var(--border)" }}>
              <div className="sl-mono" style={{ marginBottom: 24, color: "var(--text-muted)" }}>
                {s.n} / {s.label}
              </div>
              <h3 style={{
                fontFamily: "var(--font-display)", fontWeight: 400,
                fontSize: "clamp(24px, 2.4vw, 32px)", lineHeight: 1.2,
                color: "var(--primary)", margin: "0 0 20px 0",
              }}>
                {s.title}
              </h3>
              <p style={{
                fontFamily: "var(--font-body)", fontSize: 14, lineHeight: 1.7,
                color: "var(--text)", margin: "0 0 32px 0", maxWidth: "32ch",
              }}>
                {s.body}
              </p>
              <div className="sl-mono" style={{ color: "var(--accent)" }}>
                {s.trust}
              </div>
            </article>
          ))}
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════
          SECTION 2 — § 02 · Cosa succede dopo i 30 minuti
          Layout asimmetrico 4fr/8fr — eyebrow+h2 SX, lista 3 scenari DX
          ═══════════════════════════════════════════════════════════ */}
      <section className="sl-costi-w4__scenari" style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px)",
        background: "var(--surface)",
      }}>
        <div style={{
          display: "grid", gridTemplateColumns: "4fr 8fr", gap: 80, alignItems: "start",
        }}>
          <header style={{ position: "sticky", top: 96 }}>
            <div className="sl-mono" style={{ marginBottom: 24, color: "var(--text-muted)" }}>
              § 02 · Dopo i 30 minuti
            </div>
            <h2 style={{
              fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
              fontSize: "clamp(36px, 4.5vw, 64px)", lineHeight: 1.1, letterSpacing: "-0.02em",
              color: "var(--primary)", margin: 0, maxWidth: "12ch",
            }}>
              Tre scenari possibili.
            </h2>
          </header>

          <div>
            {dopoTrenta.map((s, i) => (
              <article key={s.n} style={{
                paddingTop: i === 0 ? 0 : 48,
                paddingBottom: i === dopoTrenta.length - 1 ? 0 : 48,
                borderBottom: i === dopoTrenta.length - 1 ? "none" : "1px solid var(--border)",
                display: "grid", gridTemplateColumns: "auto 1fr", gap: 48,
              }}>
                <div className="sl-mono" style={{
                  color: "var(--accent)", fontSize: 14, letterSpacing: "0.08em",
                  paddingTop: 4, minWidth: 32,
                }}>
                  {s.n}
                </div>
                <div>
                  <div className="sl-mono" style={{ marginBottom: 16, color: "var(--primary)" }}>
                    {s.label}
                  </div>
                  <p style={{
                    fontFamily: "var(--font-body)", fontSize: 17, lineHeight: 1.7,
                    color: "var(--text)", margin: "0 0 16px 0", maxWidth: "60ch",
                  }}>
                    {s.body}
                  </p>
                  <div className="sl-mono" style={{ color: "var(--text-muted)" }}>
                    {s.trust}
                  </div>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════
          SECTION 3 — § 03 · Come calcoliamo i preventivi
          Layout 6fr/6fr — drop-cap prose SX, 3 fattori cards stacked DX
          ═══════════════════════════════════════════════════════════ */}
      <section className="sl-costi-w4__calcoliamo" style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px)",
      }}>
        <header style={{ marginBottom: 64 }}>
          <div className="sl-mono" style={{ marginBottom: 24, color: "var(--text-muted)" }}>
            § 03 · Metodologia
          </div>
          <h2 style={{
            fontFamily: "var(--font-display)", fontWeight: 400,
            fontSize: "clamp(40px, 5vw, 72px)", lineHeight: 1.1, letterSpacing: "-0.02em",
            color: "var(--primary)", margin: 0, maxWidth: "20ch",
          }}>
            Come calcoliamo i preventivi.
          </h2>
        </header>

        <div style={{
          display: "grid", gridTemplateColumns: "6fr 6fr", gap: 80, alignItems: "start",
        }}>
          {/* SX — drop-cap prose editoriale ~200 parole */}
          <div className="sl-page__prose" style={{ maxWidth: "62ch" }}>
            <p style={{
              fontFamily: "var(--font-body)", fontSize: 18, lineHeight: 1.75,
              color: "var(--text)", margin: 0,
            }}>
              <span aria-hidden="true" style={{
                fontFamily: "var(--font-display)", fontSize: 84, lineHeight: 0.85,
                float: "left", margin: "8px 16px 0 0", color: "var(--primary)",
              }}>
                T
              </span>
              rasparenza è la nostra prima regola. I nostri preventivi considerano tre fattori:
              complessità della pratica (analisi atti, ricerca giurisprudenza, perizie tecniche),
              tempo stimato (ore di lavoro su atti, udienze, comunicazioni), probabilità di esito
              favorevole (incide sulla strategia consigliata).
            </p>
            <p style={{
              fontFamily: "var(--font-body)", fontSize: 18, lineHeight: 1.75,
              color: "var(--text)", margin: "32px 0 0 0",
            }}>
              Quando possibile, lavoriamo a tariffa forfettaria: ti diamo un numero finale al primo
              incontro e quello rimane. Quando la complessità non lo permette, lavoriamo a tariffa
              oraria con budget cap concordato in anticipo. <em>Niente fatturazione a sorpresa, mai.</em>
            </p>
          </div>

          {/* DX — 3 fattori stacked cards */}
          <div style={{ display: "grid", gap: 0 }}>
            {fattori.map((f, i) => (
              <article key={f.n} style={{
                paddingTop: 32, paddingBottom: 32,
                borderBottom: i === fattori.length - 1 ? "1px solid var(--border)" : "none",
                borderTop: "1px solid var(--border)",
              }}>
                <div className="sl-mono" style={{ marginBottom: 12, color: "var(--accent)" }}>
                  {f.n}
                </div>
                <h4 style={{
                  fontFamily: "var(--font-display)", fontWeight: 400,
                  fontSize: 24, lineHeight: 1.2, color: "var(--primary)",
                  margin: "0 0 12px 0",
                }}>
                  {f.h}
                </h4>
                <p style={{
                  fontFamily: "var(--font-body)", fontSize: 14, lineHeight: 1.7,
                  color: "var(--text-muted)", margin: 0, maxWidth: "44ch",
                }}>
                  {f.body}
                </p>
              </article>
            ))}
          </div>
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════
          SECTION 4 — § 04 · FAQ EDITORIALE — accordion +/− pattern .sl-acc
          5 domande, accordion default closed (Q4 demo open per orchestrator)
          ═══════════════════════════════════════════════════════════ */}
      <section className="sl-costi-w4__faq" style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px)",
        background: "var(--surface)",
      }}>
        <header style={{ marginBottom: 64, maxWidth: 720 }}>
          <div className="sl-mono" style={{ marginBottom: 24, color: "var(--text-muted)" }}>
            § 04 · Sui costi, in chiaro
          </div>
          <h2 style={{
            fontFamily: "var(--font-display)", fontWeight: 400,
            fontSize: "clamp(40px, 5vw, 72px)", lineHeight: 1.1, letterSpacing: "-0.02em",
            color: "var(--primary)", margin: 0,
          }}>
            Domande frequenti sui costi.
          </h2>
        </header>

        <div style={{ maxWidth: 960 }}>
          {faq.map((row, i) => {
            const isOpen = openFaq === i;
            return (
              <article key={i} className="sl-acc" style={{
                borderTop: i === 0 ? "1px solid var(--primary)" : "none",
                borderBottom: "1px solid var(--primary)",
              }}>
                <button
                  type="button"
                  onClick={() => setOpenFaq(isOpen ? -1 : i)}
                  aria-expanded={isOpen}
                  aria-controls={`faq-panel-${i}`}
                  style={{
                    display: "flex", justifyContent: "space-between", alignItems: "baseline",
                    width: "100%", padding: "32px 0", background: "none", border: 0, cursor: "pointer",
                    fontFamily: "var(--font-display)", fontWeight: 400,
                    fontSize: "clamp(20px, 2vw, 28px)", lineHeight: 1.3,
                    color: "var(--primary)", textAlign: "left", letterSpacing: "-0.01em",
                  }}>
                  <span>{row.q}</span>
                  <span aria-hidden="true" style={{
                    fontFamily: "var(--font-mono)", fontSize: 24, color: "var(--accent)",
                    transition: "transform 200ms cubic-bezier(0.25,1,0.5,1)",
                    transform: isOpen ? "rotate(45deg)" : "rotate(0)",
                    flexShrink: 0, marginLeft: 24,
                  }}>+</span>
                </button>
                {isOpen && (
                  <div id={`faq-panel-${i}`} style={{
                    paddingBottom: 32, paddingRight: 80, maxWidth: "72ch",
                    fontFamily: "var(--font-body)", fontSize: 17, lineHeight: 1.7,
                    color: "var(--text)",
                  }}>
                    {row.a}
                  </div>
                )}
              </article>
            );
          })}
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════
          SECTION 5 — TRUST SIGNALS RIASSUNTIVI · 4-col grid
          ═══════════════════════════════════════════════════════════ */}
      <section className="sl-costi-w4__trust-grid" style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px)",
        borderTop: "1px solid var(--border)", borderBottom: "1px solid var(--border)",
      }}>
        <div style={{
          display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: 32,
        }}>
          {[
            "Iscritti Ordine Avvocati Napoli",
            "P.IVA 06685101211",
            "Codice deontologico forense",
            "Riservatezza assoluta",
          ].map((t, i) => (
            <div key={i} style={{
              padding: "32px 24px", border: "1px solid var(--border)",
              fontFamily: "var(--font-mono)", fontSize: 12, letterSpacing: "0.08em",
              textTransform: "uppercase", color: "var(--text)", lineHeight: 1.6,
              textAlign: "center",
            }}>
              {t}
            </div>
          ))}
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════
          CTA FINALE EDITORIALE
          ═══════════════════════════════════════════════════════════ */}
      <section id="cta-finale" className="sl-costi-w4__cta-final" style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "120px clamp(24px, 5vw, 96px)",
        textAlign: "center",
      }}>
        <div className="sl-mono" style={{ marginBottom: 32, color: "var(--text-muted)" }}>
          § Pronto?
        </div>
        <h2 style={{
          fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
          fontSize: "clamp(36px, 4.5vw, 56px)", lineHeight: 1.1, letterSpacing: "-0.02em",
          color: "var(--primary)", margin: "0 auto 32px",
          maxWidth: "20ch",
        }}>
          La prima consulenza è gratuita. Sempre.
        </h2>
        <p style={{
          fontFamily: "var(--font-body)", fontSize: 18, lineHeight: 1.65,
          color: "var(--text)", margin: "0 auto 48px", maxWidth: "48ch",
        }}>
          Trenta minuti per ascoltarci, valutare insieme, capire se possiamo esserti utili.
          Senza obblighi e senza costi nascosti.
        </p>
        <a href="/contatti/" className="sl-btn sl-btn--primary" style={{
          display: "inline-flex", alignItems: "baseline", gap: 12,
          fontFamily: "var(--font-body)", fontSize: 17, fontWeight: 500,
          color: "var(--primary)", padding: "8px 0", textDecoration: "none",
          position: "relative", borderBottom: "1px solid var(--primary)",
        }}>
          <span>Prenota un incontro</span>
          <span aria-hidden="true">→</span>
        </a>
        <div className="sl-mono" style={{
          marginTop: 48, color: "var(--text-muted)", letterSpacing: "0.08em",
        }}>
          Risposta entro 24 ore · Riservatezza assoluta
        </div>
      </section>

      <S2Footer />
    </div>
  );
}

const liStyle = {
  fontFamily: "var(--font-body)", fontSize: 16, lineHeight: 1.7,
  color: "var(--text)", padding: "4px 0",
};

/* ════════════════════════════════════════════════════════════════
   RESPONSIVE NOTES (per implementation v0.23.0)
   ════════════════════════════════════════════════════════════════
   Desktop ≥1024:
     hero 8fr/4fr — section1 3-col — section2 4fr/8fr — section3 6fr/6fr
     section5 4-col

   Tablet 768-1023:
     hero stack (h1 sopra, trust sotto, NO sticky)
     section1 2-col grid (3rd a tutta larghezza row 2)
     section2 stack header+lista
     section3 stack 1-col
     section5 2x2 grid

   Mobile <768:
     tutto stack 1-col
     padding ridotto 64-32px
     touch target ≥48px
     trust hero senza sticky, full width sotto h1+lede
     scenari 2 (tutti) bordi solo bottom, no padding asimmetrico
     accordion full width, no padding-right 80

   ANIMATION INTEGRATION (allineato v0.22.0):
     - h1 .sl-word stagger via [data-split-reveal] in PHP (helper saltelli_split_h1_words)
     - drop-cap reveal scale-up tramite IntersectionObserver su .sl-page__prose first-letter
     - card hover translateY(-4px) cross-template via CSS @media (hover: hover)
     - CTA primary translateY(-1px) hover + bronze underline
     - accordion +/− rotate transition 200ms quart-out
     - prefers-reduced-motion: tutte le anim disabilitate (universale a livello CSS)

   SCHEMA JSON-LD HINTS (PHP partial-costi.php):
     - WebPage @type
     - LegalService.priceRange = "€800–€3500"
     - LegalService.offerCatalog = lista practice areas con priceSpecification
     - FAQPage.mainEntity = array di Question (5 entries da faq[])
     - BreadcrumbList = Home → Costi e prima consulenza
     - LocalBusiness inherit da partial-organization.php (NO duplicate)
   ════════════════════════════════════════════════════════════════ */
