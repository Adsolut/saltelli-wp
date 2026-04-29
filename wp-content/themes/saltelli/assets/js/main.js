/*
 * Saltelli & Partners — main entrypoint
 * Style & Animation · v0.3.0-beta-polish · 2026-04-29
 *
 * Stack: GSAP 3.12.5 + ScrollTrigger + SplitText + Lenis 1.1.13.
 * Caricato da inc/enqueue.php in footer con strategy=defer.
 *
 * Behaviors:
 *   1. Lenis smooth scroll (lerp 0.1) — disabilitato su prefers-reduced-motion + mobile
 *   2. GSAP ticker integrato con Lenis (no double rAF)
 *   3. Header solid-on-scroll — toggle .is-scrolled dopo 80px scroll
 *   4. Mobile menu toggle (burger / hidden attr)
 *   5. Hero headline reveal — stagger 80ms su .sl-hero__word (desktop only, no reduced-motion)
 *   6. Sezioni fade-in scroll-trigger 80% viewport (.sl-areas/.sl-studio/.sl-team/.sl-cases/.sl-press/.sl-contact)
 *   7. List items area pratica — stagger 80ms al primo ingresso in viewport (desktop only)
 *   8. Hover preview .sl-area — popola .sl-area__preview con cat / lead / CTA (desktop only)
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
        const scrolled = y > 80;
        header.classList.toggle('is-scrolled', scrolled);
        header.setAttribute('data-scrolled', scrolled ? 'true' : 'false');
      };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
      header.dataset.slScrollBound = '1';
    }

    // 3. Mobile menu toggle
    const menuBtn = document.querySelector('.sl-header__menu-btn, .sl-header__burger');
    const mobileMenu = document.querySelector('.sl-mobile-menu, .sl-header__mobile');
    if (menuBtn && mobileMenu && !menuBtn.dataset.slMenuBound) {
      menuBtn.addEventListener('click', () => {
        const open = mobileMenu.hasAttribute('hidden') || !mobileMenu.classList.contains('is-open');
        mobileMenu.classList.toggle('is-open', open);
        if (open) mobileMenu.removeAttribute('hidden');
        else mobileMenu.setAttribute('hidden', '');
        menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      menuBtn.dataset.slMenuBound = '1';
    }

    // Gate per CSS: appena JS è in piedi, togliamo il fallback opacity:1 !important
    // che il foglio applica solo a html:not(.js-reveal-ready) (vedi sections.css FIX D).
    if (hasGsap && !reduced) {
      document.documentElement.classList.add('js-reveal-ready');
    }

    // Reduced motion: lascia il CSS gestire la headline (fallback class) e bail-out
    if (reduced) {
      document.querySelectorAll('.sl-hero__headline .sl-word, .sl-hero__headline .sl-hero__word').forEach((w) => w.classList.add('is-revealed'));
      document.querySelectorAll('.sl-revealable').forEach((el) => el.classList.add('is-revealed'));
      bindAreaHover();
      return;
    }

    // 4. Hero headline reveal — stagger 80ms su .sl-hero__word
    const heroHeadline = document.querySelector('.sl-hero__headline, [data-split-reveal]');
    if (heroHeadline) {
      const wordEls = heroHeadline.querySelectorAll('.sl-word, .sl-hero__word');
      if (!isMobile && hasGsap && wordEls.length) {
        window.gsap.set(wordEls, { opacity: 0, y: 40 });
        window.gsap.to(wordEls, {
          opacity: 1,
          y: 0,
          duration: 0.7,
          ease: 'power2.out',
          stagger: 0.08,
          delay: 0.08,
          onComplete: () => wordEls.forEach((w) => w.classList.add('is-revealed')),
        });
      } else if (!isMobile && hasGsap && hasSplitText && !wordEls.length) {
        const split = new window.SplitText(heroHeadline, { type: 'words' });
        window.gsap.set(split.words, { opacity: 0, y: 40 });
        window.gsap.to(split.words, {
          opacity: 1,
          y: 0,
          duration: 0.7,
          ease: 'power2.out',
          stagger: 0.08,
          delay: 0.08,
        });
      } else {
        wordEls.forEach((w) => w.classList.add('is-revealed'));
      }
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

          // 6. List items area — stagger desktop only (mobile = perf skip)
          if (!isMobile) {
            const areaGroups = document.querySelectorAll('.sl-areas__list, [data-areas-list]');
            areaGroups.forEach((group) => {
              const items = group.querySelectorAll('.sl-area');
              if (!items.length) return;
              window.gsap.from(items, {
                y: 16,
                opacity: 0,
                duration: 0.5,
                ease: 'power2.out',
                stagger: 0.08,
                scrollTrigger: { trigger: group, start: 'top 70%', toggleActions: 'play none none none' },
              });
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

    // 8. Resize debounced — ScrollTrigger refresh
    if (hasScrollTrigger) {
      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => window.ScrollTrigger.refresh(), 200);
      });
    }
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
      const href = area.getAttribute('href') || '#';
      const num = area.dataset.areaNum || '';

      // Costruzione DOM via template stringa, ma con escape minimo: i data-attr arrivano già da PHP esc_attr.
      // Per sicurezza li ri-escape lato JS (nel caso siano stati toccati altrove).
      const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

      const numLabel = num ? `${esc(num)} · ` : '';
      return [
        `<div class="sl-mono sl-area__preview-cat">${numLabel}${esc(cat)}</div>`,
        `<p class="sl-area__preview-lead">${esc(lead)}</p>`,
        `<a class="sl-btn" href="${esc(href)}">`,
        `  <span>Approfondisci</span>`,
        `  <span class="arrow" aria-hidden="true">→</span>`,
        `</a>`,
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
})();
