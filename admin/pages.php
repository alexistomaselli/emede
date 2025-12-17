<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$message = '';
$db = getDB();

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $template = $_POST['template_type']; // Hidden field

    // Build Content JSON
    $content_data = [];

    // Special Case: Builder Template saves JSON directly from hidden input
    if ($template === 'builder') {
        $raw_json = $_POST['builder_json'] ?? '[]';
        $content_data = json_decode($raw_json, true) ?? [];
    }
    // Standard Templates
    elseif ($template === 'about') {
        $content_data = [
            'history_title' => $_POST['history_title'],
            'history_text' => $_POST['history_text'],
            'mission_title' => $_POST['mission_title'],
            'mission_text' => $_POST['mission_text'],
            'image_url' => $_POST['image_url']
        ];
    } elseif ($template === 'services') {
        $content_data = [
            'subtitle' => $_POST['subtitle'],
            'intro' => $_POST['intro'],
            'service_1_title' => $_POST['service_1_title'],
            'service_1_image' => $_POST['service_1_image'],
            'service_2_title' => $_POST['service_2_title'],
            'service_2_image' => $_POST['service_2_image'],
            'service_3_title' => $_POST['service_3_title'],
            'service_3_image' => $_POST['service_3_image'],
            'extra_title_1' => $_POST['extra_title_1'],
            'extra_text_1' => $_POST['extra_text_1'],
            'extra_title_2' => $_POST['extra_title_2'],
            'extra_text_2' => $_POST['extra_text_2'],
        ];
    } elseif ($template === 'start') {
        $content_data = [
            'intro_title' => $_POST['intro_title'],
            'intro_text' => $_POST['intro_text'],
        ];
        // Loop for stats and testimonials
        for ($i = 1; $i <= 4; $i++) {
            $content_data["stat_{$i}_number"] = $_POST["stat_{$i}_number"];
            $content_data["stat_{$i}_label"] = $_POST["stat_{$i}_label"];
            $content_data["stat_{$i}_icon"] = $_POST["stat_{$i}_icon"];

            $content_data["testimonial_{$i}_text"] = $_POST["testimonial_{$i}_text"];
            $content_data["testimonial_{$i}_author"] = $_POST["testimonial_{$i}_author"];
        }
    } elseif ($template === 'gallery') {
        // Handle array of images. logic: allow up to 10 for simplicity in V1
        $images = [];
        for ($i = 0; $i < 10; $i++) {
            if (!empty($_POST["image_$i"])) {
                $images[] = $_POST["image_$i"];
            }
        }
        $content_data = ['images' => $images];
    }

    $content_json = json_encode($content_data);

    // Update
    $stmt = $db->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, in_menu = ?, menu_order = ? WHERE id = ?");
    $stmt->execute([
        $_POST['title'],
        $_POST['slug'],
        $content_json,
        isset($_POST['in_menu']) ? 1 : 0,
        (int) $_POST['menu_order'],
        $id
    ]);

    // Handle existing page update
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $template = $_POST['template_type']; // Hidden field

        // Build Content JSON
        $content_data = [];

        // Special Case: Builder Template saves JSON directly
        if ($template === 'builder') {
            $raw_json = $_POST['builder_json'] ?? '[]';
            $content_data = json_decode($raw_json, true) ?? [];
        }
        // Standard Templates
        elseif ($template === 'about') {
            $content_data = [
                'history_title' => $_POST['history_title'],
                'history_text' => $_POST['history_text'],
                'mission_title' => $_POST['mission_title'],
                'mission_text' => $_POST['mission_text'],
                'image_url' => $_POST['image_url']
            ];
        } elseif ($template === 'services') {
            $content_data = [
                'subtitle' => $_POST['subtitle'],
                'intro' => $_POST['intro'],
                'service_1_title' => $_POST['service_1_title'],
                'service_1_image' => $_POST['service_1_image'],
                'service_2_title' => $_POST['service_2_title'],
                'service_2_image' => $_POST['service_2_image'],
                'service_3_title' => $_POST['service_3_title'],
                'service_3_image' => $_POST['service_3_image'],
                'extra_title_1' => $_POST['extra_title_1'],
                'extra_text_1' => $_POST['extra_text_1'],
                'extra_title_2' => $_POST['extra_title_2'],
                'extra_text_2' => $_POST['extra_text_2'],
            ];
        } elseif ($template === 'start') {
            $content_data = [
                'intro_title' => $_POST['intro_title'],
                'intro_text' => $_POST['intro_text'],
            ];
            // Loop for stats and testimonials
            for ($i = 1; $i <= 4; $i++) {
                $content_data["stat_{$i}_number"] = $_POST["stat_{$i}_number"];
                $content_data["stat_{$i}_label"] = $_POST["stat_{$i}_label"];
                $content_data["stat_{$i}_icon"] = $_POST["stat_{$i}_icon"];

                $content_data["testimonial_{$i}_text"] = $_POST["testimonial_{$i}_text"];
                $content_data["testimonial_{$i}_author"] = $_POST["testimonial_{$i}_author"];
            }
        } elseif ($template === 'gallery') {
            // Handle array of images. logic: allow up to 10 for simplicity in V1
            $images = [];
            for ($i = 0; $i < 10; $i++) {
                if (!empty($_POST["image_$i"])) {
                    $images[] = $_POST["image_$i"];
                }
            }
            $content_data = ['images' => $images];
        }

        $content_json = json_encode($content_data);

        // Update
        $stmt = $db->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, in_menu = ?, menu_order = ? WHERE id = ?");
        $stmt->execute([
            $_POST['title'],
            $_POST['slug'],
            $content_json,
            isset($_POST['in_menu']) ? 1 : 0,
            (int) $_POST['menu_order'],
            $id
        ]);

        echo "<div class='alert success'>Página actualizada correctamente. <a href='../index.php?p={$_POST['slug']}' target='_blank'>Ver cambios</a></div>";
        // Refresh data
        $edit_page = $db->query("SELECT * FROM pages WHERE id = $id")->fetch();
    } elseif (isset($_POST['new_title']) && isset($_POST['new_template'])) { // Handle new page creation
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['new_title'])));
        $stmt = $db->prepare("INSERT INTO pages (title, slug, template, content, in_menu, menu_order) VALUES (?, ?, ?, '{}', 1, 99)");
        $stmt->execute([$_POST['new_title'], $slug, $_POST['new_template']]);
        header("Location: pages.php?edit=" . $db->lastInsertId());
        exit;
    }
}

