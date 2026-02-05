<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Background Gradient Layout</title>
    <link rel="stylesheet" href="style.css">

    <script>
document.addEventListener('DOMContentLoaded', function () {
  var header = document.querySelector('.site-header');
  var btn = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');

  if (header && btn && nav) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var open = header.classList.toggle('nav-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    nav.addEventListener('click', function (e) {
      if (e.target && e.target.tagName === 'A') {
        header.classList.remove('nav-open');
        btn.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('click', function () {
      header.classList.remove('nav-open');
      btn.setAttribute('aria-expanded', 'false');
    });
  }

  var body = document.body;
  var overlay = document.getElementById('intro-overlay');
  var canvas = document.getElementById('intro-particles');
  var content = document.querySelector('.content');
  var introDuration = 850;

  if (!body || !overlay || !canvas || !content) {
    return;
  }

  var ctx = canvas.getContext('2d');
  if (!ctx) {
    body.classList.add('intro-done');
    if (overlay.parentNode) {
      overlay.parentNode.removeChild(overlay);
    }
    return;
  }

  body.classList.add('intro-active');

  var particles = [];
  var particleCount = 60;
  var rafId = null;
  var width = 0;
  var height = 0;

  function resizeCanvas() {
    width = canvas.clientWidth;
    height = canvas.clientHeight;
    canvas.width = width;
    canvas.height = height;
  }

  function createParticle() {
    return {
      x: Math.random() * width,
      y: Math.random() * height,
      size: 0.6 + Math.random() * 2,
      speedX: -0.2 + Math.random() * 0.4,
      speedY: -0.25 + Math.random() * 0.5,
      alpha: 0.2 + Math.random() * 0.7
    };
  }

  function initParticles() {
    particles = [];
    for (var i = 0; i < particleCount; i += 1) {
      particles.push(createParticle());
    }
  }

  function animateParticles() {
    ctx.clearRect(0, 0, width, height);

    for (var i = 0; i < particles.length; i += 1) {
      var p = particles[i];
      p.x += p.speedX;
      p.y += p.speedY;

      if (p.x < -6 || p.x > width + 6 || p.y < -6 || p.y > height + 6) {
        particles[i] = createParticle();
        particles[i].x = Math.random() < 0.5 ? -4 : width + 4;
        particles[i].y = Math.random() * height;
        p = particles[i];
      }

      ctx.beginPath();
      ctx.fillStyle = 'rgba(255, 230, 173, ' + p.alpha + ')';
      ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
      ctx.fill();
    }

    rafId = requestAnimationFrame(animateParticles);
  }

  function finishIntro() {
    body.classList.add('intro-done');
    body.classList.remove('intro-active');

    if (rafId) {
      cancelAnimationFrame(rafId);
      rafId = null;
    }

    setTimeout(function () {
      if (overlay && overlay.parentNode) {
        overlay.parentNode.removeChild(overlay);
      }
    }, 200);
  }

  resizeCanvas();
  initParticles();
  animateParticles();

  window.addEventListener('resize', function () {
    resizeCanvas();
    initParticles();
  });

  setTimeout(function () {
    overlay.classList.add('is-exiting');
    finishIntro();
  }, introDuration);

  setTimeout(function () {
    if (body.classList.contains('intro-active')) {
      finishIntro();
    }
  }, introDuration + 700);
});
</script>




<style>
/* Speed up intro overlay exit and content reveal */
#intro-overlay,
#intro-overlay *{
  transition-duration: .22s !important;
  animation-duration: .22s !important;
}
#intro-overlay.is-exiting{
  transition-duration: .22s !important;
}
body.intro-active .content{
  transition-duration: .22s !important;
  transition-delay: 0s !important;
}
body.intro-done .content{
  transition-duration: .22s !important;
  transition-delay: 0s !important;
}

/* ===== Artist Hover (NO click/focus) ===== */

#hover-particles{
  position:fixed; inset:0;
  width:100vw; height:100vh;
  pointer-events:none;
  z-index:9500;
}

