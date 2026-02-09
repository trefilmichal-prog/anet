<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/background_repository.php';
require_once __DIR__ . '/includes/site_header.php';
require_once __DIR__ . '/includes/settings_repository.php';
require_once __DIR__ . '/includes/partners_repository.php';

$artists = array();
$heroBackgroundImage = get_background_image('artists', 'back.png');
$siteFontStyle = get_site_font_style();
$artistsPlaceholderText = get_setting('artists_placeholder_text', 'Umělci budou brzy doplněni.');

try {
    $db = get_db();
    $stmt = $db->query('SELECT id, name, role, bio, image, sort_order FROM artists ORDER BY sort_order ASC, id DESC');
    $artists = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Umelci load failed: ' . $e->getMessage());
    $artists = array();
}

function normalize_artist_image_path($imagePath)
{
    $imagePath = trim((string) $imagePath);

    if ($imagePath === '') {
        return '';
    }

    $imagePath = str_replace('\\', '/', $imagePath);

    if (strpos($imagePath, '..') !== false || preg_match('/[^a-zA-Z0-9_\-\/.]/', $imagePath)) {
        return '';
    }

    if (strpos($imagePath, 'uploads/artists/') !== 0) {
        return '';
    }

    return $imagePath;
}
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Background Gradient Layout</title>
    <link rel="stylesheet" href="Style.css">
    <script src="assets/cursor-trail.js" defer></script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
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
.artist-card__star{
  animation: starPulse .95s cubic-bezier(.26,.67,.2,1) infinite !important;
}


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
<body<?php echo $siteFontStyle; ?>>
    <div id="cursor-trail" aria-hidden="true"></div>
    <div id="intro-overlay" aria-hidden="true">
        <canvas id="intro-particles"></canvas>
        <div id="intro-mask"></div>
        <div id="intro-star">✦</div>
    </div>
    <?php render_site_header(); ?>



    <!-- NAVAZUJ�C� OBSAH -->
    <section class="content" style="background-image: url('<?php echo htmlspecialchars($heroBackgroundImage, ENT_QUOTES, 'UTF-8'); ?>');">
         <div class="container2">
            <h2>UMĚLCI</h2>
            <br>
            <h3>Probíhající III. ročník</h3>
            <div class="container2-divider"></div>
        </div>

<div class="artists__container">
    <?php if (empty($artists)): ?>
        <?php if (trim($artistsPlaceholderText) !== ''): ?>
            <div class="festival-text-box">
                <p><?php echo htmlspecialchars($artistsPlaceholderText, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="artists__grid">
            <?php foreach ($artists as $artist): ?>
                <?php
                    $artistName = htmlspecialchars((string) $artist['name'], ENT_QUOTES, 'UTF-8');
                    $artistRole = htmlspecialchars((string) $artist['role'], ENT_QUOTES, 'UTF-8');
                    $artistBio = htmlspecialchars((string) $artist['bio'], ENT_QUOTES, 'UTF-8');
                    $artistImage = normalize_artist_image_path($artist['image']);
                    $artistImageEscaped = htmlspecialchars($artistImage, ENT_QUOTES, 'UTF-8');
                ?>
                <article class="artist-card">
                    <?php if ($artistImage !== ''): ?>
                        <div class="artist-card__media">
                            <img src="<?php echo $artistImageEscaped; ?>" alt="<?php echo $artistName; ?>">
                        </div>
                    <?php endif; ?>

                    <div class="artist-card__body">
                        <h3 class="artist-card__name"><?php echo $artistName; ?></h3>
                        <?php if ($artistRole !== ''): ?>
                            <div class="artist-card__meta"><?php echo $artistRole; ?></div>
                        <?php endif; ?>

                        <div class="artist-card__divider">
                            <span class="artist-card__star">✦</span>
                        </div>

                        <?php if ($artistBio !== ''): ?>
                            <p class="artist-card__desc"><?php echo $artistBio; ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<br>
    <br>
    <br>




            
        </div>
    
        <?php render_partners_footer(); ?>

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
