(function () {
  var trail = document.getElementById('cursor-trail');
  if (!trail) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (reduceMotion.matches) {
    trail.style.display = 'none';
    return;
  }

  var finePointer = window.matchMedia('(pointer: fine)');
  if (!finePointer.matches) {
    trail.style.display = 'none';
    return;
  }

  var trailCount = 6;
  var dots = [];
  var i;

  for (i = 0; i < trailCount; i += 1) {
    var dot = document.createElement('span');
    dot.className = 'cursor-trail__dot';
    trail.appendChild(dot);
    dots.push({
      el: dot,
      x: window.innerWidth / 2,
      y: window.innerHeight / 2
    });
  }

  var target = {
    x: window.innerWidth / 2,
    y: window.innerHeight / 2
  };

  var isActive = false;

  function lerp(start, end, amt) {
    return (1 - amt) * start + amt * end;
  }

  document.addEventListener('mousemove', function (event) {
    target.x = event.clientX;
    target.y = event.clientY;

    if (!isActive) {
      isActive = true;
      trail.classList.add('is-active');
    }
  });

  function animate() {
    if (isActive) {
      dots[0].x = lerp(dots[0].x, target.x, 0.2);
      dots[0].y = lerp(dots[0].y, target.y, 0.2);
      dots[0].el.style.transform = 'translate3d(' + dots[0].x + 'px, ' + dots[0].y + 'px, 0) translate(-50%, -50%)';

      for (i = 1; i < dots.length; i += 1) {
        dots[i].x = lerp(dots[i].x, dots[i - 1].x, 0.25);
        dots[i].y = lerp(dots[i].y, dots[i - 1].y, 0.25);
        dots[i].el.style.transform = 'translate3d(' + dots[i].x + 'px, ' + dots[i].y + 'px, 0) translate(-50%, -50%)';
      }
    }

    window.requestAnimationFrame(animate);
  }

  window.requestAnimationFrame(animate);
})();
