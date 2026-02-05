<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Background Gradient Layout</title>
    <link rel="stylesheet" href="Style.css">
    <style>
/* ===== Faster intro (star + content) ===== */
#intro-star{
  animation-duration:.55s !important;
  transition-duration:.35s !important;
}
#intro-star.star-animated{
  animation-duration:.95s !important;
}
#intro-overlay, #intro-mask{
  transition-duration:.35s !important;
}
body.intro-active .content{transition-duration:.35s !important;}
body.intro-done .content{transition-duration:.35s !important;}
    </style>
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
  var introDuration = 650;

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
    }, 250);
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
  }, introDuration + 500);
});
</script>
</head>
<body>
    <div id="intro-overlay" aria-hidden="true">
        <canvas id="intro-particles"></canvas>
        <div id="intro-mask"></div>
        <div id="intro-star" class="star-animated">✦</div>
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
            <h2>O FESTIVALU</h2>
<div class="container2-divider2"><span class="star star-animated">✦</span></div>
<a>Harmonia Caelestis je festival klasické hudby, který se koná v malebných kulisách Plzeňského kraje, jižních Čech a Karlovarského kraje. Jeho cílem je zpřístupnit široké veřejnosti krásu a bohatství klasické hudby a propojit lidi z různých koutů světa prostřednictvím sdíleného zážitku z živého hudebního umění.</a>
<br>
<a>Generálním partnerem tohoto ročníku festivalu je Plzeňská diecéze CČSH, která tak vyjadřuje svůj zájem o podporu kulturního života v regionu a šíření duchovních hodnot. Festival se koná pod záštitou plzeňského husitského biskupa Lukáše Bujny. Pořadatelem festivalu je Institut plzeňské diecéze.</a>        
<br>
<a>Koncerty jsou pořádány za finanční podpory Ministerstva kultury ČR, Magistrátu města Plzně a města Milevska.</a>
<br>
<a>Program festivalu je pestrý a zahrnuje koncerty renomovaných českých i zahraničních souborů a sólistů. Nabízí širokou škálu hudebních stylů od baroka po současnost, s důrazem na duchovní hudbu.</a>
<br>
<a>Festival Harmonia Caelestis je jedinečnou příležitostí vychutnat si krásu klasické hudby v krásných historických památkách i moderních prostorách a zároveň se setkat s lidmi, kteří sdílí lásku k hudbě.</a>
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

    




</body>
</html>