/* Hover lift (does not persist) */
.artist-card{
  position:relative;
  transform:translateZ(0);
  transition:transform .22s ease, box-shadow .22s ease, filter .22s ease;
  will-change:transform;
}
.artist-card:hover{
  transform:translateY(-8px) scale(1.02);
  z-index:50;
  box-shadow:0 18px 60px rgba(0,0,0,.35);
}


/* Speed up star animation (override whatever is in style.css) */
#intro-star{ animation-duration: .75s !important; }
.artist-card__star{ animation-duration: .95s !important; }


/* Speed up content reveal inside artist cards (override style.css) */
.artist-card__media,
.artist-card__name,
.artist-card__meta,
.artist-card__divider,
.artist-card__desc,
.artist-card__actions,
.artist-card__body{
  transition-duration: .16s !important;
  transition-delay: 0s !important;
  animation-duration: .22s !important;
  animation-delay: 0s !important;
}

</style>

</head>
<body>
    <div id="intro-overlay" aria-hidden="true">
        <canvas id="intro-particles"></canvas>
        <div id="intro-mask"></div>
        <div id="intro-star">✦</div>
    </div>
    <header class="site-header" id="head">
        <div class="header-inner">

            <!-- LOGO -->
            <div class="logo">
                Harmonia Caelestis
            </div>

            <button class="nav-toggle" type="button" aria-label="Menu" aria-expanded="false">
             <span></span> 
             <span></span>
             <span></span>
            </button>

            <!-- NAVIGACE -->
            <nav class="main-nav">
                <a href="index.php">Úvod</a>
                <a href="Aktuality.php">Aktuality</a>
                <a href="Program.php">Program</a>
                <a href="Umelci.php">Umělci</a>
                <a href="Ofestivalu.php">O festivalu</a>
                <a class="icon icon--fb" href="https://www.facebook.com/hcimfcz/" aria-label="Facebook"><span></span></a>
                <a class="icon icon--mail" href="mailto:reditel@ipd-ccsh.cz" aria-label="E-mail"><span></span></a>   
            </nav>

        </div>
    </header>



    <!-- NAVAZUJ�C� OBSAH -->
    <section class="content">
         <div class="container2">
            <h2>UMĚLCI</h2>
            <br>
            <h3>Probíhající III. ročník</h3>
            <div class="container2-divider"></div>
        </div>

<div class="artists__container">
    <div class="artists__grid">

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="1.png" alt="Roman Perucki">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#roman">Roman Perucki</a></h3>
          <div class="artist-card__meta">Varhany · Polsko</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Koncertní varhaník, festivalové programy, slavnostní večery a interpretace klasických děl.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#roman">Profil</a>
          </div>
        </div>
      </article>

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="2.png" alt="Jesús Sampredo Márquez">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#jesus">Jesús Sampredo Márquez</a></h3>
          <div class="artist-card__meta">Varhany · Španělsko</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Mezinárodní host, virtuózní interpretace a jedinečná atmosféra koncertů v historických prostorách.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#jesus">Profil</a>
          </div>
        </div>
      </article>

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="3.png" alt="Michaela Káčerková">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#michaela">Michaela Káčerková</a></h3>
          <div class="artist-card__meta">Varhany, cembalo · ČR</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Špičková interpretka, komorní projekty a reprezentativní programy pro festivalové publikum.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#michaela">Profil</a>
          </div>
        </div>
      </article>

    </div>

