/*
 * Saltelli — GSAP init (placeholder).
 *
 * TODO Style & Animation agent: registrare plugin e impostare le animazioni base.
 * Pattern:
 *
 *   gsap.registerPlugin(ScrollTrigger, SplitText);
 *
 *   // Hero text reveal
 *   const split = new SplitText('.hero h1', { type: 'lines' });
 *   gsap.from(split.lines, {
 *     yPercent: 100,
 *     opacity: 0,
 *     duration: 1.2,
 *     ease: 'expo.out',
 *     stagger: 0.08,
 *   });
 *
 *   // Reveal on scroll (subtle)
 *   gsap.utils.toArray('[data-reveal]').forEach((el) => {
 *     gsap.from(el, {
 *       y: 24,
 *       opacity: 0,
 *       duration: 0.9,
 *       ease: 'expo.out',
 *       scrollTrigger: { trigger: el, start: 'top 80%' },
 *     });
 *   });
 *
 * Rispetta prefers-reduced-motion.
 */
