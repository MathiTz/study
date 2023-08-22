(function () {
  const menuToggle = document.querySelector('.menu-toggle');
  menuToggle.onclick = function (e) {
    const body = document.querySelector('body');

    body.classList.toggle('hide-sidebar');
  }
})();

function activateClock() {
  function addOneSecond(hours, minutes, seconds) {
    const d = new Date();
    d.setHours(parseInt(hours));
    d.setMinutes(parseInt(minutes));
    d.setSeconds(parseInt(seconds) + 1);

    const h = `${d.getHours()}`.padStart(2, '0');
    const m = `${d.getMinutes()}`.padStart(2, '0');
    const s = `${d.getSeconds()}`.padStart(2, '0');

    return `${h}:${m}:${s}`;
  }

  const activeClock = document.querySelector('[active-clock]');

  if (!activeClock) return;

  setInterval(function () {
    const time = activeClock.innerHTML.split(':');

    activeClock.innerHTML = addOneSecond(...time);
  }, 1000);
}

activateClock();