</div>
<div class="artists__container">
    <div class="artists__grid">

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="4.png" alt="Tomáš Strašil">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#roman">Tomáš Strašil</a></h3>
          <div class="artist-card__meta">Varhany · Polsko</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Koncertní varhaník, festivalové programy, slavnostní večery a interpretace klasických děl.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#roman">Profil</a>
          </div>
        </div>
      </article>

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="5.png" alt="Karolína Cingrošová">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#jesus">Karolína Cingrošová</a></h3>
          <div class="artist-card__meta">Varhany · Španělsko</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Mezinárodní host, virtuózní interpretace a jedinečná atmosféra koncertů v historických prostorách.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#jesus">Profil</a>
          </div>
        </div>
      </article>

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="6.png" alt="Anna Paulová">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#michaela">Anna Paulová</a></h3>
          <div class="artist-card__meta">Varhany, cembalo · ČR</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Špičková interpretka, komorní projekty a reprezentativní programy pro festivalové publikum.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#michaela">Profil</a>
          </div>
        </div>
      </article>

    </div>

</div>
<div class="artists__container">
    <div class="artists__grid">

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="7.png" alt="Lukáš Sommer">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#roman">Lukáš Sommer</a></h3>
          <div class="artist-card__meta">Varhany · Polsko</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Koncertní varhaník, festivalové programy, slavnostní večery a interpretace klasických děl.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#roman">Profil</a>
          </div>
        </div>
      </article>

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="8.png" alt="Kateřina Málková">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#jesus">Kateřina Málková</a></h3>
          <div class="artist-card__meta">Varhany · Španělsko</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Mezinárodní host, virtuózní interpretace a jedinečná atmosféra koncertů v historických prostorách.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#jesus">Profil</a>
          </div>
        </div>
      </article>

      <article class="artist-card">
        <div class="artist-card__media">
          <img src="9.png" alt="Josef Kovačič">
        </div>

        <div class="artist-card__body">
          <h3 class="artist-card__name"><a href="#michaela">Josef Kovačič</a></h3>
          <div class="artist-card__meta">Varhany, cembalo · ČR</div>

          <div class="artist-card__divider">
            <span class="artist-card__star">✦</span>
          </div>

          <p class="artist-card__desc">
            Špičková interpretka, komorní projekty a reprezentativní programy pro festivalové publikum.
          </p>

          <div class="artist-card__actions">
            <a class="box box--primary" href="#program">Program <span class="box__arrow">›</span></a>
            <a class="box box--ghost" href="#michaela">Profil</a>
          </div>
        </div>
      </article>

    </div>

</div>
<br>
    <br>
    <br>




            
        </div>
    
        <footer class="site-footer" id="sponsors">

            <div class="footer-inner">

                <div class="footer-title">Partneři</div>

                <div class="sponsors-row">
                    <a href="https://www.ccshplzen.cz" class="sponsor">
                        <img src="ccsh.png" alt="ccsh">
                    </a>

                    <a href="https://duxnet.cz" class="sponsor">
                        <img src="d.png" alt="Duxnet.cz">
                    </a>
                    <a href="https://mk.gov.cz" class="sponsor">
                        <img src="mk.jpg" alt="MK">
                    </a>
                    <a href="https://www.plzen2025.eu" class="sponsor">
                        <img src="2025.png" alt="2025">
                    </a>


                </div>

            </div>

        </footer>

    </section>

    
  <canvas id="hover-particles" aria-hidden="true"></canvas>

  <script>
