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

    




</body>
</html>
