/**
 * Wave P9 Design Handoff — archive-casi filter toggle (vanilla JS, no AJAX).
 * Hardcode 5 tab (Tutti/Privati/Imprese/Contenzioso/Altri).
 * Toggle .is-active sul bottone cliccato + .is-hidden sui row in base a
 * data-filter / data-category match. Nessuna query server-side, nessun AJAX.
 *
 * @package Saltelli
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var filterBar = document.querySelector('.sl-casi__filter-bar');
    if (!filterBar) return;

    var buttons = filterBar.querySelectorAll('.sl-casi__filter-btn');
    var rows = document.querySelectorAll('.sl-blog__list [data-category]');

    if (!buttons.length || !rows.length) return;

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var filter = btn.getAttribute('data-filter');
        if (!filter) return;

        buttons.forEach(function (b) { b.classList.remove('is-active'); });
        btn.classList.add('is-active');

        rows.forEach(function (row) {
          var cat = row.getAttribute('data-category');
          if (filter === 'all' || cat === filter) {
            row.classList.remove('is-hidden');
          } else {
            row.classList.add('is-hidden');
          }
        });
      });
    });
  });
})();
