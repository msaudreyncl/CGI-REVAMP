// COFFEE GRADE IDENTIFICATION — live header clock
// Keeps the date/time in the header ticking without a page reload.
// Server-rendered PHP already prints the correct value on first load;
// this just updates it client-side once per second.

(function () {
  const dateEl = document.getElementById('clock-date');
  const timeEl = document.getElementById('clock-time');

  if (!dateEl || !timeEl) return;

  const MONTHS = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

  function pad(n) {
    return n < 10 ? '0' + n : '' + n;
  }

  function render() {
    const now = new Date();

    const day = pad(now.getDate());
    const month = MONTHS[now.getMonth()];
    const year = now.getFullYear();
    dateEl.textContent = day + ' ' + month + ' ' + year;

    let hours = now.getHours();
    const minutes = pad(now.getMinutes());
    const suffix = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    if (hours === 0) hours = 12;
    timeEl.textContent = hours + ':' + minutes + ' ' + suffix;
  }

  render();
  setInterval(render, 1000 * 30);
})();

// COFFEE GRADE IDENTIFICATION — user menu (profile / logout) dropdown
(function () {
  const trigger = document.getElementById('userMenuTrigger');
  const menu = document.getElementById('userMenu');

  if (!trigger || !menu) return;

  function openMenu() {
    menu.hidden = false;
    trigger.setAttribute('aria-expanded', 'true');
  }

  function closeMenu() {
    menu.hidden = true;
    trigger.setAttribute('aria-expanded', 'false');
  }

  trigger.addEventListener('click', function (event) {
    event.stopPropagation();
    if (menu.hidden) {
      openMenu();
    } else {
      closeMenu();
    }
  });

  document.addEventListener('click', function (event) {
    if (!menu.hidden && !menu.contains(event.target) && event.target !== trigger) {
      closeMenu();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeMenu();
  });
})();