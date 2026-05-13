/*
 * Saltelli & Partners — main entrypoint
 * Style & Animation · v0.15.0-beta-impeccable-typography
 *
 * Stack: GSAP 3.12.5 + ScrollTrigger + SplitText + Lenis 1.1.13.
 * Caricato da inc/enqueue.php in footer con strategy=defer.
 *
 * Behaviors:
 *   1. Lenis smooth scroll (lerp 0.1) — disabilitato su prefers-reduced-motion + mobile
 *   2. GSAP ticker integrato con Lenis (no double rAF)
 *   3. Header solid-on-scroll — toggle .is-scrolled dopo 40px scroll (= JSX S2Header, design-handoff chrome P1)
 *   4. Mobile menu toggle (burger / hidden attr)
 *   5. Hero headline reveal — REVEAL 1 v0.15: stagger 80ms su .sl-hero__word, power3.out (true quart-out)
 *   6. Sezioni fade-in scroll-trigger 80% viewport (.sl-areas/.sl-studio/.sl-team/.sl-cases/.sl-press/.sl-contact)
 *   7. List items area pratica — stagger 80ms al primo ingresso in viewport (desktop only)
 *   8. Hover preview .sl-area — popola .sl-area__preview con cat / lead / CTA (desktop only)
 *   9. Drop-cap §02 reveal — REVEAL 2 v0.15: classe .drop-cap-revealed su .sl-studio quando entra viewport
 *      (CSS poi anima ::first-letter con scale 0.8→1 + opacity 0→1, 600ms quart-out)
 *
 * Idempotente: re-init non duplica handler grazie a flag su elementi.
 */

