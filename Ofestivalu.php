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
            <h2>O FESTIVALU</h2>
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
