/* global React */
/* Studio Legale Saltelli — LOGO DEFINITIVO v1.1
   Fonte: Studio Legale Saltelli - Logo.html (master guidelines)
   Sistema editoriale a 3 piani tipografici, hairlines, accento bronzo.

   Componenti:
     <SLLogoPrimary />     — uso istituzionale, hero, copertine
                             3 righe: kicker hairlined / Saltelli italic / payoff con bronze dots
     <SLLogoHorizontal />  — Variante A · header sito, email signature
     <SLLogoMonogram />    — Variante B · favicon, avatar social, sigillo
     <SLLogoStack />       — Variante C · footer, watermark documenti

   Tutte accettano tone="dark" (default · sfondi chiari) o tone="light" (sfondi navy).
*/

function _palette(tone) {
  const isDark = tone === "dark";
  return {
    main:  isDark ? "var(--primary)" : "var(--background)",
    muted: isDark ? "var(--text-muted)" : "rgba(255,255,255,0.55)",
    rule:  isDark ? "var(--primary)" : "var(--background)",
    accent: "var(--accent)",
  };
}

/* ─── PRIMARIO ─────────────────────────────────────────── */
function SLLogoPrimary({ tone = "dark", size = "md", href = "/" }) {
  const sizes = {
    sm: { kicker: 11, name: 56,  payoff: 10, kickGap: 14, kickMb: 14, payMt: 14 },
    md: { kicker: 14, name: 96,  payoff: 12, kickGap: 18, kickMb: 18, payMt: 18 },
    lg: { kicker: 16, name: 132, payoff: 13, kickGap: 18, kickMb: 18, payMt: 18 },
  }[size];
  const c = _palette(tone);

  const dot = (
    <span style={{
      display: "inline-block", width: 3, height: 3,
      background: c.accent, borderRadius: "50%",
      transform: "translateY(-3px)", margin: "0 10px",
    }} />
  );

  return (
    <a href={href} aria-label="Studio Legale Saltelli — Home" style={{
      display: "inline-grid", justifyItems: "center", gap: 0,
      color: c.main, textDecoration: "none", textAlign: "center",
    }}>
      <div style={{
        display: "grid", gridTemplateColumns: "1fr auto 1fr",
        alignItems: "center", gap: sizes.kickGap,
        marginBottom: sizes.kickMb, width: "100%", minWidth: 280,
      }}>
        <span style={{ height: 1, background: c.rule, alignSelf: "center" }} />
        <span style={{
          fontFamily: "var(--font-body)", fontWeight: 500,
          fontSize: sizes.kicker, letterSpacing: "0.42em",
          textTransform: "uppercase", color: c.main,
          whiteSpace: "nowrap", paddingLeft: "0.42em",
        }}>Studio<span style={{ margin: "0 6px" }}>·</span>Legale</span>
        <span style={{ height: 1, background: c.rule, alignSelf: "center" }} />
      </div>
      <div style={{
        fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
        fontSize: sizes.name, lineHeight: 0.95, letterSpacing: "-0.025em",
        color: c.main,
      }}>
        <span style={{ color: c.accent }}>S</span>altelli
      </div>
      <div style={{
        fontFamily: "var(--font-mono)", fontSize: sizes.payoff,
        letterSpacing: "0.32em", textTransform: "uppercase",
        color: c.muted, marginTop: sizes.payMt,
      }}>
        Napoli{dot}Chiaia{dot}Dal 1999
      </div>
    </a>
  );
}

/* ─── VARIANTE A · ORIZZONTALE ─────────────────────────── */
function SLLogoHorizontal({ tone = "dark", size = "md", href = "/" }) {
  const sizes = {
    sm: { top: 9,  bot: 8,  rule: 28, name: 24, gap: 18 },
    md: { top: 10, bot: 9,  rule: 36, name: 32, gap: 24 },
    lg: { top: 12, bot: 10, rule: 44, name: 40, gap: 28 },
  }[size];
  const c = _palette(tone);

  return (
    <a href={href} aria-label="Studio Legale Saltelli — Home" style={{
      display: "inline-grid", gridTemplateColumns: "auto 1px auto",
      alignItems: "center", gap: sizes.gap,
      color: c.main, textDecoration: "none", whiteSpace: "nowrap", textAlign: "left",
    }}>
      <div style={{ display: "grid", justifyItems: "end", gap: 2 }}>
        <span style={{
          fontFamily: "var(--font-body)", fontWeight: 500,
          fontSize: sizes.top, letterSpacing: "0.32em", textTransform: "uppercase",
          color: c.main,
        }}>Studio Legale</span>
        <span style={{
          fontFamily: "var(--font-mono)", fontSize: sizes.bot,
          letterSpacing: "0.24em", textTransform: "uppercase",
          color: c.muted,
        }}>Napoli · 1999</span>
      </div>
      <div style={{ height: sizes.rule, width: 1, background: c.rule }} />
      <div style={{
        fontFamily: "var(--font-display)", fontStyle: "italic", fontWeight: 400,
        fontSize: sizes.name, lineHeight: 1, letterSpacing: "-0.02em",
        color: c.main, whiteSpace: "nowrap",
      }}>
        <span style={{ color: c.accent }}>S</span>altelli
      </div>
    </a>
  );
}

