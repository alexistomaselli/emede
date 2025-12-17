<?php
require_once 'config.php';
$db = getDB();

// --- FETCH SETTINGS ---
// 1. Menu
$menu_json = get_setting('site_menu');
if ($menu_json) {
    $menu_items = json_decode($menu_json, true);
} else {
    // Fallback if no custom menu yet
    $pages = $db->query("SELECT title as label, slug FROM pages WHERE in_menu = 1 ORDER BY menu_order ASC")->fetchAll(PDO::FETCH_ASSOC);
    $menu_items = [];
    foreach ($pages as $p) {
        $menu_items[] = ['label' => $p['label'], 'url' => '?p=' . $p['slug'], 'type' => 'page'];
    }
}

// 2. Header Config
$header_conf = json_decode(get_setting('site_header'), true) ?: ['layout' => 'logo_left', 'show_social' => false, 'show_search' => false];

// 3. Footer Config
$footer_conf = json_decode(get_setting('site_footer'), true) ?: ['columns' => 3];


// Determine Current Page
$current_slug = $_GET['p'] ?? 'inicio';
$page_stmt = $db->prepare("SELECT * FROM pages WHERE slug = ?");
$page_stmt->execute([$current_slug]);
$current_page = $page_stmt->fetch();

// 404 Handling
if (!$current_page) {
    http_response_code(404);
    $current_page = [
        'title' => 'Página no encontrada',
        'template' => '404',
        'content' => '{}'
    ];
}

$page = $current_page; // For templates relying on variable $page
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']); ?> |
        <?php echo htmlspecialchars(get_setting('site_title', 'Gráfica Emede')); ?>
    </title>
    <link rel="preconnect"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dynamic Header Styles */
        .header-container.logo-center {
            justify-content: center;
            flex-direction: column;
            padding: 20px 0;
        }

        .header-container.logo-center .nav {
            margin-top: 20px;
        }

        .header-icons {
            display: flex;
            gap: 15px;
            margin-left: 20px;
        }

        .header-icons i {
            cursor: pointer;
            color: var(--text);
        }

        /* Dynamic Footer Grid */
        .footer-grid-dynamic {
            display: grid;
            grid-template-columns: repeat(<?php echo $footer_conf['columns']; ?>, 1fr);
            gap: 40px;
        }

        @media (max-width: 768px) {
            .footer-grid-dynamic {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="header">
        <div
            class="container header-container <?php echo $header_conf['layout'] === 'logo_center' ? 'logo-center' : ''; ?>">
            <a href="index.php"
                class="logo"><?php echo htmlspecialchars(get_setting('logo_text', 'Gráfica Emede.')); ?></a>

            <nav class="nav">
                <ul class="nav-list">
                    <?php foreach ($menu_items as $item): ?>
                        <li><a href="<?php echo htmlspecialchars($item['url']); ?>"
                                class="<?php echo ($current_slug == ($item['slug'] ?? '')) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($item['label']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <?php if ($header_conf['layout'] !== 'logo_center'): ?>
                <div class="header-icons">
                    <?php if (!empty($header_conf['show_search'])): ?>
                        <i class="fas fa-search"></i>
                    <?php endif; ?>
                    <?php if (!empty($header_conf['show_social'])): ?>
                        <i class="fab fa-instagram"></i>
                        <i class="fab fa-facebook"></i>
                    <?php endif; ?>
                    <a href="#contacto" class="btn btn-primary"
                        style="margin-left:10px"><?php echo htmlspecialchars(get_setting('hero_cta', 'Cotizar')); ?></a>
                </div>
            <?php endif; ?>

            <div class="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT RENDER -->
    <main>
        <?php
        $template_file = 'templates/t-' . $page['template'] . '.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="container section-padding"><h2>Error: Plantilla no encontrada</h2><p>La plantilla solicitada (' . htmlspecialchars($page['template']) . ') no existe.</p></div>';
        }
        ?>
    </main>

    <!-- Footer / Contacto -->
    <section id="contacto" class="footer bg-darker text-white">
        <div class="container">
            <div class="footer-grid-dynamic">
                <?php
                for ($i = 1; $i <= $footer_conf['columns']; $i++) {
                    echo '<div class="footer-col">';

                    // Render Logo in Col 1 if enabled
                    if ($i === 1 && !empty($footer_conf['show_logo'])) {
                        echo '<div style="margin-bottom: 15px;">';
                        echo '<a href="index.php" class="logo" style="margin-right:0; font-size: 1.8rem; color:white;">' . htmlspecialchars(get_setting('logo_text', 'Gráfica Emede.')) . '</a>';
                        echo '</div>';
                    }

                    $content = $footer_conf["col$i"] ?? '';
                    // Allow simple HTML, or render default if empty (only for Col 1 in default mode)
                    if (empty($content) && $i === 1 && !get_setting('site_footer')) {
                        // Default Fallback
                        echo '<h3>Contacto</h3>';
                        echo '<p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars(get_setting('address')) . '</p>';
                        echo '<p><i class="fas fa-envelope"></i> ' . htmlspecialchars(get_setting('email')) . '</p>';
                    } else {
                        echo nl2br($content); // Basic rendering
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <div class="copyright text-center mt-5">
                <p>&copy; 2024 <?php echo htmlspecialchars(get_setting('site_title')); ?>. Todos los derechos
                    reservados.</p>
            </div>
        </div>
    </section>

</body>

</html>