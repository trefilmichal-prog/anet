(function () {
  var trail = document.getElementById('cursor-trail');
  if (!trail) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var finePointer = window.matchMedia('(pointer: fine)');
  var anyFinePointer = window.matchMedia('(any-pointer: fine)');
  var allowPointer = finePointer.matches || anyFinePointer.matches;

  if (!allowPointer) {
    trail.style.display = 'none';
    return;
  }

  if (reduceMotion.matches) {
    trail.style.display = 'none';
    return;
  }

  var trailCount = 5;
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
  var lead = {
    x: window.innerWidth / 2,
    y: window.innerHeight / 2
  };

  function lerp(start, end, amt) {
    return (1 - amt) * start + amt * end;
  }

  function updateTarget(event) {
    target.x = event.clientX;
    target.y = event.clientY;

    if (!isActive) {
      isActive = true;
      trail.classList.add('is-active');
    }
  }

  document.addEventListener('mousemove', updateTarget);
  document.addEventListener('pointermove', updateTarget);
  document.addEventListener('pointerenter', updateTarget);

  function animate() {
    if (isActive) {
      lead.x = lerp(lead.x, target.x, 0.2);
      lead.y = lerp(lead.y, target.y, 0.2);

      for (i = 0; i < dots.length; i += 1) {
        var source = i === 0 ? lead : dots[i - 1];
        dots[i].x = lerp(dots[i].x, source.x, 0.25);
        dots[i].y = lerp(dots[i].y, source.y, 0.25);
        dots[i].el.style.transform = 'translate3d(' + dots[i].x + 'px, ' + dots[i].y + 'px, 0) translate(-50%, -50%)';
      }
    }

    window.requestAnimationFrame(animate);
  }

  window.requestAnimationFrame(animate);
})();