(function(){
  var canvas = document.getElementById('hover-particles');
  var cards = document.querySelectorAll('.artist-card');
  if (!canvas || !cards || !cards.length) return;

  var ctx = canvas.getContext('2d');
  if (!ctx) return;

  var dpr = Math.max(1, Math.min(2, window.devicePixelRatio || 1));
  var vw = 0, vh = 0;
  function resize(){
    vw = window.innerWidth || document.documentElement.clientWidth || 0;
    vh = window.innerHeight || document.documentElement.clientHeight || 0;
    canvas.style.width = vw + 'px';
    canvas.style.height = vh + 'px';
    canvas.width = Math.floor(vw * dpr);
    canvas.height = Math.floor(vh * dpr);
    ctx.setTransform(dpr,0,0,dpr,0,0);
  }
  resize();
  window.addEventListener('resize', resize);

  var activeHover = null;
  var hoverRect = null;
  var particles = [];
  var raf = null;
  var lastT = 0;
  var emitAcc = 0;

  function getRect(el){
    try { return el.getBoundingClientRect(); } catch(e){ return null; }
  }

  function spawnEdgeParticle(r){
    // spawn only on border (thin band)
    var side = Math.floor(Math.random()*4); // 0 top,1 right,2 bottom,3 left
    var inset = 2 + Math.random()*4; // band thickness
    var x, y;
    if (side === 0){
      x = r.left + Math.random()*r.width;
      y = r.top + inset;
    } else if (side === 1){
      x = r.right - inset;
      y = r.top + Math.random()*r.height;
    } else if (side === 2){
      x = r.left + Math.random()*r.width;
      y = r.bottom - inset;
    } else {
      x = r.left + inset;
      y = r.top + Math.random()*r.height;
    }
    // gentle outward drift
    var vx = (Math.random()-.5)*0.35;
    var vy = (Math.random()-.5)*0.35;
    if (side===0) vy = -0.35 - Math.random()*0.25;
    if (side===2) vy =  0.35 + Math.random()*0.25;
    if (side===1) vx =  0.35 + Math.random()*0.25;
    if (side===3) vx = -0.35 - Math.random()*0.25;

    particles.push({
      x:x, y:y,
      vx:vx, vy:vy,
      life: 600 + Math.random()*500,
      age:0,
      size: 1 + Math.random()*2.2,
      a: 0.25 + Math.random()*0.55
    });
  }

  function animate(t){
    if (!lastT) lastT = t;
    var dt = Math.min(40, t - lastT);
    lastT = t;

    ctx.clearRect(0,0,vw,vh);

    // keep hoverRect synced with scroll/layout
    if (activeHover){
      hoverRect = getRect(activeHover);
      if (hoverRect && hoverRect.width > 0 && hoverRect.height > 0){
        // emit rate ~ 80/s (scaled by dt)
        emitAcc += dt;
        while (emitAcc >= 12){
          emitAcc -= 12;
          spawnEdgeParticle(hoverRect);
        }
      }
    }

    for (var i = particles.length - 1; i >= 0; i--){
      var p = particles[i];
      p.age += dt;
      if (p.age >= p.life){
        particles.splice(i,1);
        continue;
      }
      var k = 1 - (p.age / p.life);
      p.x += p.vx * (dt * 0.06);
      p.y += p.vy * (dt * 0.06);

      ctx.beginPath();
      ctx.fillStyle = 'rgba(255,230,173,' + (p.a * k) + ')';
      ctx.arc(p.x, p.y, p.size, 0, Math.PI*2);
      ctx.fill();
    }

    // stop loop when nothing left
    if (activeHover || particles.length){
      raf = requestAnimationFrame(animate);
    } else {
      raf = null;
      lastT = 0;
      ctx.clearRect(0,0,vw,vh);
    }
  }

  function ensureAnim(){
    if (!raf) raf = requestAnimationFrame(animate);
  }

  function onEnter(card){
    activeHover = card;
    hoverRect = getRect(card);
    ensureAnim();
  }
  function onLeave(card){
    if (activeHover === card) activeHover = null;
    // let particles fade out naturally; loop stops when empty
    ensureAnim();
  }

  // Events
  for (var i=0;i<cards.length;i++){
    (function(card){
      card.addEventListener('mouseenter', function(){ onEnter(card); }, {passive:true});
      card.addEventListener('mouseleave', function(){ onLeave(card); }, {passive:true});
    })(cards[i]);
  }

  // Safety: if user scrolls while not in focus, keep hover aligned
  window.addEventListener('scroll', function(){
    if (!activeHover) return;
    ensureAnim();
  }, {passive:true});

  // When leaving the window, drop hover immediately (prevents "stuck" state)
  document.addEventListener('visibilitychange', function(){
    if (document.hidden){ activeHover = null; ensureAnim(); }
  });
})();
  </script>

</body>
</html>