/* ─── VARIANTE B · MONOGRAMMA ──────────────────────────── */
function SLLogoMonogram({ tone = "dark", size = "md", href = "/", showLabel = true }) {
  const sizes = {
    sm: { seal: 56, glyph: 28, name: 9,  pay: 8 },
    md: { seal: 88, glyph: 44, name: 11, pay: 9 },
    lg: { seal: 120, glyph: 60, name: 13, pay: 10 },
  }[size];
  const c = _palette(tone);

  return (
    <a href={href} aria-label="Studio Legale Saltelli — Home" style={{
      display: "grid", justifyItems: "center", gap: 14,
      color: c.main, textDecoration: "none", textAlign: "center",
    }}>
      <div style={{
        width: sizes.seal, height: sizes.seal,
        border: `1px solid ${c.main === "var(--primary)" ? "var(--primary)" : "var(--background)"}`,
        borderRadius: "50%", display: "grid", placeItems: "center",
        fontFamily: "var(--font-display)", fontStyle: "italic",
        fontSize: sizes.glyph, lineHeight: 1, color: c.main,
        position: "relative",
      }}>
        S
        <span style={{
          position: "absolute", inset: 4,
          border: `1px solid ${c.accent}`, borderRadius: "50%",
          opacity: 0.55,
        }} />
      </div>
      {showLabel && (
        <>
          <span style={{
            fontFamily: "var(--font-body)", fontWeight: 500,
            fontSize: sizes.name, letterSpacing: "0.36em",
            textTransform: "uppercase", color: c.main,
          }}>Studio Legale Saltelli</span>
          <span style={{
            fontFamily: "var(--font-mono)", fontSize: sizes.pay,
            letterSpacing: "0.24em", textTransform: "uppercase",
            color: c.muted,
          }}>Napoli · Chiaia</span>
        </>
      )}
    </a>
  );
}

/* ─── VARIANTE C · COMPATTO / STACK ────────────────────── */
function SLLogoStack({ tone = "dark", size = "md", href = "/" }) {
  const sizes = {
    sm: { row1: 9,  row2: 28, row3: 8,  tracking1: "0.36em" },
    md: { row1: 11, row2: 56, row3: 9,  tracking1: "0.42em" },
    lg: { row1: 13, row2: 72, row3: 10, tracking1: "0.42em" },
  }[size];
  const c = _palette(tone);

  return (
    <a href={href} aria-label="Studio Legale Saltelli — Home" style={{
      display: "grid", justifyItems: "center", gap: 4,
      color: c.main, textDecoration: "none", textAlign: "center",
    }}>
      <span style={{
        fontFamily: "var(--font-body)", fontWeight: 500,
        fontSize: sizes.row1, letterSpacing: sizes.tracking1,
        textTransform: "uppercase", color: c.main, whiteSpace: "nowrap",
      }}>Studio Legale</span>
      <span style={{
        fontFamily: "var(--font-display)", fontWeight: 400, fontStyle: "italic",
        fontSize: sizes.row2, lineHeight: 0.95, letterSpacing: "-0.02em",
        color: c.main, whiteSpace: "nowrap",
      }}>
        <span style={{ color: c.accent }}>S</span>altelli
      </span>
      <span style={{
        fontFamily: "var(--font-mono)", fontSize: sizes.row3,
        letterSpacing: "0.24em", textTransform: "uppercase",
        color: c.muted, marginTop: 4, whiteSpace: "nowrap",
      }}>Napoli · Dal 1999</span>
    </a>
  );
}

/* Alias retro-compat per file vecchi che importavano SLLogoCompact */
const SLLogoCompact = SLLogoStack;

Object.assign(window, {
  SLLogoPrimary, SLLogoHorizontal, SLLogoMonogram, SLLogoStack, SLLogoCompact,
});
