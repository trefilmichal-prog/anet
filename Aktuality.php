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
