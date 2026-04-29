/*
 * Saltelli & Partners — main entrypoint (Frame 1 animations)
 * Style & Animation agent · SHIP MODE 24H · 2026-04-29
 *
 * Stack: GSAP 3.12.5 + ScrollTrigger + SplitText + Lenis 1.1.13.
 * Caricato da inc/enqueue.php in footer con strategy=defer.
 *
 * Behaviors:
 *   1. Lenis smooth scroll (lerp 0.1) — disabilitato su prefers-reduced-motion
 *   2. GSAP ticker integrato con Lenis (no double rAF)
 *   3. Hero headline reveal — SplitText su parole, stagger 80ms (desktop only)
 *   4. Sezioni reveal — fade + translateY 24px su scroll, trigger 80% viewport
 *   5. List items area pratica — stagger 80ms al primo ingresso in viewport
 *   6. Header solid-on-scroll — toggle .is-scrolled dopo 80px scroll
 *   7. Mobile (< 768px) — niente SplitText, niente translate aggressivi: solo fade
 *
 * TODO Style & Animation Agent: aggiungere refinement post-demo cliente
 *   (parallax foto facciata, magnetic buttons, hover ritratto avvocato extra).
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

    // Guards: GSAP/Lenis sono CDN async. Bail-out grazioso se mancano.
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

    // 2. Header solid-on-scroll — supporta sia class .is-scrolled sia attr [data-scrolled]
    const header = document.querySelector('.sl-header');
    if (header) {
      const onScroll = () => {
        const y = window.scrollY || window.pageYOffset || 0;
        const scrolled = y > 80;
        header.classList.toggle('is-scrolled', scrolled);
        header.setAttribute('data-scrolled', scrolled ? 'true' : 'false');
      };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }

    // 3. Mobile menu toggle — supporta entrambi i naming
    const menuBtn = document.querySelector('.sl-header__menu-btn, .sl-header__burger');
    const mobileMenu = document.querySelector('.sl-mobile-menu, .sl-header__mobile');
    if (menuBtn && mobileMenu) {
      menuBtn.addEventListener('click', () => {
        const open = mobileMenu.hasAttribute('hidden') || !mobileMenu.classList.contains('is-open');
        mobileMenu.classList.toggle('is-open', open);
        if (open) mobileMenu.removeAttribute('hidden');
        else mobileMenu.setAttribute('hidden', '');
        menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }

    // Reduced motion: lascia il CSS gestire la headline (fallback class)
    if (reduced) {
      document.querySelectorAll('.sl-hero__headline .sl-word, .sl-hero__headline .sl-hero__word').forEach(w => w.classList.add('is-revealed'));
      document.querySelectorAll('.sl-revealable').forEach(el => el.classList.add('is-revealed'));
      return;
    }

    // 4. Hero headline reveal — supporta sia .sl-word sia .sl-hero__word + [data-split-reveal]
    const heroHeadline = document.querySelector('.sl-hero__headline, [data-split-reveal]');
    if (heroHeadline) {
      const wordSel = '.sl-word, .sl-hero__word';
      const wordEls = heroHeadline.querySelectorAll(wordSel);
      if (!isMobile && hasGsap) {
        if (wordEls.length) {
          // Parole già pre-segmentate dal template — anima quelle
          window.gsap.from(wordEls, {
            yPercent: 60,
            opacity: 0,
            duration: 0.7,
            ease: 'power2.out',
            stagger: 0.08,
            delay: 0.1,
            onComplete: () => wordEls.forEach(w => w.classList.add('is-revealed')),
          });
        } else if (hasSplitText) {
          const split = new window.SplitText(heroHeadline, { type: 'words' });
          window.gsap.from(split.words, {
            yPercent: 60,
            opacity: 0,
            duration: 0.7,
            ease: 'power2.out',
            stagger: 0.08,
            delay: 0.1,
          });
        }
      } else {
        // Mobile o no-GSAP: usa il fallback CSS via class
        wordEls.forEach(w => w.classList.add('is-revealed'));
      }
    }

    // 5. Reveal generico — sezioni e revealable
    if (hasGsap && hasScrollTrigger) {
      const targets = document.querySelectorAll('.sl-revealable, [data-reveal]');
      targets.forEach((el) => {
        window.gsap.from(el, {
          y: isMobile ? 12 : 24,
          opacity: 0,
          duration: isMobile ? 0.5 : 0.7,
          ease: 'power2.out',
          scrollTrigger: { trigger: el, start: 'top 85%', once: true },
          onComplete: () => el.classList.add('is-revealed'),
        });
      });

      // 6. List items area — stagger di gruppo
      const areaGroups = document.querySelectorAll('.sl-areas__list, [data-areas-list]');
      areaGroups.forEach((group) => {
        const items = group.querySelectorAll('.sl-area');
        if (!items.length) return;
        window.gsap.from(items, {
          y: isMobile ? 8 : 16,
          opacity: 0,
          duration: isMobile ? 0.4 : 0.6,
          ease: 'power2.out',
          stagger: 0.08,
          scrollTrigger: { trigger: group, start: 'top 80%', once: true },
        });
      });
    } else {
      // No GSAP: rivela tutto subito
      document.querySelectorAll('.sl-revealable, [data-reveal]').forEach(el => el.classList.add('is-revealed'));
    }

    // 7. Resize debounced — ScrollTrigger refresh
    if (hasScrollTrigger) {
      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => window.ScrollTrigger.refresh(), 200);
      });
    }
  }
})();