// Handle Edit Fetch
$edit_page = null;
$content_data = [];
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_page = $stmt->fetch();
    if ($edit_page) {
        $content_data = json_decode($edit_page['content'], true) ?? [];
    }
}

// List Pages
$pages = $db->query("SELECT * FROM pages")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Páginas - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: #f8fafc;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #0f172a;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            overflow-y: auto;
        }

        .content {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 40px;
            display: block;
            color: white;
            text-decoration: none;
        }

        .menu a {
            display: block;
            padding: 12px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .menu a:hover,
        .menu a.active {
            background: #1e293b;
            color: white;
        }

        .menu a i {
            margin-right: 10px;
            width: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            font-weight: 600;
            color: #475569;
        }

        .btn-edit {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            font-family: inherit;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        .section-break {
            border-top: 1px dashed #e2e8f0;
            margin: 20px 0;
            padding-top: 20px;
        }

        button {
            padding: 12px 25px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .alert {
            padding: 15px;
            background: #dcfce7;
            color: #166534;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .badge {
            background: #e2e8f0;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <a href="index.php" class="logo">Gráfica Emede</a>
        <nav class="menu">
            <a href="/admin/index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="/admin/settings.php"><i class="fas fa-cog"></i> Configuración Global</a>
            <a href="/admin/pages.php" class="active"><i class="fas fa-file-alt"></i> Páginas</a>
            <a href="/admin/media.php"><i class="fas fa-images"></i> Medios</a>
            <a href="/admin/appearance.php"><i class="fas fa-paint-brush"></i> Apariencia</a>
            <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Sitio</a>
            <a href="/admin/logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fas fa-sign-out-alt"></i>
                Salir</a>
        </nav>
    </div>
    <div class="content">
        <h1>Gestión de Páginas</h1>

        <!-- Create New Page Form -->
        <div class="card">
            <h3>Crear Nueva Página</h3>
            <form method="post" style="display: flex; gap: 10px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label>Título</label>
                    <input type="text" name="new_title" placeholder="Ej: Contacto" required style="margin-bottom: 0;">
                </div>
                <div style="flex: 1;">
                    <label>Plantilla</label>
                    <select name="new_template"
                        style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 5px;" required>
                        <option value="about">Historia / Institucional</option>
                        <option value="services">Servicios</option>
                        <option value="gallery">Galería</option>
                        <option value="start">Inicio (Solo una)</option>
                        <option value="builder">Constructor Flexible (Bloques)</option>
                    </select>
                </div>
                <button type="submit" style="height: 42px;">Crear Página</button>
            </form>
        </div>

        <!-- List Info -->
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Título</th>
                        <th>Slug (URL)</th>
                        <th>Plantilla</th>
                        <th>En Menú</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                        <tr>
                            <td><?php echo $p['menu_order']; ?></td>
                            <td><?php echo htmlspecialchars($p['title']); ?></td>
                            <td><?php echo htmlspecialchars($p['slug']); ?></td>
                            <td>
                                <span class="badge"><?php echo htmlspecialchars($p['template']); ?></span>
                            </td>
                            <td>
                                <?php echo $p['in_menu'] ? '<span style="color:green">Sí</span>' : '<span style="color:red">No</span>'; ?>
                            </td>
                            <td>
                                <a href="pages.php?edit=<?php echo $p['id']; ?>" class="btn-small">Editar</a>
                                <a href="pages.php?delete=<?php echo $p['id']; ?>" class="btn-small btn-danger"
                                    onclick="return confirm('¿Seguro?')">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Form -->
        <?php if ($edit_page): ?>
            <div class="card" id="edit-form">
                <?php if ($message)
                    echo "<div class='alert'>$message</div>"; ?>
                <h3>Editar Página: <?php echo htmlspecialchars($edit_page['title']); ?></h3>
                <form method="post" action="pages.php?edit=<?php echo $edit_page['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo $edit_page['id']; ?>">
                    <input type="hidden" name="template_type" value="<?php echo $edit_page['template']; ?>">
                    <div
                        style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;">
                        <h4>Configuración General</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <label>Título de la Página</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_page['title']); ?>"
                                    required>
                            </div>
                            <div>
                                <label>Slug (URL amigable)</label>
                                <input type="text" name="slug" value="<?php echo htmlspecialchars($edit_page['slug']); ?>"
                                    required>
                            </div>
                            <div>
                                <label>Orden en Menú</label>
                                <input type="number" name="menu_order"
                                    value="<?php echo htmlspecialchars($edit_page['menu_order']); ?>">
                            </div>
                            <div style="display: flex; align-items: center; padding-top: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" name="in_menu" value="1" <?php echo $edit_page['in_menu'] ? 'checked' : ''; ?> style="width: auto; margin-right: 10px;">
                                    Mostrar en Menú
                                </label>
                            </div>
                        </div>
                        <label>Plantilla (No cambiable)</label>
                        <input type="text" value="<?php echo htmlspecialchars($edit_page['template']); ?>" disabled
                            style="background: #f0f0f0;">
                    </div>

                    <div
                        style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <h4>Contenido de la Página</h4>
                        <!-- DYNAMIC FIELDS BASED ON TEMPLATE -->
                        <?php if ($edit_page['template'] == 'about'): ?>
                            <div class="section-break"></div>
                            <h4>Sección Historia</h4>
                            <label>Título Historia</label>
                            <input type="text" name="history_title"
                                value="<?php echo htmlspecialchars($content_data['history_title'] ?? ''); ?>">
                            <label>Texto Historia</label>
                            <textarea name="history_text"
                                rows="5"><?php echo htmlspecialchars($content_data['history_text'] ?? ''); ?></textarea>

                            <label>Imagen URL (Principal)</label>
                            <input type="text" name="image_url"
                                value="<?php echo htmlspecialchars($content_data['image_url'] ?? ''); ?>">

                            <div class="section-break"></div>
                            <h4>Sección Misión / Política</h4>
                            <label>Título Bloque</label>
                            <input type="text" name="mission_title"
                                value="<?php echo htmlspecialchars($content_data['mission_title'] ?? ''); ?>">
                            <label>Texto Bloque</label>
                            <textarea name="mission_text"
                                rows="3"><?php echo htmlspecialchars($content_data['mission_text'] ?? ''); ?></textarea>

                        <?php elseif ($edit_page['template'] == 'services'): ?>
                            <label>Subtítulo</label>
                            <input type="text" name="subtitle"
                                value="<?php echo htmlspecialchars($content_data['subtitle'] ?? ''); ?>">
                            <label>Intro Texto</label>
                            <textarea name="intro"
                                rows="3"><?php echo htmlspecialchars($content_data['intro'] ?? ''); ?></textarea>

                            <div class="section-break"></div>
                            <h4>Bloques de Servicio (Hasta 3)</h4>
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <div style="background: #f1f5f9; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                                    <label>Servicio <?php echo $i; ?> - Título</label>
                                    <input type="text" name="service_<?php echo $i; ?>_title"
                                        value="<?php echo htmlspecialchars($content_data["service_{$i}_title"] ?? ''); ?>">
                                    <label>Servicio <?php echo $i; ?> - Imagen URL</label>
                                    <input type="text" name="service_<?php echo $i; ?>_image"
                                        value="<?php echo htmlspecialchars($content_data["service_{$i}_image"] ?? ''); ?>">
                                </div>
                            <?php endfor; ?>

                            <div class="section-break"></div>
                            <h4>Info Extra (2 Columnas)</h4>
                            <label>Título Extra 1</label>
                            <input type="text" name="extra_title_1"
                                value="<?php echo htmlspecialchars($content_data['extra_title_1'] ?? ''); ?>">
                            <label>Texto Extra 1</label>
                            <input type="text" name="extra_text_1"
                                value="<?php echo htmlspecialchars($content_data['extra_text_1'] ?? ''); ?>">

                            <label>Título Extra 2</label>
                            <input type="text" name="extra_title_2"
                                value="<?php echo htmlspecialchars($content_data['extra_title_2'] ?? ''); ?>">
                            <label>Texto Extra 2</label>
                            <input type="text" name="extra_text_2"
                                value="<?php echo htmlspecialchars($content_data['extra_text_2'] ?? ''); ?>">

                        <?php elseif ($edit_page['template'] == 'start'): ?>
                            <h4>Intro Sección</h4>
                            <label>Título Intro</label>
                            <input type="text" name="intro_title"
                                value="<?php echo htmlspecialchars($content_data['intro_title'] ?? ''); ?>">
                            <label>Texto Intro</label>
                            <textarea name="intro_text"
                                rows="3"><?php echo htmlspecialchars($content_data['intro_text'] ?? ''); ?></textarea>

                            <div class="section-break"></div>
                            <h4>Estadísticas (Hasta 4)</h4>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div style="background: #f1f5f9; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                                    <label>Dato <?php echo $i; ?> (Número/Texto)</label>
                                    <input type="text" name="stat_<?php echo $i; ?>_number"
                                        value="<?php echo htmlspecialchars($content_data["stat_{$i}_number"] ?? ''); ?>">
                                    <label>Etiqueta <?php echo $i; ?></label>
                                    <input type="text" name="stat_<?php echo $i; ?>_label"
                                        value="<?php echo htmlspecialchars($content_data["stat_{$i}_label"] ?? ''); ?>">
                                    <label>Icono (Clase FontAwesome)</label>
                                    <input type="text" name="stat_<?php echo $i; ?>_icon"
                                        value="<?php echo htmlspecialchars($content_data["stat_{$i}_icon"] ?? 'fas fa-star'); ?>">
                                </div>
                            <?php endfor; ?>

                            <div class="section-break"></div>
                            <h4>Testimonios (Hasta 4)</h4>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div style="background: #f1f5f9; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                                    <label>Testimonio <?php echo $i; ?></label>
                                    <textarea name="testimonial_<?php echo $i; ?>_text"
                                        rows="2"><?php echo htmlspecialchars($content_data["testimonial_{$i}_text"] ?? ''); ?></textarea>
                                    <label>Autor <?php echo $i; ?></label>
                                    <input type="text" name="testimonial_<?php echo $i; ?>_author"
                                        value="<?php echo htmlspecialchars($content_data["testimonial_{$i}_author"] ?? ''); ?>">
                                </div>
                            <?php endfor; ?>

                        <?php elseif ($edit_page['template'] == 'gallery'): ?>
                            <h4>Imágenes de Galería</h4>
                            <p>Pega las URLs de las imágenes que quieres mostrar.</p>
                            <?php
                            $imgs = $content_data['images'] ?? [];
                            for ($i = 0; $i < 10; $i++):
                                ?>
                                <label>Imagen <?php echo $i + 1; ?></label>
                                <input type="text" name="image_<?php echo $i; ?>"
                                    value="<?php echo htmlspecialchars($imgs[$i] ?? ''); ?>">
                            <?php endfor; ?>

                        <?php elseif ($edit_page['template'] == 'builder'): ?>
                            <!-- BUILDER EDITOR CONTAINER -->
                            <div id="builder-wrapper">
                                <p>Agrega bloques para construir tu página.</p>

                                <!-- Toolbar -->
                                <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                                    <button type="button" onclick="addBlock('text')" style="background: #e2e8f0; color: #333;">+
                                        Texto</button>
                                    <button type="button" onclick="addBlock('image')"
                                        style="background: #e2e8f0; color: #333;">+ Imagen</button>
                                    <button type="button" onclick="addBlock('columns_2')"
                                        style="background: #e2e8f0; color: #333;">+ 2 Col</button>
                                    <button type="button" onclick="addBlock('cta')" style="background: #e2e8f0; color: #333;">+
                                        Botón CTA</button>
                                </div>

                                <!-- Container where blocks are rendered -->
                                <div id="blocks-container"
                                    style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px;"></div>

                                <!-- Hidden Input to store JSON -->
                                <input type="hidden" name="builder_json" id="builder_json">
                            </div>

                            <script>
                                // Initialize blocks from DB
                                let blocks = <?php echo json_encode($content_data ?? []); ?>;

                                // Render on load
                                document.addEventListener('DOMContentLoaded', renderBlocks);

                                function renderBlocks() {
                                    const container = document.getElementById('blocks-container');
                                    container.innerHTML = '';

                                    blocks.forEach((block, index) => {
                                        let html = `
                                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; position: relative;">
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                    <span style="font-weight: bold; text-transform: uppercase; font-size: 0.8rem; color: #64748b;">${block.type}</span>
                                                    <div>
                                                        <button type="button" onclick="moveBlock(${index}, -1)" style="padding: 2px 8px; font-size: 0.8rem; background: #cbd5e1;">&uarr;</button>
                                                        <button type="button" onclick="moveBlock(${index}, 1)" style="padding: 2px 8px; font-size: 0.8rem; background: #cbd5e1;">&darr;</button>
                                                        <button type="button" onclick="removeBlock(${index})" style="padding: 2px 8px; font-size: 0.8rem; background: #ef4444; color: white;">X</button>
                                                    </div>
                                                </div>
                                        `;

                                        // Render Inputs based on Type
                                        if (block.type === 'text') {
                                            html += `<textarea onchange="updateBlock(${index}, 'content', this.value)" rows="4" style="width:100%">${escapeHtml(block.content || '')}</textarea>`;
                                        }
                                        else if (block.type === 'image') {
                                            html += `
                                                <label>URL Imagen</label>
                                                <input type="text" value="${escapeHtml(block.url || '')}" onchange="updateBlock(${index}, 'url', this.value)">
                                                <label>Caption (Opcional)</label>
                                                <input type="text" value="${escapeHtml(block.caption || '')}" onchange="updateBlock(${index}, 'caption', this.value)">
                                            `;
                                        }
                                        else if (block.type === 'cta') {
                                            html += `
                                                <label>Texto Botón</label>
                                                <input type="text" value="${escapeHtml(block.btn_text || '')}" onchange="updateBlock(${index}, 'btn_text', this.value)">
                                                <label>Enlace URL</label>
                                                <input type="text" value="${escapeHtml(block.url || '')}" onchange="updateBlock(${index}, 'url', this.value)">
                                                <label>Título Sección</label>
                                                <input type="text" value="${escapeHtml(block.title || '')}" onchange="updateBlock(${index}, 'title', this.value)">
                                            `;
                                        }
                                        else if (block.type === 'columns_2') {
                                            html += `
                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                                    <div style="border: 1px dashed #ccc; padding: 10px;">
                                                        <strong>Col 1</strong>
                                                        <select onchange="updateBlock(${index}, 'col1_type', this.value)" style="margin-bottom: 5px;">
                                                            <option value="text" ${block.col1_type === 'text' ? 'selected' : ''}>Texto</option>
                                                            <option value="image" ${block.col1_type === 'image' ? 'selected' : ''}>Imagen</option>
                                                        </select>
                                                        ${block.col1_type === 'image' ?
                                            `<input type="text" placeholder="URL Img" value="${escapeHtml(block.col1_url || '')}" onchange="updateBlock(${index}, 'col1_url', this.value)">` :
                                            `<textarea rows="3" placeholder="Texto" onchange="updateBlock(${index}, 'col1_content', this.value)">${escapeHtml(block.col1_content || '')}</textarea>`
                                        }
                                                    </div>
                                                    <div style="border: 1px dashed #ccc; padding: 10px;">
                                                        <strong>Col 2</strong>
                                                        <select onchange="updateBlock(${index}, 'col2_type', this.value)" style="margin-bottom: 5px;">
                                                            <option value="text" ${block.col2_type === 'text' ? 'selected' : ''}>Texto</option>
                                                            <option value="image" ${block.col2_type === 'image' ? 'selected' : ''}>Imagen</option>
                                                        </select>
                                                        ${block.col2_type === 'image' ?
                                            `<input type="text" placeholder="URL Img" value="${escapeHtml(block.col2_url || '')}" onchange="updateBlock(${index}, 'col2_url', this.value)">` :
                                            `<textarea rows="3" placeholder="Texto" onchange="updateBlock(${index}, 'col2_content', this.value)">${escapeHtml(block.col2_content || '')}</textarea>`
                                        }
                                                    </div>
                                                </div>
                                            `;
                                        }

                                        html += `</div>`;
                                        container.innerHTML += html;
                                    });

                                    // Update hidden input
                                    document.getElementById('builder_json').value = JSON.stringify(blocks);
                                }

                                function addBlock(type) {
                                    let newBlock = { type: type };
                                    if (type === 'columns_2') {
                                        newBlock.col1_type = 'text';
                                        newBlock.col2_type = 'text';
                                    }
                                    blocks.push(newBlock);
                                    renderBlocks();
                                }

                                function removeBlock(index) {
                                    if (confirm('¿Eliminar bloque?')) {
                                        blocks.splice(index, 1);
                                        renderBlocks();
                                    }
                                }

                                function moveBlock(index, direction) {
                                    if (direction === -1 && index > 0) {
                                        [blocks[index], blocks[index - 1]] = [blocks[index - 1], blocks[index]];
                                    } else if (direction === 1 && index < blocks.length - 1) {
                                        [blocks[index], blocks[index + 1]] = [blocks[index + 1], blocks[index]];
                                    }
                                    renderBlocks();
                                }

                                function updateBlock(index, key, value) {
                                    blocks[index][key] = value;
                                    document.getElementById('builder_json').value = JSON.stringify(blocks);
                                }

                                function escapeHtml(text) {
                                    if (!text) return '';
                                    return text
                                        .replace(/&/g, "&amp;")
                                        .replace(/</g, "&lt;")
                                        .replace(/>/g, "&gt;")
                                        .replace(/"/g, "&quot;")
                                        .replace(/'/g, "&#039;");
                                }
                            </script>

                        <?php else: ?>
                            <p>Este template no tiene campos configurables en esta versión.</p>
                        <?php endif; ?>

                        <button type="submit">Actualizar Página</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>