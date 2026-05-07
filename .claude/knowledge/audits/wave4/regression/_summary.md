# Wave 4 — NO regression smoke (post-Phase 6)

Run: 2026-05-07 ~10:30
Branch: feat/wave4-production-readiness @ a4219d1

## Wave 5 IA refactor (DEC-024)
| Smoke                    | Total | Fails | Status |
|--------------------------|-------|-------|--------|
| audit-aligned URLs       | 33    | 0     | PASS   |
| legacy redirects (301)   | 18    | 0     | PASS   |
| blog 33-chain (301→200)  | 33    | 0     | PASS   |

## Wave 6 GEO/CRO blocks (DEC-025)
| Render check                                     | Result |
|--------------------------------------------------|--------|
| 21 audit-aligned URLs (200)                       | 21/21 PASS |
| home: trust-bar fallback visible (`sl-trust-bar`) | 13 occurrences PASS (added in 6f1adff) |
| home: mobile-bar conditional (`sl-mobile-bar`)    | 10 occurrences PASS |
| home: testimonials-block                          | 0 (OK — not enabled on home) |
| home: cro.css enqueued                            | 1 PASS |
| Tier-1 competenza: mobile-bar                     | 10 PASS |
| Tier-1 competenza: mini-form                      | 5 PASS |
| Tier-2 competenza: FAQPage schema emitted         | 1 PASS |

## Conclusion

**NO regression** introduced by Wave 4 phases 1-6.
All Wave 5 (IA refactor + redirects) + Wave 6 (GEO/CRO blocks + FAQPage) acceptance criteria still hold.
