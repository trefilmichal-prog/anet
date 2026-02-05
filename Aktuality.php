<?php
require_once __DIR__ . '/includes/db.php';

$newsRows = array();
$newsError = '';

try {
    $db = get_db();
    $stmt = $db->query("SELECT id, title, body, image, published_at, sort_order FROM news
        ORDER BY
            CASE
                WHEN published_at IS NULL OR TRIM(published_at) = '' THEN 1
                ELSE 0
            END ASC,
            datetime(published_at) DESC,
            sort_order ASC,
            id DESC");
    $newsRows = $stmt->fetchAll();
} catch (RuntimeException $e) {
    error_log('Aktuality DB failed: ' . $e->getMessage());
    $newsError = 'Aktuality se teď nepodařilo načíst. Zkuste to prosím později.';
    $newsRows = array();
} catch (Exception $e) {
    error_log('Aktuality load failed: ' . $e->getMessage());
    $newsError = 'Aktuality se teď nepodařilo načíst. Zkuste to prosím později.';
    $newsRows = array();
}

function split_news_body($body)
{
    $normalized = trim(str_replace(array("\r\n", "\r"), "\n", (string) $body));
    if ($normalized === '') {
        return array('', array());
    }

    $parts = preg_split("/\n\s*\n+/", $normalized);
    $paragraphs = array();

    foreach ($parts as $part) {
        $clean = trim($part);
        if ($clean !== '') {
            $paragraphs[] = $clean;
        }
    }

    if (!$paragraphs) {
        return array('', array());
    }

    $lead = array_shift($paragraphs);
    return array($lead, $paragraphs);
}
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Background Gradient Layout</title>
    <link rel="stylesheet" href="Style.css">

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

/* ===== News Cards (Aktuality) ===== */
.news__container{margin-top:18px;}
.news__grid{
  display:grid;
  grid-template-columns:repeat(12,1fr);
  gap:18px;
}
@media (max-width: 980px){
  .news__grid{grid-template-columns:repeat(6,1fr);}
}
@media (max-width: 640px){
  .news__grid{grid-template-columns:repeat(1,1fr);}
}
.news-card{
  grid-column:span 12;
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.10);
  border-radius:18px;
  overflow:hidden;
  box-shadow:0 14px 50px rgba(0,0,0,.22);
  backdrop-filter: blur(10px);
}
.news-card__top{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:14px;
  padding:18px 18px 0 18px;
}
.news-card__tag{
  display:inline-flex;
  align-items:center;
  font-size:.85rem;
  letter-spacing:.06em;
  text-transform:uppercase;
  opacity:.8;
  padding:0;
  border:none;
  background:transparent;
  white-space:nowrap;
}
.news-card__date{
  opacity:.8;
  font-size:.9rem;
  white-space:nowrap;
}
.news-card__body{padding:12px 18px 18px 18px;}
.news-card__image-wrap{
  padding:0 18px;
}
.news-card__image{
  display:block;
  width:100%;
  max-height:320px;
  object-fit:cover;
  border-radius:12px;
}
.news-card__title{
  margin:0 0 10px 0;
  font-size:1.25rem;
  line-height:1.25;
}
.news-card__lead{
  margin:0 0 10px 0;
  opacity:.92;
}
.news-card__text p{margin:0 0 10px 0; opacity:.88;}
.news-card__text p:last-child{margin-bottom:0;}
.news-card__btn:hover{
  transform:translateY(-2px);
  background:rgba(255,255,255,.12);
  border-color:rgba(255,255,255,.22);
}
.news-card details{
  margin-top:10px;
  border-top:1px solid rgba(255,255,255,.10);
  padding-top:12px;
}
.news-card summary{
  cursor:pointer;
  list-style:none;
  opacity:.9;
}
.news-card summary::-webkit-details-marker{display:none;}
.news-card summary:after{
  content:"▾";
  float:right;
  opacity:.7;
  transform:translateY(1px);
}
.news-card details[open] summary:after{content:"▴";}
</style>

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


    <!-- NAVAZUJÍCÍ OBSAH -->
    <section class="content">
        <div class="container2">
            <h2>AKTUALITY</h2>
            <br>
            <h3>Novinky a tiskové zprávy</h3>
            <div class="container2-divider"></div>

            <div class="news__container">
                <div class="news__grid">
                    <?php if ($newsError !== ''): ?>
                        <article class="news-card">
                            <div class="news-card__body">
                                <p class="news-card__lead"><?php echo htmlspecialchars($newsError, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </article>
                    <?php elseif (!$newsRows): ?>
                        <article class="news-card">
                            <div class="news-card__body">
                                <p class="news-card__lead">Zatím bez aktualit</p>
                            </div>
                        </article>
                    <?php else: ?>
                        <?php foreach ($newsRows as $row): ?>
                            <?php
                            $bodyData = split_news_body(isset($row['body']) ? $row['body'] : '');
                            $leadText = $bodyData[0];
                            $restParagraphs = $bodyData[1];
                            ?>
                            <article class="news-card">
                                <div class="news-card__top">
                                    <span class="news-card__tag">Sdělení</span>
                                    <div class="news-card__date"><?php echo htmlspecialchars((string) $row['published_at'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>

                                <?php if (!empty($row['image'])): ?>
                                    <div class="news-card__image-wrap">
                                        <img class="news-card__image" src="<?php echo htmlspecialchars((string) $row['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) $row['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="news-card__body">
                                    <h3 class="news-card__title"><?php echo htmlspecialchars((string) $row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>

                                    <?php if ($leadText !== ''): ?>
                                        <p class="news-card__lead"><?php echo nl2br(htmlspecialchars($leadText, ENT_QUOTES, 'UTF-8')); ?></p>
                                    <?php endif; ?>

                                    <?php if ($restParagraphs): ?>
                                        <details>
                                            <summary>Číst celý článek</summary>
                                            <div class="news-card__text">
                                                <?php foreach ($restParagraphs as $paragraph): ?>
                                                    <p><?php echo nl2br(htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8')); ?></p>
                                                <?php endforeach; ?>
                                            </div>
                                        </details>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>
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

    <!-- PŮVODNÍ VERZE (záloha pro porovnání) -->
    <!--
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Background Gradient Layout</title>
    <link rel="stylesheet" href="Style.css">
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

            <!— LOGO —>
            <div class="logo">
                Harmonia Caelestis
            </div>
<button class="nav-toggle" type="button" aria-label="Menu" aria-expanded="false">
             <span></span> 
             <span></span>
             <span></span>
            </button>
            <!— NAVIGACE —>
            <nav class="main-nav">
                <a href="index.php">Úvod</a>
                <a href="Aktuality.php">Aktuality</a>
                <a href="Program.php">Program</a>
                <a href="Umelci.php">Umělci</a>
                <a href="Ofestivalu.php">O festivalu</a>
                <a class="icon icon—fb" href="https://www.facebook.com/hcimfcz/" aria-label="Facebook"><span></span></a>
                <a class="icon icon—mail" href="mailto:reditel@ipd-ccsh.cz" aria-label="E-mail"><span></span></a>




            </nav>

        </div>
    </header>



    <!— NAVAZUJ�C� OBSAH —>
    <section class="content">
        <div class="container2">
            <h2>AKTUALITY</h2>
           
            <a>S radostí připravujeme třetí ročník festivalu. Zveme všechny milovníky klasické hudby do kostela Krista dobrého Pastýře již na neděli 15. června v 16 hodin. Setkáme se při poslechu kvalitní hudby v interpretaci polského umělce Romana Perucki.
            </a>
            <a>Vstupné je, jako vždy, dobrovolné.</a>
            <br>
            <div class="container2-divider"></div>
            <br>
            <h3 style="margin-top: 1rem;">Harmonia Caelestis vstupuje do třetí sezóny, získal podporu pro další rok</h3>
            <a>Plzeň, 26. 5. 2026. Festival klasické hudby Harmonia Caelestis získal i pro rok 2026 od magistrátu města Plzeň, města Milevsko a Ministerstva kultury ČR podporu pro pořádání koncertů. Stejně jako loni se jejich místem konání stane kostel Krista dobrého Pastýře na Husově ulici. </a>
            <a>„Máme radost, že festival Harmonia Caelestis může díky podpoře města Plzeň, Ministerstva kultury a města Milevsko zahájit svůj třetí ročník,“ říká ředitel festivalu Lukáš Moc. „I v letošním roce se návštěvníci mohou těšit na pestrý program, v jehož rámci zazní skladby známých i méně známých skladatelů napříč žánry i érami klasické hudby.“</a>    
            <br>
            <div class="container2-divider"></div>
            <br>
            <a style="margin-top: 1rem;">Kostel jako koncertní síň</a>
            <a>Druhý ročník festivalu Harmonia Caelestis nabídne pásmo koncertů, které se budou konat od června 2025. Stejně jako v průběhu loňského, debutového ročníku se i letos budou koncerty konat v prostorách kostela Krista dobrého Pastýře, který se nachází na plzeňské Husově ulici. Letos se navíc jeden z koncertů odehraje také v prostorách milevské synagogy.</a>
            <br>
            <br>
            <h3>Kultura dostupná všem</h3>
            <a>Jedním z cílů festivalu Harmonia Caelestis je zpřístupnit klasickou hudbu co možná nejširšímu publiku a v duchu svého motta „Tóny nebeské harmonie otevírají duše a spojují světy“ propojit publikum s tvůrci i interprety. </a>
            <a>„V některých lidech je zakořeněné přesvědčení, že klasická hudba je něčím příliš komplikovaným, náročným, nezábavným, případně že jsou koncerty vážné hudby drahé  a určené vyšším vrstvám. To v případě našeho festivalu neplatí,“ zdůrazňuje Lukáš Moc. Vstupné na všechny koncerty v rámci festivalu je dobrovolné, návštěvníci se o tvůrcích, dílech i interpretech dozví vše potřebné srozumitelnou a zábavnou formou na sociálních sítích i webových stránkách festivalu. </a>
            <a>„Za to, že si můžeme dovolit nasadit dobrovolné vstupné, vděčíme Magistrátu města Plzně, Ministerstvu kultury a městu Milevsko, kteří našemu festivalu s důvěrou poskytli velkorysou finanční podporu,“ dodává Moc.</a>
            <a>První koncert v rámci festivalu se odehraje v prostorách kostela Krista dobrého Pastýře již v průběhu června. V průběhu roku budou realizovány další koncerty klasické hudby v interpretaci špičkových českých i zahraničních umělců.</a>


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

    -->
</body>
</html>
