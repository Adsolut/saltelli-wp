/* global React, S2Header, S2Footer */
/* Sessione 2 R2 · /blog/ — ARCHIVE EDITORIALE
   Spec key: hero count + tab categorie · featured 8/4 · grid 3-col card
   no-rounded · paginazione minimal · sidebar opzionale (qui inline footer)
   Schema JSON-LD: WebPage + Blog + ItemList di Article */

function S2BlogArchive() {
  const [cat, setCat] = React.useState("Tutti");

  const cats = ["Tutti", "Diritto Tributario", "Famiglia", "Lavoro", "Cartelle", "Cassazione", "Editorial"];

  const featured = {
    cat: "Diritto Tributario",
    title: "Cartelle esattoriali: cosa cambia con la riforma 2026",
    lede: "La riforma del processo tributario introduce termini più stringenti e nuovi vizi di notifica. Cosa significa, in pratica, per chi riceve una cartella oggi.",
    date: "16 Aprile 2026",
    author: "Emiliano Saltelli",
    read: "8 min",
  };

  const posts = [
    { cat: "Diritto Tributario", title: "Accertamento sintetico: la sentenza che ribalta tutto", excerpt: "La Cassazione torna sulla presunzione legale relativa e indica un nuovo standard probatorio per il contribuente.", date: "02 Apr 2026", author: "E. Saltelli", read: "12 min" },
    { cat: "Famiglia", title: "Trascrizione integrale di atto di nascita: il caso napoletano", excerpt: "Primo riconoscimento in Campania per due madri. Note a margine sulla giurisprudenza recente.", date: "28 Mar 2026", author: "A. Battista", read: "9 min" },
    { cat: "Cassazione", title: "Reati tributari: il ravvedimento operoso oltre soglia", excerpt: "Quando il pagamento integrale prima del dibattimento esclude la punibilità. La pronuncia 4521/2025.", date: "21 Mar 2026", author: "E. Saltelli", read: "6 min" },
    { cat: "Lavoro", title: "Licenziamento per giusta causa: l'onere della prova", excerpt: "La condotta antisindacale e i parametri del Tribunale di Napoli nelle sentenze 2024-2025.", date: "12 Mar 2026", author: "F. Saltelli", read: "10 min" },
    { cat: "Cartelle", title: "Prescrizione delle cartelle: il termine quinquennale", excerpt: "Quando la prescrizione breve si applica alle cartelle esattoriali e cosa fare in pratica.", date: "04 Mar 2026", author: "E. Saltelli", read: "7 min" },
    { cat: "Editorial", title: "Vent'anni di studio. Una nota personale", excerpt: "Pensieri sull'evoluzione della professione forense a Napoli, dal 1999 a oggi.", date: "21 Feb 2026", author: "E. Saltelli", read: "5 min" },
  ];

  const visible = cat === "Tutti" ? posts : posts.filter(p => p.cat === cat);

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
          <div className="sl-mono" style={{ marginBottom: 48 }}>§ Editoriale · Saltelli</div>
          <h1 style={{
            fontSize: "clamp(72px, 9vw, 140px)",
            lineHeight: 0.95, letterSpacing: "-0.035em", fontWeight: 400,
          }}>
            Editoriale.
          </h1>
        </div>
        <div style={{ paddingBottom: 24 }}>
          <p style={{
            fontFamily: "var(--font-display)", fontStyle: "italic",
            fontSize: 24, lineHeight: 1.5, color: "var(--text)", maxWidth: "44ch", marginBottom: 24,
          }}>
            Articoli, casi vinti, novità giurisprudenziali da Studio Legale Saltelli &amp; Partners. Aggiornato settimanalmente.
          </p>
          <div className="sl-mono">326 articoli · 12 categorie · agg. {featured.date}</div>
        </div>
      </section>

      {/* CATEGORY TABS — sticky */}
      <section style={{
        position: "sticky", top: 73, zIndex: 10,
        background: "var(--background)",
        borderTop: "1px solid var(--border)",
        borderBottom: "1px solid var(--border)",
      }}>
        <div style={{
          maxWidth: 1440, margin: "0 auto",
          padding: "20px clamp(24px, 5vw, 96px)",
          display: "flex", gap: 32, flexWrap: "wrap", overflowX: "auto",
        }}>
          {cats.map(c => (
            <button key={c} onClick={() => setCat(c)} style={{
              background: "none", border: 0, cursor: "pointer", padding: "6px 0",
              color: cat === c ? "var(--primary)" : "var(--text-muted)",
              borderBottom: cat === c ? "1px solid var(--accent)" : "1px solid transparent",
              fontFamily: "var(--font-mono)", fontSize: 12,
              letterSpacing: "0.08em", textTransform: "uppercase",
              whiteSpace: "nowrap", transition: "all 200ms var(--ease-editorial)",
            }}>{c}</button>
          ))}
        </div>
      </section>

      {/* FEATURED */}
      <section style={{
        maxWidth: 1440, margin: "0 auto",
        padding: "96px clamp(24px, 5vw, 96px) 96px",
        borderBottom: "1px solid var(--border)",
      }}>
        <div className="sl-mono" style={{ marginBottom: 48, color: "var(--accent)" }}>§ In evidenza · {featured.date}</div>
        <a href="#" style={{
          display: "grid", gridTemplateColumns: "8fr 4fr", gap: 64,
          textDecoration: "none", color: "inherit",
        }}>
          {/* Featured image 16:9 */}
          <div style={{
            aspectRatio: "16 / 9",
            background: "linear-gradient(135deg, #c8c5be 0%, #4a4540 100%)",
            position: "relative", overflow: "hidden",
            border: "1px solid var(--border)",
            filter: "grayscale(0.6) contrast(1.05)",
          }}>
            <div className="sl-mono" style={{ position: "absolute", bottom: 16, left: 16, color: "rgba(255,255,255,0.85)" }}>
              Foto editoriale · 16:9
            </div>
            <div className="sl-mono" style={{ position: "absolute", top: 16, left: 16, color: "rgba(255,255,255,0.85)" }}>
              Plate · IV
            </div>
          </div>
          <div style={{ display: "flex", flexDirection: "column", justifyContent: "space-between" }}>
            <div>
              <div className="sl-mono" style={{ marginBottom: 24 }}>{featured.cat}</div>
              <h2 style={{
                fontFamily: "var(--font-display)", fontSize: "clamp(32px, 3.5vw, 48px)",
                lineHeight: 1.1, letterSpacing: "-0.02em", color: "var(--primary)",
                marginBottom: 24,
              }}>
                {featured.title}
              </h2>
              <p style={{
                fontFamily: "var(--font-display)", fontStyle: "italic",
                fontSize: 19, lineHeight: 1.5, color: "var(--text)",
                marginBottom: 32, maxWidth: "42ch",
              }}>
                {featured.lede}
              </p>
            </div>
            <div>
              <div className="sl-mono" style={{ marginBottom: 16 }}>
                {featured.date} · {featured.author} · {featured.read}
              </div>
              <span className="sl-btn">Leggi l'articolo<span className="arrow">→</span></span>
            </div>
          </div>
        </a>
      </section>

      {/* GRID 3-col */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "96px clamp(24px, 5vw, 96px)" }}>
        <div style={{ display: "grid", gridTemplateColumns: "3fr 9fr", gap: 64, marginBottom: 64 }}>
          <div className="sl-mono">§ Archivio · {visible.length} di {posts.length}</div>
          <h2 style={{ fontSize: "clamp(36px, 4vw, 56px)", letterSpacing: "-0.02em" }}>
            {cat === "Tutti" ? "Tutti gli articoli." : <>{cat}.</>}
          </h2>
        </div>

        <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 64 }}>
          {visible.map((p, i) => <BlogCard key={i} p={p} />)}
        </div>
      </section>

      {/* PAGINATION editoriale */}
      <section style={{ maxWidth: 1440, margin: "0 auto", padding: "32px clamp(24px, 5vw, 96px) 128px" }}>
        <div style={{
          paddingTop: 32, borderTop: "1px solid var(--border)",
          display: "flex", justifyContent: "space-between", alignItems: "center",
        }}>
          <a href="#" className="sl-mono" style={{ color: "var(--text-muted)" }}>← Precedenti</a>
          <div className="sl-mono">1 — 12 di 326</div>
          <a href="#" className="sl-mono" style={{ color: "var(--primary)" }}>Successivi →</a>
        </div>
      </section>

      {/* CTA NEWSLETTER inline */}
      <section style={{ background: "var(--surface)", padding: "96px clamp(24px, 5vw, 96px)" }}>
        <div style={{ maxWidth: 1440, margin: "0 auto", display: "grid", gridTemplateColumns: "5fr 7fr", gap: 64, alignItems: "center" }}>
          <div>
            <div className="sl-mono" style={{ marginBottom: 24 }}>§ Newsletter</div>
            <h2 style={{ fontSize: "clamp(36px, 4vw, 56px)", letterSpacing: "-0.02em", lineHeight: 1.05 }}>
              Un articolo<br/>
              <em style={{ fontStyle: "italic", color: "var(--accent)" }}>al mese.</em>
            </h2>
          </div>
          <div>
            <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: 20, color: "var(--text)", marginBottom: 32, maxWidth: "44ch" }}>
              Una sola mail al mese. Solo casi vinti, novità giurisprudenziali, e qualche nota personale. Niente promozione.
            </p>
            <form onSubmit={e => e.preventDefault()} style={{ display: "grid", gridTemplateColumns: "1fr auto", gap: 24, alignItems: "end", maxWidth: 480 }}>
              <label style={{ display: "block" }}>
                <span className="sl-mono" style={{ display: "block", marginBottom: 8 }}>Email</span>
                <input type="email" required placeholder="lei@esempio.it" style={{
                  width: "100%", border: 0, borderBottom: "1px solid var(--primary)",
                  background: "transparent", padding: "8px 0",
                  fontFamily: "var(--font-body)", fontSize: 17, color: "var(--primary)", outline: "none",
                }} />
              </label>
              <button type="submit" className="sl-btn">Iscriviti<span className="arrow">→</span></button>
            </form>
          </div>
        </div>
      </section>

      <S2Footer />
    </div>
  );
}

