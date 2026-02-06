<?php

require_once __DIR__ . '/settings_repository.php';
require_once __DIR__ . '/menu_repository.php';

function get_site_font_style()
{
    $siteFontFamilyValue = get_setting('site_font_family', get_site_font_family_default());
    $siteFontFamilyNormalized = normalize_site_font_family($siteFontFamilyValue);
    if ($siteFontFamilyNormalized === '') {
        $siteFontFamilyNormalized = get_site_font_family_default();
    }

    return ' style="--site-font-family: ' . htmlspecialchars($siteFontFamilyNormalized, ENT_QUOTES, 'UTF-8') . ';"';
}

function render_site_header()
{
    $siteMenuRgba = get_admin_menu_rgba($siteMenuEnabled);
    $siteMenuStyle = $siteMenuRgba !== '' ? ' style="--site-menu-bg: ' . htmlspecialchars($siteMenuRgba, ENT_QUOTES, 'UTF-8') . ';"' : '';
    $siteHeaderClass = $siteMenuEnabled ? 'site-header site-header--menu-bg' : 'site-header';
    $menuItems = get_menu_items();
    $logoPath = trim((string) get_setting('site_logo_path', ''));
    ?>
    <header class="<?php echo htmlspecialchars($siteHeaderClass, ENT_QUOTES, 'UTF-8'); ?>" id="head"<?php echo $siteMenuStyle; ?>>
        <div class="header-inner">
            <div class="logo">
                <?php if ($logoPath !== ''): ?>
                    <img src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>" alt="Harmonia Caelestis">
                <?php else: ?>
                    Harmonia Caelestis
                <?php endif; ?>
            </div>
            <button class="nav-toggle" type="button" aria-label="Menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <nav class="main-nav">
                <?php foreach ($menuItems as $item): ?>
                    <?php
                    $label = isset($item['label']) ? (string) $item['label'] : '';
                    $url = isset($item['url']) ? (string) $item['url'] : '';
                    $itemType = isset($item['item_type']) ? (string) $item['item_type'] : 'link';
                    ?>
                    <?php if ($itemType === 'icon'): ?>
                        <?php $iconClass = get_menu_icon_class($url, $label); ?>
                        <a class="icon<?php echo $iconClass !== '' ? ' ' . htmlspecialchars($iconClass, ENT_QUOTES, 'UTF-8') : ''; ?>" href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo htmlspecialchars($label !== '' ? $label : 'Ikonka', ENT_QUOTES, 'UTF-8'); ?>">
                            <span></span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </div>
        <script>
(function () {
  var header = document.querySelector('.site-header');
  var btn = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');

  if (!header || !btn || !nav) {
    return;
  }

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
})();
        </script>
    </header>
    <?php
}