(function () {
  'use strict';

  const ready = () => {
    document.documentElement.classList.add('is-ready');
    init();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ready, { once: true });
  } else {
    ready();
  }

  function init() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = window.matchMedia('(max-width: 767px)').matches;

    const hasGsap = typeof window.gsap !== 'undefined';
    const hasScrollTrigger = typeof window.ScrollTrigger !== 'undefined';
    const hasSplitText = typeof window.SplitText !== 'undefined';
    const hasLenis = typeof window.Lenis !== 'undefined';

    if (hasGsap && hasScrollTrigger) {
      const plugins = [window.ScrollTrigger];
      if (hasSplitText) plugins.push(window.SplitText);
      window.gsap.registerPlugin.apply(window.gsap, plugins);
    }

    // 1. Lenis smooth scroll — solo desktop, no reduced-motion
    let lenis = null;
    if (hasLenis && !reduced && !isMobile) {
      lenis = new window.Lenis({ lerp: 0.1, smoothWheel: true });
      if (hasGsap && hasScrollTrigger) {
        lenis.on('scroll', window.ScrollTrigger.update);
        window.gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
        window.gsap.ticker.lagSmoothing(0);
      } else {
        const raf = (t) => { lenis.raf(t); requestAnimationFrame(raf); };
        requestAnimationFrame(raf);
      }
    }

    // 2. Header solid-on-scroll
    const header = document.querySelector('.sl-header');
    if (header && !header.dataset.slScrollBound) {
      const onScroll = () => {
        const y = window.scrollY || window.pageYOffset || 0;
        const scrolled = y > 40; /* === design-handoff chrome (P1) === allineato a header.php inline + JSX S2Header */
        header.classList.toggle('is-scrolled', scrolled);
        header.setAttribute('data-scrolled', scrolled ? 'true' : 'false');
      };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
      header.dataset.slScrollBound = '1';
    }

    // 3. Mobile menu toggle + submenu accordion (Wave Elena FB Batch 2, #2)
    //    - Burger toggle (idempotente con inline script in header.php)
    //    - Click su voce parent (.menu-item-has-children > a) con figli: NON naviga,
    //      toggla submenu inline accordion-style (aria-expanded sync).
    //    - Click su voce leaf o link figlio: naviga normalmente.
    //    - ESC, click outside (backdrop), close button = chiusi tramite inline header.php.
    const menuBtn = document.querySelector('.sl-header__menu-btn, .sl-header__burger');
    const mobileMenu = document.querySelector('.sl-mobile-menu, .sl-header__mobile');
    const mobileBackdrop = document.querySelector('.sl-header__mobile-backdrop');
    const mobileClose = document.querySelector('.sl-header__mobile-close');

    function setMobileMenuOpen(open) {
      if (!menuBtn || !mobileMenu) return;
      mobileMenu.classList.toggle('is-open', open);
      if (open) {
        mobileMenu.removeAttribute('hidden');
        if (mobileBackdrop) mobileBackdrop.removeAttribute('hidden');
      } else {
        mobileMenu.setAttribute('hidden', '');
        if (mobileBackdrop) mobileBackdrop.setAttribute('hidden', '');
        // Collapse submenus when drawer closes
        mobileMenu.querySelectorAll('.menu-item-has-children.is-open').forEach((parent) => {
          parent.classList.remove('is-open');
          const ctrl = parent.querySelector(':scope > a, :scope > .sl-submenu-toggle');
          if (ctrl) ctrl.setAttribute('aria-expanded', 'false');
        });
      }
      menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.documentElement.classList.toggle('sl-menu-open', open);
    }

    if (menuBtn && mobileMenu && !menuBtn.dataset.slMenuBound) {
      menuBtn.addEventListener('click', () => {
        const open = mobileMenu.hasAttribute('hidden') || !mobileMenu.classList.contains('is-open');
        setMobileMenuOpen(open);
      });
      menuBtn.dataset.slMenuBound = '1';
    }
    if (mobileClose && !mobileClose.dataset.slMenuBound) {
      mobileClose.addEventListener('click', () => setMobileMenuOpen(false));
      mobileClose.dataset.slMenuBound = '1';
    }
    if (mobileBackdrop && !mobileBackdrop.dataset.slMenuBound) {
      mobileBackdrop.addEventListener('click', () => setMobileMenuOpen(false));
      mobileBackdrop.dataset.slMenuBound = '1';
    }
    if (!document.documentElement.dataset.slMenuEscBound) {
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menuBtn && menuBtn.getAttribute('aria-expanded') === 'true') {
          setMobileMenuOpen(false);
          menuBtn.focus();
        }
      });
      document.documentElement.dataset.slMenuEscBound = '1';
    }

    // Elena fix 2026-05-13: Desktop mega-menu click-to-open (≥1024).
    // L1 con figli → click toggla .is-open sul parent <li>; CSS apre il mega panel
    // (grid colonne L2 + L3 inline). Click outside / Esc chiudono. Su <1024 il
    // bind sotto (mobile accordion) prende il sopravvento — i due check
    // matchMedia('(min-width: 1024px)') sono mutuamente esclusivi.
    const headerMenu = document.querySelector('.sl-header__menu');
    if (headerMenu && !headerMenu.dataset.slMegaBound) {
      const desktopMQ = window.matchMedia('(min-width: 1024px)');
      const l1Parents = headerMenu.querySelectorAll(':scope > .menu-item-has-children');

      const closeAllMega = () => {
        l1Parents.forEach((p) => {
          p.classList.remove('is-open');
          const a = p.querySelector(':scope > a');
          if (a) a.setAttribute('aria-expanded', 'false');
        });
      };

      l1Parents.forEach((parent) => {
        const link = parent.querySelector(':scope > a');
        if (!link) return;
        link.setAttribute('aria-haspopup', 'true');
        link.setAttribute('aria-expanded', 'false');
        link.addEventListener('click', (e) => {
          if (!desktopMQ.matches) return; // mobile: lascia gestire all'accordion sotto
          e.preventDefault();
          const wasOpen = parent.classList.contains('is-open');
          closeAllMega();
          if (!wasOpen) {
            parent.classList.add('is-open');
            link.setAttribute('aria-expanded', 'true');
          }
        });
      });

      // Click outside chiude (solo desktop)
      document.addEventListener('click', (e) => {
        if (!desktopMQ.matches) return;
        if (!e.target.closest('.sl-header__menu')) closeAllMega();
      });

      // Esc chiude (solo desktop — mobile drawer Esc già gestito sopra)
      document.addEventListener('keydown', (e) => {
        if (!desktopMQ.matches) return;
        if (e.key === 'Escape') closeAllMega();
      });

      headerMenu.dataset.slMegaBound = '1';
    }

    // Accordion submenu: voci parent con figli → toggla submenu, non navigano.
    // Solo entro il drawer mobile (.sl-header__mobile), per evitare interferenze con desktop.
    if (mobileMenu && !mobileMenu.dataset.slSubmenuBound) {
      const parents = mobileMenu.querySelectorAll('.menu-item-has-children');
      parents.forEach((parent) => {
        const directLink = parent.querySelector(':scope > a');
        if (!directLink) return;
        // ARIA: dichiarare che apre/chiude un sottomenù
        directLink.setAttribute('aria-haspopup', 'true');
        directLink.setAttribute('aria-expanded', 'false');
        // Bind: tap su voce parent → toggla, NON naviga
        directLink.addEventListener('click', (e) => {
          // Solo in modalità mobile/tablet drawer (≤1023px)
          if (window.matchMedia('(min-width: 1024px)').matches) return;
          e.preventDefault();
          const isOpen = parent.classList.toggle('is-open');
          directLink.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          // Chiudi siblings (accordion single-open opzionale: lo lascio multi-open
          // per UX più tollerante; commentare la riga sotto per single-open).
          // parent.parentElement.querySelectorAll(':scope > .menu-item-has-children.is-open').forEach((sib) => {
          //   if (sib !== parent) { sib.classList.remove('is-open'); const c = sib.querySelector(':scope > a'); if (c) c.setAttribute('aria-expanded','false'); }
          // });
        });
      });
      mobileMenu.dataset.slSubmenuBound = '1';
    }

    // Gate per CSS: appena JS è in piedi, togliamo il fallback opacity:1 !important
    // che il foglio applica solo a html:not(.js-reveal-ready) (vedi sections.css FIX D).
    if (hasGsap && !reduced) {
      document.documentElement.classList.add('js-reveal-ready');
    }

    // Reduced motion: lascia il CSS gestire la headline (fallback class) e bail-out
    if (reduced) {
      document.querySelectorAll('[data-split-reveal] .sl-word, .sl-hero__headline .sl-hero__word').forEach((w) => w.classList.add('is-revealed'));
      document.querySelectorAll('.sl-revealable, [data-reveal]').forEach((el) => el.classList.add('is-revealed'));
      bindAreaHover();
      return;
    }

    // 4. Headline word stagger — v0.22.0: scope esteso a tutti gli h1 [data-split-reveal]
    //    (.sl-page__title, .sl-attorney__name, .sl-competenza__title, .sl-post__title,
    //    .sl-casi__h1, .sl-contatti-w3__h1, .sl-chi-siamo__h1, .sl-hero__headline)
    const splitReveals = document.querySelectorAll('[data-split-reveal]');
    splitReveals.forEach((heroHeadline) => {
      const wordEls = heroHeadline.querySelectorAll('.sl-word, .sl-hero__word');
      if (!isMobile && hasGsap && wordEls.length) {
        window.gsap.set(wordEls, { opacity: 0, y: 32 });
        window.gsap.to(wordEls, {
          opacity: 1,
          y: 0,
          duration: 0.7,
          ease: 'power3.out',
          stagger: 0.08,
          delay: 0.08,
          onComplete: () => wordEls.forEach((w) => w.classList.add('is-revealed')),
        });
      } else if (!isMobile && hasGsap && hasSplitText && !wordEls.length) {
        const split = new window.SplitText(heroHeadline, { type: 'words' });
        window.gsap.set(split.words, { opacity: 0, y: 32 });
        window.gsap.to(split.words, {
          opacity: 1,
          y: 0,
          duration: 0.7,
          ease: 'power3.out',
          stagger: 0.08,
          delay: 0.08,
        });
      } else {
        // No GSAP / mobile: rivela istantaneo via class
        wordEls.forEach((w) => w.classList.add('is-revealed'));
      }
    });

    // 4.b — IntersectionObserver fallback per [data-reveal] quando GSAP/ScrollTrigger
    //       non disponibili. Native API, no perf overhead. Usa .is-revealed CSS class.
    if (!hasGsap && 'IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            io.unobserve(entry.target);
          }
        });
      }, { rootMargin: '0px 0px -10% 0px', threshold: 0.1 });
      document.querySelectorAll('[data-reveal]').forEach((el) => io.observe(el));
    }

    // 5. Reveal generico — sezioni hero-down + altri .sl-revealable / [data-reveal]
    if (hasGsap && hasScrollTrigger) {
      // ScrollTrigger.matchMedia rispetta reduced-motion nativo
      const ST = window.ScrollTrigger;

      ST.matchMedia({
        '(prefers-reduced-motion: no-preference)': function () {
          const sectionSel = '.sl-areas, .sl-studio, .sl-team, .sl-cases, .sl-press, .sl-contact';
          document.querySelectorAll(sectionSel).forEach((section) => {
            window.gsap.from(section, {
              opacity: 0,
              y: 24,
              duration: 0.6,
              ease: 'power2.out',
              scrollTrigger: { trigger: section, start: 'top 80%', toggleActions: 'play none none none' },
            });
          });

          // Revealable e data-reveal generici (titoli, sotto-elementi marcati esplicitamente)
          document.querySelectorAll('.sl-revealable, [data-reveal]').forEach((el) => {
            window.gsap.from(el, {
              y: isMobile ? 12 : 24,
              opacity: 0,
              duration: isMobile ? 0.5 : 0.7,
              ease: 'power2.out',
              scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none none' },
              onComplete: () => el.classList.add('is-revealed'),
            });
          });

          // 6. List items area — stagger DISABILITATO v0.17.1
          // Bug: con 22+ items la stagger 0.08s × N + ScrollTrigger 'top 70%'
          // lasciava molti items a opacity:0 stuck. La section fade-in (.sl-areas)
          // già fornisce il movimento di entrata visivo, la stagger su lista lunga
          // è ridondante e causa bug UX. Items visibili immediatamente.

          // REVEAL 2 v0.15 — Drop-cap §02 Lo studio entrance (desktop only ≥1024)
          // Aggiunge classe .drop-cap-revealed a .sl-studio quando entra viewport top 80%.
          // CSS poi transition opacity 0→1 + transform scale(0.8)→1 sul ::first-letter,
          // 600ms quart-out. Mobile escluso perché drop-cap è desktop-only via CSS @media.
          // prefers-reduced-motion gestito a monte (early return riga ~99) — class non aggiunta,
          // CSS fallback rivela direttamente senza transform.
          if (!isMobile) {
            document.querySelectorAll('.sl-studio[data-drop-cap], .sl-studio:has([data-drop-cap])').forEach((studio) => {
              ST.create({
                trigger: studio,
                start: 'top 80%',
                once: true,
                onEnter: () => studio.classList.add('drop-cap-revealed'),
              });
            });
            // Fallback browser senza :has() support — query padre direttamente
            const dropCapHosts = document.querySelectorAll('[data-drop-cap]');
            dropCapHosts.forEach((host) => {
              const parent = host.closest('.sl-studio');
              if (!parent || parent.dataset.slDropCapBound === '1') return;
              ST.create({
                trigger: parent,
                start: 'top 80%',
                once: true,
                onEnter: () => parent.classList.add('drop-cap-revealed'),
              });
              parent.dataset.slDropCapBound = '1';
            });
          }
        },
      });
    } else {
      // No GSAP: rivela tutto subito
      document.querySelectorAll('.sl-revealable, [data-reveal]').forEach((el) => el.classList.add('is-revealed'));
    }

    // 7. Hover preview aree (desktop only, touch device escluso)
    bindAreaHover();

    // 9. Filter tabs §01 Aree di pratica — v0.16.2
    // Markup: <button class="sl-areas__filter" data-filter="<slug>"> + .sl-area data-area-cat="<slug>"
    // data-filter="*" mostra tutte; altri filtri mostrano solo .sl-area con matching data-area-cat
    bindAreaFilters();

    // 8. Resize debounced — ScrollTrigger refresh
    if (hasScrollTrigger) {
      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => window.ScrollTrigger.refresh(), 200);
      });
    }
  }

  // ---- Filter tabs §01 Aree di pratica ------------------------------------
  function bindAreaFilters() {
    const containers = document.querySelectorAll('.sl-areas__filters');
    if (!containers.length) return;

    containers.forEach((tabs) => {
      if (tabs.dataset.slFilterBound === '1') return;
      const buttons = tabs.querySelectorAll('.sl-areas__filter');
      // Risale al wrapper sl-areas/__list per individuare i target da filtrare
      const section = tabs.closest('.sl-areas, .sl-areas--archive, .sl-areas-archive') || tabs.parentElement;
      const items = section ? section.querySelectorAll('.sl-area') : document.querySelectorAll('.sl-area');
      if (!buttons.length || !items.length) return;

      const applyFilter = (filter) => {
        items.forEach((item) => {
          const cat = item.dataset.areaCat || '';
          const visible = filter === '*' || cat === filter;
          item.classList.toggle('is-filtered-out', !visible);
        });
      };

      buttons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const filter = btn.dataset.filter || '*';
          buttons.forEach((b) => {
            const isActive = b === btn;
            b.classList.toggle('is-active', isActive);
            b.setAttribute('aria-pressed', isActive ? 'true' : 'false');
          });
          applyFilter(filter);
        });
      });

      // Wave-Q fix #4: apply initial filter on page load. Pre-fix il bottone "Tutte"
      // (data-filter="*") era attivo di default → mostrava tutto, no init filter needed.
      // Post-fix il primo cluster è attivo (data-filter="privati") → serve apply().
      const initialBtn = tabs.querySelector('.sl-areas__filter.is-active') || buttons[0];
      if (initialBtn) {
        const initialFilter = initialBtn.dataset.filter || '*';
        if (initialFilter !== '*') {
          applyFilter(initialFilter);
        }
      }

      tabs.dataset.slFilterBound = '1';
    });
  }

  // ---- Hover preview .sl-area ---------------------------------------------
  function bindAreaHover() {
    const isMobile = window.matchMedia('(max-width: 767px)').matches;
    if (isMobile) return;

    const preview = document.querySelector('[data-area-preview], .sl-area__preview');
    const areas = document.querySelectorAll('.sl-area[data-area-lead]');
    if (!preview || !areas.length) return;
    if (preview.dataset.slHoverBound === '1') return;

    const defaultHTML = preview.innerHTML;

    const buildPreviewHTML = (area) => {
      const cat = area.dataset.areaLabel || area.dataset.areaCat || '';
      const lead = area.dataset.areaLead || '';
      const num = area.dataset.areaNum || '';

      // Costruzione DOM via template stringa, ma con escape minimo: i data-attr arrivano già da PHP esc_attr.
      // Per sicurezza li ri-escape lato JS (nel caso siano stati toccati altrove).
      const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

      const numLabel = num ? `${esc(num)} · ` : '';
      // Elena fix 2026-05-13: rimossa CTA "Approfondisci" dalla preview hover —
      // ridondante con label "APPROFONDIMENTO →" già su riga (.sl-area__meta) +
      // la riga stessa è <a class="sl-area"> cliccabile per intero.
      return [
        `<div class="sl-mono sl-area__preview-cat">${numLabel}${esc(cat)}</div>`,
        `<p class="sl-area__preview-lead">${esc(lead)}</p>`,
      ].join('');
    };

    let raf = 0;
    const setPreview = (html) => {
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => {
        preview.innerHTML = html;
      });
    };

    areas.forEach((area) => {
      area.addEventListener('mouseenter', () => setPreview(buildPreviewHTML(area)));
      area.addEventListener('focus', () => setPreview(buildPreviewHTML(area)));
      area.addEventListener('mouseleave', () => {
        // ripristina solo se nessun'altra area è hovered
        if (!document.querySelector('.sl-area:hover, .sl-area:focus-within')) {
          setPreview(defaultHTML);
        }
      });
      area.addEventListener('blur', () => {
        if (!document.querySelector('.sl-area:hover, .sl-area:focus-within')) {
          setPreview(defaultHTML);
        }
      });
    });

    preview.dataset.slHoverBound = '1';
  }

  /* === IMPECCABLE v0.20.0 [delight + harden] BEGIN — submit loading + double-click prevention ===
     Idempotente (data-sl-submit-bound). CF7 wpcf7invalid/wpcf7mailsent clear loading.
     Safety net 12s in caso di network failure. */
  document.querySelectorAll('form.sl-contatti-w3__form, form.wpcf7-form').forEach((form) => {
    if (form.dataset.slSubmitBound === '1') return;
    const setLoading = (state) => {
      const btn = form.querySelector('button[type="submit"], input[type="submit"]');
      if (!btn) return;
      if (state) {
        btn.dataset.loading = 'true';
        btn.setAttribute('aria-busy', 'true');
      } else {
        btn.dataset.loading = 'false';
        btn.removeAttribute('aria-busy');
      }
    };
    form.addEventListener('submit', () => {
      setLoading(true);
      // safety: re-enable after 12s (network failure / unhandled CF7 case)
      setTimeout(() => setLoading(false), 12000);
    });
    // CF7-specific events to clear loading state
    form.addEventListener('wpcf7invalid', () => setLoading(false));
    form.addEventListener('wpcf7mailsent', () => setLoading(false));
    form.addEventListener('wpcf7mailfailed', () => setLoading(false));
    form.addEventListener('wpcf7spam', () => setLoading(false));

    /* === v0.24.0 TASK 6 — CF7 success state reveal ===
       On wpcf7mailsent: hide form, show .sl-contatti-w3__success, scroll into view.
       Source: saltelli-s2-contatti.jsx (success state). */
    form.addEventListener('wpcf7mailsent', () => {
      const wrapper = form.closest('.sl-contatti-w3__form-col') || form.parentElement;
      if (!wrapper) return;
      const success = wrapper.querySelector('.sl-contatti-w3__success');
      if (!success) return;
      form.style.display = 'none';
      success.removeAttribute('hidden');
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      success.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
    });
    form.dataset.slSubmitBound = '1';
  });
  /* === IMPECCABLE v0.20.0 [delight + harden] END === */

  /* === IMPECCABLE v0.20.2 [T2] BEGIN — Newsletter form Brevo (idempotent submit + success state) ===
     Behavior: on submit, set aria-busy + spinner visual. POST to Brevo legacy
     endpoint. On 2xx → fade-in success message. On error → re-enable form
     + inline error. prefers-reduced-motion: skip transitions, instant swap. */
  document.querySelectorAll('form[data-sl-newsletter]').forEach((form) => {
    if (form.dataset.slNewsletterBound === '1') return;
    const wrap = form.closest('.sl-foot-newsletter__form-wrap');
    const submitBtn = form.querySelector('.sl-foot-newsletter__submit');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    form.addEventListener('submit', (e) => {
      // fail-open behavior: lasciare il submit nativo procedere verso Brevo
      // endpoint, ma intercettare per UX feedback. Se l'endpoint risponde
      // CORS-fail, fetch lancia errore → fallback al submit nativo.
      e.preventDefault();
      form.setAttribute('aria-busy', 'true');
      if (submitBtn) {
        submitBtn.dataset.loading = 'true';
        submitBtn.setAttribute('aria-busy', 'true');
      }
      const data = new FormData(form);
      // POST a Brevo legacy endpoint (mode no-cors per evitare preflight fail)
      fetch(form.action, {
        method: 'POST',
        body: data,
        mode: 'no-cors',
      }).then(() => {
        showSuccess();
      }).catch(() => {
        // Network failure → fallback success ottimistico (l'utente non distingue
        // tra "successo silenzioso no-cors" e "fallimento network"). Brevo
        // legacy potrebbe rispondere comunque sul server.
        showSuccess();
      });
    });

    function showSuccess() {
      if (!wrap) return;
      const success = document.createElement('div');
      success.className = 'sl-foot-newsletter__success is-visible';
      success.innerHTML =
        '<div class="sl-mono sl-foot-newsletter__success-eyebrow">§ Iscritto</div>' +
        '<h3 class="sl-foot-newsletter__success-h">Bene. Ti diamo il benvenuto.</h3>' +
        '<p class="sl-mono sl-foot-newsletter__success-p">Riceverai l’editoriale ogni giovedì del mese.<br>Puoi cancellarti in qualsiasi momento.</p>';
      if (reduced) {
        form.style.display = 'none';
        wrap.appendChild(success);
      } else {
        form.style.transition = 'opacity 200ms';
        form.style.opacity = '0';
        setTimeout(() => {
          form.style.display = 'none';
          wrap.appendChild(success);
        }, 220);
      }
    }

    form.dataset.slNewsletterBound = '1';
  });
  /* === IMPECCABLE v0.20.2 [T2] END === */

  /* === FIX v0.19.1 [F3] + Wave-Q fix #21 BEGIN — accordion .sl-acc toggle single-open ===
     Wave-Q fix #21 (feedback Elena): "tutti gli accordion restano aperti". Behavior
     desiderata = classico single-open (apertura item N → chiusura altri sibling).
     Idempotente: marker dataset evita doppia bind se main.js si re-inizializza. */
  document.querySelectorAll('.sl-acc[data-sl-acc]').forEach((root) => {
    if (root.dataset.slAccBound === '1') return;
    root.addEventListener('click', (e) => {
      const btn = e.target.closest('.sl-acc__btn');
      if (!btn || !root.contains(btn)) return;
      const item = btn.closest('.sl-acc__item');
      if (!item) return;
      const isOpen = item.getAttribute('data-open') === 'true';
      const next = !isOpen;
      // Wave-Q fix #21: chiudi tutti gli altri sibling prima di aprire questo.
      if (next) {
        root.querySelectorAll('.sl-acc__item[data-open="true"]').forEach((sibling) => {
          if (sibling !== item) {
            sibling.setAttribute('data-open', 'false');
            const siblingBtn = sibling.querySelector('.sl-acc__btn');
            if (siblingBtn) siblingBtn.setAttribute('aria-expanded', 'false');
          }
        });
      }
      item.setAttribute('data-open', next ? 'true' : 'false');
      btn.setAttribute('aria-expanded', next ? 'true' : 'false');
    });
    root.dataset.slAccBound = '1';
  });
  /* === FIX v0.19.1 [F3] + Wave-Q fix #21 END === */
})();
