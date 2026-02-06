<?php
require_once __DIR__ . '/includes/program_repository.php';
require_once __DIR__ . '/includes/background_repository.php';
require_once __DIR__ . '/includes/site_header.php';
$programItems = get_program_items(3);
$heroBackgroundImage = get_background_image('home', 'kostel.jpg');
$homeContentBackgroundImage = get_background_image('home_content', 'back.png');
$siteFontStyle = get_site_font_style();
$brandType = trim((string) get_setting('brand_type', ''));
$brandValue = trim((string) get_setting('brand_value', ''));
$brandFallback = array(
    'type' => 'svg',
    'value' => 'inline'
);
$allowedBrandPositions = array('left', 'center');
$brandPositionDesktopValue = trim((string) get_setting('brand_position_desktop', ''));
$brandPositionMobileValue = trim((string) get_setting('brand_position_mobile', ''));
$brandPositionDesktop = in_array($brandPositionDesktopValue, $allowedBrandPositions, true)
    ? $brandPositionDesktopValue
    : 'center';
$brandPositionMobile = in_array($brandPositionMobileValue, $allowedBrandPositions, true)
    ? $brandPositionMobileValue
    : 'center';
$brandConfigPath = __DIR__ . '/includes/brand-config.php';
if (file_exists($brandConfigPath)) {
    $brandConfig = require $brandConfigPath;
    if (is_array($brandConfig)) {
        if (isset($brandConfig['type'])) {
            $brandFallback['type'] = $brandConfig['type'];
        }
        if (isset($brandConfig['value'])) {
            $brandFallback['value'] = $brandConfig['value'];
        }
    }
}
if ($brandType === '') {
    $brandType = $brandFallback['type'];
}
if ($brandValue === '') {
    $brandValue = $brandFallback['value'];
}
if (!in_array($brandType, array('text', 'image', 'svg'), true)) {
    $brandType = 'svg';
}
?>
﻿<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Background Gradient Layout</title>
    <link rel="stylesheet" href="Style.css">
    <script src="assets/cursor-trail.js" defer></script>
</head>
<body<?php echo $siteFontStyle; ?>>
    <div id="cursor-trail" aria-hidden="true"></div>
    <?php render_site_header(); ?>
    <script>
(function () {
  function runBrandAnim() {
    var el = document.getElementById('brand');
    if (!el) {
      return;
    }

    el.classList.remove('is-animate');
    void el.offsetWidth;
    el.classList.add('is-animate');
  }

  window.addEventListener('load', runBrandAnim);
  window.addEventListener('pageshow', runBrandAnim);
})();
    </script>


    <!-- HERO / HEADER -->
    <section class="hero" id="img" style="background-image: url('<?php echo htmlspecialchars($heroBackgroundImage, ENT_QUOTES, 'UTF-8'); ?>');">
        <div class="hero__overlay"></div>
        <div class="home-ornaments" aria-hidden="true">
            <img src="assets/lorg.png" alt="">
            <img src="assets/porg.png" alt="">
        </div>


        <div class="hero__content">


            <div class="brand brand--pos-desktop-<?php echo htmlspecialchars($brandPositionDesktop, ENT_QUOTES, 'UTF-8'); ?> brand--pos-mobile-<?php echo htmlspecialchars($brandPositionMobile, ENT_QUOTES, 'UTF-8'); ?>" id="brand">
                <?php if ($brandType === 'text'): ?>
                    <span class="brand-text"><?php echo htmlspecialchars($brandValue, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php elseif ($brandType === 'image'): ?>
                    <img class="brand-image" src="<?php echo htmlspecialchars($brandValue, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo">
                <?php else: ?>
                    <?php
                    $inlineSvg = null;
                    if (!empty($brandValue) && $brandValue !== 'inline') {
                        $svgPath = $brandValue;
                        if (!preg_match('/^\\//', $svgPath)) {
                            $svgPath = __DIR__ . '/' . ltrim($svgPath, '/');
                        }
                        if (file_exists($svgPath)) {
                            $inlineSvg = file_get_contents($svgPath);
                        }
                    }
                    ?>
                    <?php if (!empty($inlineSvg)): ?>
                        <?php echo $inlineSvg; ?>
                    <?php else: ?>
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
                            <text class="brand-text-path" x="160" y="210">armonia Caelestis</text>
                        </svg>
                    <?php endif; ?>
                <?php endif; ?>
            </div>






        </div>
    </section>



    <!-- NAVAZUJÍCÍ OBSAH -->
    <section class="content" style="background-image: url('<?php echo htmlspecialchars($homeContentBackgroundImage, ENT_QUOTES, 'UTF-8'); ?>');">
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
