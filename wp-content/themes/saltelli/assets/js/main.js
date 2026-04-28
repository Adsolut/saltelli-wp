/*
 * Saltelli — main entrypoint.
 *
 * Stato: scaffold. Per ora questo file non importa nulla.
 * Lo Style & Animation agent collegherà GSAP + Lenis + interaction layer
 * quando il design sarà firmato.
 *
 * TODO Style & Animation agent:
 *   - import './lenis-init.js'
 *   - import './gsap-init.js'
 *   - registrare ScrollTrigger.refresh() su resize (debounced)
 *   - registrare i moduli di micro-interaction (header, accordion FAQ, ecc.)
 */

(function () {
  'use strict';

  // Ready guard.
  const ready = () => {
    document.documentElement.classList.add('is-ready');
    // TODO: bootstrap interaction modules.
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ready, { once: true });
  } else {
    ready();
  }
})();
