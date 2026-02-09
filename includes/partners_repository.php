<?php

require_once __DIR__ . '/db.php';

function get_partners()
{
    try {
        $db = get_db();
        $stmt = $db->query('SELECT id, name, url, image, sort_order FROM partners ORDER BY sort_order ASC, id DESC');
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Partners lookup failed: ' . $e->getMessage());
        return array();
    }
}

function render_partners_footer()
{
    $partners = get_partners();
    ?>
    <footer class="site-footer" id="sponsors">
        <div class="footer-inner">
            <div class="footer-title">Partneři</div>
            <div class="sponsors-row">
                <?php foreach ($partners as $partner): ?>
                    <?php
                    $name = isset($partner['name']) ? (string) $partner['name'] : '';
                    $url = isset($partner['url']) ? (string) $partner['url'] : '';
                    $image = isset($partner['image']) ? (string) $partner['image'] : '';
                    if ($image === '') {
                        continue;
                    }
                    $nameEscaped = htmlspecialchars($name !== '' ? $name : 'Partner', ENT_QUOTES, 'UTF-8');
                    $imageEscaped = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
                    $urlEscaped = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
                    ?>
                    <?php if ($url !== ''): ?>
                        <a href="<?php echo $urlEscaped; ?>" class="sponsor">
                            <img src="<?php echo $imageEscaped; ?>" alt="<?php echo $nameEscaped; ?>">
                        </a>
                    <?php else: ?>
                        <div class="sponsor">
                            <img src="<?php echo $imageEscaped; ?>" alt="<?php echo $nameEscaped; ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </footer>
    <?php
}
