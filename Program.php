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
    </header>



    <!-- NAVAZUJ�C� OBSAH -->
    <section class="content">
        <div class="container2">
            <h2>PROGRAM III. ROČNÍK 2026</h2>
            <br>
            <div class="container2-divider2"><span class="star">✦</span></div>
            <br>
            <h3><strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">Roman Perucki</a></strong> – Polsko (varhany)</h3>
            <a>Kostel Krista dobrého Pastýře, Husova 1, Plzeň</a>
            <a></a>
            <br>
            <div class="container2-divider2"><span class="star">✦</span></div>
            <br>
            <h3><strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">Jesús Sampedro Márquez</a></strong> – Španělsko (varhany)</h3>
            <a>Kostel Krista dobrého Pastýře, Husova 1, Plzeň</a>
            <a></a>
            <br>
            <div class="container2-divider2"><span class="star">✦</span></div>
            <br>
            <h3><strong></strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">Karolína Cingrošová</a> – (soprán)<strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">, Anna Paulová </a></strong> - (klarinet)<strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">, Lukáš Sommer</a></strong> - (kytara)</h3>
            <a>Synagoga Milevsko</a>
            <a></a>
            <br>
            <div class="container2-divider2"><span class="star">✦</span></div>
            <br>
            <h3><strong></strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">Michaela Káčerková</a> – (varhany, cembalo)<strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">, Tomáš Strašil </a></strong> - (violoncello)</h3>
            <a>Kostel Krista dobrého Pastýře, Husova 1, Plzeň</a>
            <a></a>
            <br>
            <div class="container2-divider2"><span class="star">✦</span></div>
            <br>
            <h3><strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">Kateřina Málková</a></strong> – (varhany)<strong><a style="font-size: 1.5rem; color: rgb(255, 230, 173);" href="" data-type="page">, Josef Kovačič </a></strong> - (bas)</h3>
            <a>Kostel Krista dobrého Pastýře, Husova 1, Plzeň</a>
            <a></a>
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

    




</body>
</html>
