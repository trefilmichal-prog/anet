(function () {
  var debug = true;
  var trail = document.getElementById('cursor-trail');
  if (!trail) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var finePointer = window.matchMedia('(pointer: fine)');
  var anyFinePointer = window.matchMedia('(any-pointer: fine)');
  var anyHover = window.matchMedia('(any-hover: hover)');
  var allowPointer = finePointer.matches
    || anyFinePointer.matches
    || anyHover.matches;
  if (debug) {
    console.log('cursor-trail init', allowPointer);
  }
  if (debug) {
    console.log('cursor-trail reduced motion', reduceMotion.matches);
  }
  var reducedMotionEnabled = false;
  if (reduceMotion.matches) {
    reducedMotionEnabled = true;
  }

  if (!allowPointer) {
    trail.style.display = 'none';
  }

  var trailCount = reducedMotionEnabled ? 2 : 5;
  var dots = [];
  var i;
  var rippleInterval = 70;
  var lastRippleTime = 0;
  var allowRipple = !reducedMotionEnabled;

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
  var inactivityTimeoutId = null;
  var hasLoggedEvent = false;
  var lead = {
    x: window.innerWidth / 2,
    y: window.innerHeight / 2
  };

  function lerp(start, end, amt) {
    return (1 - amt) * start + amt * end;
  }

  function updateTarget(event) {
    if (!allowPointer) {
      var hasPointerType = event && typeof event.pointerType === 'string';
      var maxTouchPoints = (navigator && typeof navigator.maxTouchPoints === 'number')
        ? navigator.maxTouchPoints
        : 0;
      var isMouse = hasPointerType
        ? event.pointerType === 'mouse'
        : maxTouchPoints === 0;

      if (isMouse) {
        allowPointer = true;
        trail.style.display = '';
      } else {
        return;
      }
    }

    if (debug && !hasLoggedEvent && event) {
      console.log('cursor-trail event', event.type, event.clientX, event.clientY);
      hasLoggedEvent = true;
    }

    target.x = event.clientX;
    target.y = event.clientY;

    if (inactivityTimeoutId) {
      window.clearTimeout(inactivityTimeoutId);
    }

    inactivityTimeoutId = window.setTimeout(function () {
      trail.classList.remove('is-active');
      isActive = false;
    }, 450);

    if (!isActive) {
      isActive = true;
      trail.classList.add('is-active');
    }

    var now = (window.performance && window.performance.now) ? window.performance.now() : Date.now();
    if (allowRipple && now - lastRippleTime >= rippleInterval) {
      lastRippleTime = now;
      createRipple(target.x, target.y);
    }
  }

  document.addEventListener('mousemove', updateTarget);
  document.addEventListener('pointermove', updateTarget);
  document.addEventListener('pointerenter', updateTarget);

  function createRipple(x, y) {
    var ripple = document.createElement('span');
    ripple.className = 'cursor-trail__ripple';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    trail.appendChild(ripple);

    function removeRipple() {
      if (ripple && ripple.parentNode) {
        ripple.parentNode.removeChild(ripple);
      }
    }

    ripple.addEventListener('animationend', removeRipple);
    window.setTimeout(removeRipple, 1100);
  }

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
