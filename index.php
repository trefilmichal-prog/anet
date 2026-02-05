<?php
require_once __DIR__ . '/includes/program_repository.php';
$programItems = get_program_items(3);
?>
﻿<!doctype html>
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

  if (!header || !btn || !nav) return;

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
});
</script>
</head>
<body>
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

        <script>
(function () {
  function runBrandAnim() {
    const el = document.getElementById('brand');
    if (!el) return;

    // reset (aby se to vždycky znovu rozjelo)
    el.classList.remove('is-animate');
    void el.offsetWidth; // reflow hack
    el.classList.add('is-animate');
  }

  window.addEventListener('load', runBrandAnim);
  window.addEventListener('pageshow', runBrandAnim); // když se stránka vrátí z cache (Safari/Firefox)
})();
</script>

    </header>


    <!-- HERO / HEADER -->
    <section class="hero" id="img">
        <div class="hero__overlay"></div>


        <div class="hero__content">


            <div class="brand">
 <svg class="brand-mark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 455.35 252.33" aria-hidden="true">
  <defs>
    <style>
      .cls-1{
        fill:none;
        stroke:#f2e9dc;          /* barva */
        stroke-linecap:round;
        stroke-miterlimit:10;
        stroke-width:12px;
      }
    </style>
  </defs>

  <!-- 1) levá čára -->
  <line class="cls-1"
        x1="57.78" y1="56.11" x2="57.78" y2="246.33"
        pathLength="1" stroke-dasharray="1" stroke-dashoffset="1">
    <animate id="t1" attributeName="stroke-dashoffset" from="1" to="0" dur="0.35s" begin="0s" fill="freeze"/>
  </line>

  <!-- 2) pravá čára -->
  <line class="cls-1"
        x1="137.55" y1="18.54" x2="137.55" y2="221.22"
        pathLength="1" stroke-dasharray="1" stroke-dashoffset="1">
    <animate id="t2" attributeName="stroke-dashoffset" from="1" to="0" dur="0.35s" begin="t1.end" fill="freeze"/>
  </line>

  <!-- 3) křivka -->
  <path class="cls-1"
        d="M6.31,202.83s-6.79-21,43.43-51.61C141.32,95.4,449.35,6,449.35,6"
        pathLength="1" stroke-dasharray="1" stroke-dashoffset="1">
    <animate id="t3" attributeName="stroke-dashoffset" from="1" to="0" dur="1.05s" begin="t2.end" fill="freeze"/>
  </path>
</svg>










  <span class="brand-text">armonia Caelestis</span>
</div>






        </div>
    </section>



    <!-- NAVAZUJÍCÍ OBSAH -->
    <section class="content">
        <div class="container" id="button">
            <a href="Program.php" class="btn-program">
                Program 2026
                <span class="btn-arrow">›</span>
            </a>

        </div>
        <section class="feature-grid" id="boxs">

            <!-- BOX 1 -->
            <article class="glass-card" id="aktuality">
                <h3 class="glass-title">Program</h3>

                <div class="glass-list">
                    <?php if (empty($programItems)): ?>
                        <div class="glass-item">
                            <div class="glass-note">Program bude brzy doplněn.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($programItems as $index => $item): ?>
                            <div class="glass-item">
                                <div class="glass-date"><?php echo htmlspecialchars(trim($item['event_date'] . ' ' . $item['event_time']), ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="glass-place"><?php echo htmlspecialchars($item['venue'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="glass-note"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php if (!empty($item['subtitle'])): ?>
                                    <div class="glass-note"><?php echo htmlspecialchars($item['subtitle'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                            </div>

                            <?php if ($index < count($programItems) - 1): ?>
                                <div class="glass-divider"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="glass-actions">
                    <a class="btn-glass" href="Program.php">Zobrazit program <span>›</span></a>
                </div>
            </article>

            <!-- BOX 2 -->
            <article class="glass-card" id="festival">
                <h3 class="glass-title">O festivalu</h3>

                <div class="glass-media">
                    <img src="festival.jpg" alt="">
                </div>

                <p class="glass-text">
                    Festival klasické hudby zve z srdce hudební příznivce na koncerty a večery plné jedinečné atmosféry.
                </p>

                <div class="glass-actions">
                    <a class="btn-glass" href="Ofestivalu.php">Více o festivalu <span>›</span></a>
                </div>
            </article>

            <!-- BOX 3 -->
            <article class="glass-card" id="umelci">
                <h3 class="glass-title">Umělci</h3>

                <div class="artist-row">
                    <div class="artist">
                        <img src="a1.png" alt="">
                        <div class="artist-name">Michaela Káčerková</div>
                    </div>
                    <div class="artist">
                        <img src="a2.png" alt="">
                        <div class="artist-name">Josef Kovačič</div>
                    </div>
                    <div class="artist">
                        <img src="a3.png" alt="">
                        <div class="artist-name">Kateřina Málková</div>
                    </div>
                </div>

                <p class="glass-text">
                    Špičkoví interpreti, jedinečné programy a slavnostní koncerty v mimořádných prostorách.
                </p>

                <div class="glass-actions">
                    <a class="btn-glass" href="Umelci.php">Všichni umělci <span>›</span></a>
                </div>
            </article>

        </section>
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
