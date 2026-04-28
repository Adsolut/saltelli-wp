/*
 * Saltelli — Lenis init (placeholder).
 *
 * TODO Style & Animation agent: implementare smooth momentum scroll.
 * Pattern standard 2026 (combo Lenis + GSAP ticker):
 *
 *   import Lenis from 'https://cdn.jsdelivr.net/npm/lenis@latest/dist/lenis.min.mjs';
 *
 *   const lenis = new Lenis({ lerp: 0.1, smoothWheel: true });
 *   lenis.on('scroll', ScrollTrigger.update);
 *   gsap.ticker.add((time) => lenis.raf(time * 1000));
 *   gsap.ticker.lagSmoothing(0);
 *
 * Disabilita Lenis su prefers-reduced-motion.
 */
