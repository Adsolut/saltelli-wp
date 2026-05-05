/* global React, S2Header, S2Footer */
/* Sessione 2 R2 · /glossario-legale/ — ENTITY-RICH SEO/GEO
   Spec key: hero count · search + a-z sticky · <dl> 2-col semantic ·
   term sx 30 / definition dx 70 · FAQ glossario · CTA "non trovi?"
   Schema JSON-LD: DefinedTermSet + Article + FAQPage */

function S2Glossario() {
  const [q, setQ] = React.useState("");
  const [openFaq, setOpenFaq] = React.useState(0);

  const terms = [
    { l: "A", k: "accertamento-sintetico", t: "Accertamento sintetico", cat: "Tributario", def: "Metodo presuntivo di determinazione del reddito basato sulla disponibilità di beni e servizi indici di capacità contributiva. La presunzione è legale relativa: l'onere della prova grava sul contribuente, che può dimostrare la non rilevanza fiscale delle spese.", esempio: "Acquisto di un'auto da €60.000 con un reddito dichiarato di €25.000 può attivare un controllo redditometrico.", correlate: ["Diritto tributario"] },
    { l: "A", k: "affidamento-condiviso", t: "Affidamento condiviso", cat: "Famiglia", def: "Regime di esercizio della responsabilità genitoriale a seguito di separazione, in cui entrambi i genitori partecipano alle decisioni rilevanti per il figlio. È la regola; l'affidamento esclusivo è l'eccezione, motivata dall'interesse del minore.", esempio: "Decisioni su scuola, salute, residenza prese insieme, anche con collocamento prevalente presso un genitore.", correlate: ["Diritto di famiglia"] },
    { l: "A", k: "appello", t: "Appello", cat: "Processo", def: "Mezzo di impugnazione che consente la revisione della sentenza di primo grado da parte di un giudice superiore. In materia civile si propone alla Corte d'Appello entro 30 giorni dalla notifica o 6 mesi dalla pubblicazione.", esempio: "Sentenza del Tribunale di Napoli del 10 gennaio: appello entro 10 luglio (termine lungo) o 30 gg dalla notifica.", correlate: ["Contenzioso civile"] },
    { l: "C", k: "cartella-esattoriale", t: "Cartella esattoriale", cat: "Tributario", def: "Atto con cui l'Agenzia delle Entrate-Riscossione richiede al contribuente il pagamento di somme iscritte a ruolo (imposte, contributi, sanzioni). Va impugnata davanti alla Commissione Tributaria entro 60 giorni dalla notifica.", esempio: "Cartella per IRPEF non versata 2019: impugnazione possibile per vizio di notifica o prescrizione quinquennale.", correlate: ["Diritto tributario"] },
    { l: "C", k: "cassazione", t: "Corte di Cassazione", cat: "Processo", def: "Organo di vertice della giurisdizione ordinaria, giudice di legittimità (non di merito). Decide su violazioni di legge, vizi procedurali, motivazione contraddittoria. Il ricorso è ammesso solo per i motivi tassativi dell'art. 360 c.p.c.", esempio: "Riforma di una sentenza d'appello per omessa motivazione su un motivo decisivo della controversia.", correlate: ["Contenzioso civile"] },
    { l: "D", k: "demansionamento", t: "Demansionamento", cat: "Lavoro", def: "Assegnazione del lavoratore a mansioni inferiori a quelle del livello contrattuale, vietata salvo eccezioni (art. 2103 c.c. modificato dal Jobs Act). Genera diritto al risarcimento del danno professionale e biologico.", esempio: "Dirigente assegnato a compiti meramente esecutivi senza giustificato motivo: demansionamento.", correlate: ["Diritto del lavoro"] },
    { l: "L", k: "licenziamento-giusta-causa", t: "Licenziamento per giusta causa", cat: "Lavoro", def: "Recesso datoriale fondato su una condotta del lavoratore così grave da non consentire la prosecuzione, anche provvisoria, del rapporto. Esclude il preavviso. Sindacabile dal giudice sotto il profilo della gravità e della proporzionalità.", esempio: "Sottrazione di beni aziendali, violenza sul luogo di lavoro, abbandono del posto in fasi critiche.", correlate: ["Diritto del lavoro"] },
    { l: "P", k: "prescrizione", t: "Prescrizione", cat: "Civile", def: "Estinzione del diritto per il decorso del tempo unito all'inerzia del titolare. Termine ordinario decennale (art. 2946 c.c.). Termini brevi quinquennali per fitti, retribuzioni, danni da illecito, cartelle.", esempio: "Cartella notificata nel 2018, mai sollecitata: prescritta nel 2023.", correlate: ["Contenzioso civile", "Diritto tributario"] },
    { l: "U", k: "unione-civile", t: "Unione civile", cat: "Famiglia", def: "Istituto introdotto dalla legge 76/2016 che disciplina la convivenza di due persone maggiorenni dello stesso sesso, con effetti analoghi al matrimonio in tema di obblighi reciproci, regime patrimoniale e successione.", esempio: "Costituzione davanti all'ufficiale di stato civile, dichiarazione congiunta di scelta del regime patrimoniale.", correlate: ["Famiglia LGBTQ+"] },
  ];

  const az = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
  const present = new Set(terms.map(t => t.l));

  const visible = q
    ? terms.filter(t => (t.t + t.def).toLowerCase().includes(q.toLowerCase()))
    : terms;

  const grouped = visible.reduce((acc, t) => {
    (acc[t.l] = acc[t.l] || []).push(t);
    return acc;
  }, {});

  const faq = [
    { q: "Qual è la differenza tra avvocato e procuratore?", a: "Nell'ordinamento italiano i due termini sono oggi sostanzialmente coincidenti: la figura del procuratore legale è stata abolita nel 1997. Si parla ancora di procuratore in senso processuale, per indicare l'avvocato che rappresenta la parte in giudizio in virtù di mandato (procura alle liti)." },
    { q: "Cos'è la prima udienza?", a: "È la prima comparizione delle parti davanti al giudice (art. 183 c.p.c. nel rito civile). In questa sede il giudice verifica la regolarità del contraddittorio, tenta la conciliazione, fissa il calendario delle attività istruttorie." },
    { q: "Cosa significa contumacia?", a: "È la mancata costituzione di una parte nel giudizio. Il processo prosegue ugualmente; le difese non svolte non possono più essere sollevate, salve le eccezioni rilevabili d'ufficio." },
    { q: "Quanto costa una causa?", a: "Le spese si articolano in onorari dell'avvocato (parametri ministeriali), contributo unificato (variabile per scaglione), spese vive (CTU, notifiche). In caso di soccombenza, il giudice condanna alle spese processuali." },
    { q: "Che differenza c'è tra ricorso e appello?", a: "Il ricorso è la forma generale dell'atto introduttivo o impugnatorio; l'appello è uno specifico mezzo di gravame contro le sentenze di primo grado. Si fa appello contro una sentenza di tribunale; si fa ricorso (ad esempio) per cassazione, al TAR, in materia tributaria." },
  ];

  return (
    <div className="sl-root">
      <S2Header />

      {/* HERO */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "120px clamp(24px, 5vw, 96px) 64px",
        display: "grid", gridTemplateColumns: "5fr 7fr", gap: 64, alignItems: "end",
      }}>
        <div>
          <div className="sl-mono" style={{ marginBottom: 48 }}>§ Riferimenti · Glossario</div>
          <h1 style={{
            fontSize: "clamp(64px, 8vw, 124px)",
            lineHeight: 0.95, letterSpacing: "-0.035em", fontWeight: 400,
          }}>
            Glossario<br/>
            <em style={{ fontStyle: "italic", color: "var(--text-muted)" }}>legale.</em>
          </h1>
        </div>
        <div style={{ paddingBottom: 24 }}>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 22, lineHeight: 1.5, color: "var(--text)", maxWidth: "44ch", marginBottom: 24,
          }}>
            Sessanta termini essenziali del diritto italiano spiegati in linguaggio chiaro. Aggiornato a maggio 2026.
          </p>
          <div className="sl-mono">{terms.length} termini · 24 categorie</div>
        </div>
      </section>

      {/* SEARCH + A-Z sticky */}
      <section style={{
        position: "sticky", top: 73, zIndex: 10,
        background: "var(--background)",
        borderTop: "1px solid var(--border)",
        borderBottom: "1px solid var(--border)",
        padding: "20px clamp(24px, 5vw, 96px)",
      }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "1fr auto", gap: 48, alignItems: "center" }}>
          <input type="search" value={q} onChange={e => setQ(e.target.value)}
            placeholder="Cerca un termine — es. cartella, prescrizione, affidamento…"
            style={{
              width: "100%", border: 0, borderBottom: "1px solid var(--border)",
              padding: "12px 0", background: "transparent",
              fontFamily: "var(--font-body)", fontSize: 17,
              color: "var(--primary)", outline: "none",
            }} />
          <nav style={{ display: "flex", gap: 12, flexWrap: "wrap" }}>
            {az.map(L => (
              <a key={L} href={present.has(L) ? `#${L}` : undefined}
                 className="sl-mono"
                 style={{
                   color: present.has(L) ? "var(--primary)" : "var(--border)",
                   pointerEvents: present.has(L) ? "auto" : "none",
                   textDecoration: "none", padding: "0 4px",
                 }}>
                {L}
              </a>
            ))}
          </nav>
        </div>
      </section>

      {/* LISTA TERMINI */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "96px clamp(24px, 5vw, 96px) 128px" }}>
        {Object.keys(grouped).length === 0 ? (
          <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 24, color: "var(--text-muted)", textAlign: "center", padding: "96px 0" }}>
            Nessun risultato per "{q}".
          </p>
        ) : Object.keys(grouped).sort().map(letter => (
          <div key={letter} id={letter} style={{ marginBottom: 80 }}>
            <h2 style={{
              fontFamily: "var(--font-display)", fontStyle: "italic",
              fontSize: 96, color: "var(--accent)", lineHeight: 0.9,
              letterSpacing: "-0.03em", marginBottom: 32,
              borderBottom: "1px solid var(--border)", paddingBottom: 24,
            }}>
              {letter}
            </h2>
            <dl style={{ margin: 0 }}>
              {grouped[letter].map(t => <GlossEntry key={t.k} t={t} />)}
            </dl>
          </div>
        ))}
      </section>

      {/* FAQ glossario */}
      <section style={{ background: "var(--surface)", padding: "128px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ Domande generali</div>
          <div>
            <h2 style={{ fontSize: "clamp(40px, 4.5vw, 64px)", letterSpacing: "-0.02em", marginBottom: 48 }}>
              Cinque chiarimenti.
            </h2>
            <div className="sl-acc">
              {faq.map((f, i) => (
                <div key={i} className="sl-acc__item" data-open={openFaq === i}>
                  <button className="sl-acc__btn" onClick={() => setOpenFaq(openFaq === i ? -1 : i)}>
                    <span>{f.q}</span>
                    <span className="sl-acc__icon">+</span>
                  </button>
                  <div className="sl-acc__panel"><div className="sl-acc__inner">{f.a}</div></div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "128px clamp(24px, 5vw, 96px) 160px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64 }}>
          <div className="sl-mono">§ Manca un termine?</div>
          <div>
            <h2 style={{ fontSize: "clamp(48px, 5.5vw, 80px)", letterSpacing: "-0.025em", lineHeight: 0.98, marginBottom: 32 }}>
              Non trovi un termine?<br/>
              <em style={{ fontStyle: "italic", color: "var(--accent)" }}>Scrivici.</em>
            </h2>
            <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 20, color: "var(--text)", lineHeight: 1.5, maxWidth: "44ch", marginBottom: 48 }}>
              Il glossario è in continua espansione. Suggerisci un termine: lo aggiungeremo nella prossima revisione.
            </p>
            <a href="/contatti/" className="sl-btn sl-btn--primary">
              Suggerisci un termine
              <span className="arrow">→</span>
            </a>
          </div>
        </div>
      </section>

      <S2Footer />
    </div>
  );
}

function GlossEntry({ t }) {
  const [hover, setHover] = React.useState(false);
  return (
    <div id={t.k}
         onMouseEnter={() => setHover(true)} onMouseLeave={() => setHover(false)}
         style={{
           display: "grid", gridTemplateColumns: "30% 70%", gap: 48,
           padding: "32px 0", borderBottom: "1px solid var(--border)",
           transform: hover ? "translateX(4px)" : "translateX(0)",
           transition: "transform 200ms var(--ease-editorial)",
         }}>
      <dt style={{ paddingRight: 24 }}>
        <div className="sl-mono" style={{ marginBottom: 8 }}>{t.cat}</div>
        <div style={{
          fontFamily: "var(--font-display)", fontSize: 28,
          color: hover ? "var(--accent)" : "var(--primary)",
          letterSpacing: "-0.015em", lineHeight: 1.15,
          transition: "color 200ms",
        }}>{t.t}</div>
      </dt>
      <dd style={{ margin: 0 }}>
        <p style={{ fontSize: 17, lineHeight: 1.65, color: "var(--text)", marginBottom: 16 }}>
          {t.def}
        </p>
        <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 16, color: "var(--text-muted)", lineHeight: 1.55, marginBottom: 16 }}>
          <strong style={{ fontWeight: 500, fontStyle: "normal", color: "var(--primary)" }}>Esempio. </strong>
          {t.esempio}
        </p>
        <div style={{ display: "flex", gap: 16, flexWrap: "wrap", alignItems: "center" }}>
          <span className="sl-mono">Aree correlate:</span>
          {t.correlate.map(c => <a key={c} href="#" className="sl-link" style={{ fontSize: 14 }}>{c}</a>)}
        </div>
      </dd>
    </div>
  );
}

window.S2Glossario = S2Glossario;