function BlogCard({ p }) {
  const [hover, setHover] = React.useState(false);
  return (
    <a href="#"
       onMouseEnter={() => setHover(true)} onMouseLeave={() => setHover(false)}
       style={{ textDecoration: "none", color: "inherit", display: "grid", gap: 20 }}>
      {/* Image 4:3 */}
      <div style={{
        aspectRatio: "4 / 3",
        background: "linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%)",
        overflow: "hidden", border: "1px solid var(--border)",
        filter: "grayscale(0.85) contrast(1.05)",
      }}>
        <div style={{
          width: "100%", height: "100%",
          background: "linear-gradient(135deg, #b8b4ac 0%, #5a564f 100%)",
          transform: hover ? "scale(1.03)" : "scale(1)",
          transition: "transform 600ms var(--ease-editorial)",
        }} />
      </div>
      <div className="sl-mono" style={{ color: hover ? "var(--accent)" : "var(--text-muted)", transition: "color 300ms" }}>
        {p.cat}
      </div>
      <h3 style={{
        fontFamily: "var(--font-display)", fontSize: 24, lineHeight: 1.25,
        letterSpacing: "-0.015em", color: "var(--primary)",
        display: "-webkit-box", WebkitBoxOrient: "vertical", WebkitLineClamp: 3, overflow: "hidden",
      }}>{p.title}</h3>
      <p style={{
        fontSize: 16, lineHeight: 1.55, color: "var(--text-muted)",
        display: "-webkit-box", WebkitBoxOrient: "vertical", WebkitLineClamp: 2, overflow: "hidden",
      }}>{p.excerpt}</p>
      <div className="sl-mono" style={{ paddingTop: 8, borderTop: "1px solid var(--border)" }}>
        {p.date} · {p.author} · {p.read}
      </div>
    </a>
  );
}

window.S2BlogArchive = S2BlogArchive;
