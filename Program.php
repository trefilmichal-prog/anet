<?php
require_once __DIR__ . '/includes/program_repository.php';
require_once __DIR__ . '/includes/background_repository.php';
require_once __DIR__ . '/includes/site_header.php';
require_once __DIR__ . '/includes/settings_repository.php';
require_once __DIR__ . '/includes/partners_repository.php';
$programItems = get_program_items();
$heroBackgroundImage = get_background_image('program', 'back.png');
$siteFontStyle = get_site_font_style();
$programPlaceholderText = get_setting('program_placeholder_text', 'Program bude brzy doplněn.');
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Background Gradient Layout</title>
    <link rel="stylesheet" href="Style.css">
    <script src="assets/cursor-trail.js" defer></script>
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
<body<?php echo $siteFontStyle; ?>>
    <div id="cursor-trail" aria-hidden="true"></div>
    <div id="intro-overlay" aria-hidden="true">
        <canvas id="intro-particles"></canvas>
        <div id="intro-mask"></div>
        <div id="intro-star" class="star-animated">✦</div>
    </div>
    <?php render_site_header(); ?>



    <!-- NAVAZUJ�C� OBSAH -->
    <section class="content" style="background-image: url('<?php echo htmlspecialchars($heroBackgroundImage, ENT_QUOTES, 'UTF-8'); ?>');">
        <div class="container2">
            <h2>PROGRAM III. ROČNÍK 2026</h2>
            <br>
            <?php if (empty($programItems)): ?>
                <p><?php echo htmlspecialchars($programPlaceholderText, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php else: ?>
                <div class="container2-divider2"><span class="star star-animated">✦</span></div>
                <div class="program-grid">
                    <?php foreach ($programItems as $item): ?>
                        <article class="program-card">
                            <div class="program-card__body">
                                <h3 class="program-card__title">
                                    <span class="program-card__title-text">
                                        <?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <?php if (!empty($item['subtitle'])): ?>
                                        <span class="program-card__subtitle"><?php echo htmlspecialchars($item['subtitle'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endif; ?>
                                </h3>
                                <?php if (!empty($item['venue'])): ?>
                                    <p class="program-card__venue"><?php echo htmlspecialchars($item['venue'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['event_date']) || !empty($item['event_time'])): ?>
                                    <p class="program-card__datetime"><?php echo htmlspecialchars(trim($item['event_date'] . ' ' . $item['event_time']), ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php render_partners_footer(); ?>

    </section>

    




</body>
</html>
