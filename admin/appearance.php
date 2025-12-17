<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';
$db = getDB();

$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        /* --- SAVE MENU --- */
        if ($_POST['action'] === 'save_menu') {
            $menu_items = $_POST['menu_items'] ?? '[]'; // JSON string
            set_setting('site_menu', $menu_items);
            $message = "Menú actualizado correctamente.";
        }
        /* --- SAVE HEADER --- */ elseif ($_POST['action'] === 'save_header') {
            $header_config = [
                'layout' => $_POST['header_layout'] ?? 'logo_left',
                'show_social' => isset($_POST['show_social']),
                'show_search' => isset($_POST['show_search'])
            ];
            set_setting('site_header', json_encode($header_config));
            $message = "Cabecera actualizada.";
        }
        /* --- SAVE FOOTER --- */ elseif ($_POST['action'] === 'save_footer') {
            $footer_config = [
                'columns' => (int) $_POST['footer_columns'],
                'show_logo' => isset($_POST['footer_show_logo']),
                'col1' => $_POST['col1'] ?? '',
                'col2' => $_POST['col2'] ?? '',
                'col3' => $_POST['col3'] ?? '',
                'col4' => $_POST['col4'] ?? ''
            ];
            set_setting('site_footer', json_encode($footer_config));
            $message = "Pie de página actualizado.";
        }
    }
}

// Fetch Current Settings
$current_menu = get_setting('site_menu') ?: '[]';
$current_header = json_decode(get_setting('site_header') ?: '{"layout":"logo_left"}', true);
$current_footer = json_decode(get_setting('site_footer') ?: '{"columns":3}', true);

// Fetch Pages for Menu Builder
$stmt = $db->query("SELECT id, title, slug FROM pages ORDER BY title ASC");
$all_pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Apariencia - Admin</title>
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
        }

        .content {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
            max-width: 1000px;
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
            margin-bottom: 20px;
        }

        h3 {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #1e293b;
        }

        .alert {
            padding: 15px;
            background: #dcfce7;
            color: #166534;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* TABS */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            color: #64748b;
            font-weight: 600;
        }

        .tab.active {
            border-bottom-color: #3b82f6;
            color: #3b82f6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* FORMS */
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #475569;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button.btn-primary {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        button.btn-primary:hover {
            background: #2563eb;
        }

        /* MENU BUILDER */
        .menu-builder-container {
            display: flex;
            gap: 20px;
        }

        .menu-source,
        .menu-target {
            flex: 1;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            background: #f8fafc;
        }

        .menu-item {
            background: white;
            border: 1px solid #cbd5e1;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
        }

        .menu-item .actions i {
            cursor: pointer;
            color: #94a3b8;
            margin-left: 5px;
        }

        .menu-item .actions i:hover {
            color: #ef4444;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <a href="index.php" class="logo">Gráfica Emede</a>
        <nav class="menu">
            <a href="/admin/index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="/admin/settings.php"><i class="fas fa-cog"></i> Configuración Global</a>
            <a href="/admin/pages.php"><i class="fas fa-file-alt"></i> Páginas</a>
            <a href="/admin/media.php"><i class="fas fa-images"></i> Medios</a>
            <a href="/admin/appearance.php" class="active"><i class="fas fa-paint-brush"></i> Apariencia</a>
            <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Sitio</a>
            <a href="/admin/logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fas fa-sign-out-alt"></i>
                Salir</a>
        </nav>
    </div>

    <div class="content">
        <h1>Apariencia del Sitio</h1>

        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="tabs">
            <div class="tab active" onclick="showTab('menu')">Menú</div>
            <div class="tab" onclick="showTab('header')">Cabecera</div>
            <div class="tab" onclick="showTab('footer')">Pie de Página</div>
        </div>

        <!-- ================= MENU TAB ================= -->
        <div id="menu" class="tab-content active">
            <div class="card">
                <h3>Gestor de Menú</h3>
                <div class="menu-builder-container">
                    <!-- Source: Pages & Custom Links -->
                    <div class="menu-source">
                        <h4>Agregar Elementos</h4>
                        <div style="margin-bottom: 20px;">
                            <label>Páginas del Sitio</label>
                            <select id="pageSelect">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($all_pages as $p): ?>
                                    <option value="<?php echo $p['slug']; ?>"
                                        data-title="<?php echo htmlspecialchars($p['title']); ?>">
                                        <?php echo htmlspecialchars($p['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn-primary" style="width: 100%; margin-top: 5px;"
                                onclick="addPageToMenu()">Agregar Página</button>
                        </div>
                        <hr>
                        <div>
                            <label>Enlace Personalizado</label>
                            <input type="text" id="customLinkTitle" placeholder="Texto del enlace (ej: Google)">
                            <input type="text" id="customLinkUrl" placeholder="URL (ej: https://google.com)">
                            <button type="button" class="btn-primary" style="width: 100%;"
                                onclick="addCustomLink()">Agregar Enlace</button>
                        </div>
                    </div>

                    <!-- Target: Current Menu -->
                    <div class="menu-target">
                        <h4>Estructura del Menú</h4>
                        <div id="menuList">
                            <!-- JS renders items here -->
                        </div>
                    </div>
                </div>

                <form method="POST" style="margin-top: 20px; text-align: right;">
                    <input type="hidden" name="action" value="save_menu">
                    <input type="hidden" name="menu_items" id="menuItemsInput">
                    <button type="submit" class="btn-primary" onclick="prepareMenuSave()"><i class="fas fa-save"></i>
                        Guardar Menú</button>
                </form>
            </div>
        </div>

        <!-- ================= HEADER TAB ================= -->
        <div id="header" class="tab-content">
            <div class="card">
                <h3>Configuración de Cabecera</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="save_header">

                    <label>Diseño</label>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: normal; margin-bottom: 10px; display: block;">
                            <input type="radio" name="header_layout" value="logo_left" <?php echo ($current_header['layout'] ?? '') === 'logo_left' ? 'checked' : ''; ?>>
                            Logo Izquierda - Menú Derecha (Clásico)
                        </label>
                        <label style="font-weight: normal; display: block;">
                            <input type="radio" name="header_layout" value="logo_center" <?php echo ($current_header['layout'] ?? '') === 'logo_center' ? 'checked' : ''; ?>>
                            Logo Centrado - Menú Debajo
                        </label>
                    </div>

                    <label
                        style="display:flex; align-items:center; gap: 10px; font-weight: normal; margin-bottom: 10px;">
                        <input type="checkbox" name="show_social" <?php echo !empty($current_header['show_social']) ? 'checked' : ''; ?>>
                        Mostrar Iconos Sociales
                    </label>

                    <label
                        style="display:flex; align-items:center; gap: 10px; font-weight: normal; margin-bottom: 20px;">
                        <input type="checkbox" name="show_search" <?php echo !empty($current_header['show_search']) ? 'checked' : ''; ?>>
                        Mostrar Icono de Búsqueda
                    </label>

                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar Cabecera</button>
                </form>
            </div>
        </div>

        <!-- ================= FOOTER TAB ================= -->
        <div id="footer" class="tab-content">
            <div class="card">
                <h3>Configuración de Pie de Página</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="save_footer">

                    <label
                        style="display:flex; align-items:center; gap: 10px; font-weight: normal; margin-bottom: 20px;">
                        <input type="checkbox" name="footer_show_logo" <?php echo !empty($current_footer['show_logo']) ? 'checked' : ''; ?>>
                        Mostrar Logo del Sitio en Columna 1
                    </label>

                    <label>Columnas</label>
                    <select name="footer_columns" id="footerCols" onchange="updateFooterCols()">
                        <option value="1" <?php echo ($current_footer['columns'] == 1) ? 'selected' : ''; ?>>1 Columna
                        </option>
                        <option value="2" <?php echo ($current_footer['columns'] == 2) ? 'selected' : ''; ?>>2 Columnas
                        </option>
                        <option value="3" <?php echo ($current_footer['columns'] == 3) ? 'selected' : ''; ?>>3 Columnas
                        </option>
                        <option value="4" <?php echo ($current_footer['columns'] == 4) ? 'selected' : ''; ?>>4 Columnas
                        </option>
                    </select>

                    <!-- Footer Content Helper -->
                    <div
                        style="background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #cbd5e1;">
                        <h4 style="margin-top:0; margin-bottom:10px; color:#334155;"><i class="fas fa-magic"></i>
                            Asistente de Contenido</h4>
                        <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 10px;">Selecciona una página y
                            agrégala al editor donde tengas el cursor.</p>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <select id="footerPageSelect" style="max-width: 250px; margin-bottom:0;">
                                <option value="">-- Seleccionar Página --</option>
                                <?php foreach ($all_pages as $p): ?>
                                    <option value="<?php echo $p['slug']; ?>"
                                        data-title="<?php echo htmlspecialchars($p['title']); ?>">
                                        <?php echo htmlspecialchars($p['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn-primary" onclick="insertFooterLink('li')"><i
                                    class="fas fa-list"></i> Agregar como Item de Lista</button>
                            <button type="button" class="btn-primary" style="background: #64748b;"
                                onclick="insertFooterLink('a')"><i class="fas fa-link"></i> Solo Link</button>
                        </div>
                        <hr style="border-color: #cbd5e1; margin: 15px 0;">
                        <div style="display: flex; gap: 10px;">
                            <button type="button" class="btn-primary"
                                style="background: #64748b; font-size: 0.8rem; padding: 5px 10px;"
                                onclick="insertTag('<ul>\n  <li>Link aquí...</li>\n</ul>')">Insertar Lista (UL)</button>
                            <button type="button" class="btn-primary"
                                style="background: #64748b; font-size: 0.8rem; padding: 5px 10px;"
                                onclick="insertTag('<h3>Q Título...</h3>')">Insertar Título (H3)</button>
                        </div>
                    </div>

                    <div id="footerEditors">
                        <div class="col-editor" id="editor1">
                            <label>Columna 1</label>
                            <textarea name="col1" rows="8"
                                onfocus="lastFocusedFooter=this"><?php echo htmlspecialchars($current_footer['col1'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-editor" id="editor2">
                            <label>Columna 2</label>
                            <textarea name="col2" rows="8"
                                onfocus="lastFocusedFooter=this"><?php echo htmlspecialchars($current_footer['col2'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-editor" id="editor3">
                            <label>Columna 3</label>
                            <textarea name="col3" rows="8"
                                onfocus="lastFocusedFooter=this"><?php echo htmlspecialchars($current_footer['col3'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-editor" id="editor4">
                            <label>Columna 4</label>
                            <textarea name="col4" rows="8"
                                onfocus="lastFocusedFooter=this"><?php echo htmlspecialchars($current_footer['col4'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar Pie de Página</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // TABS
        function showTab(tabId) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        // FOOTER COLS
        function updateFooterCols() {
            const cols = parseInt(document.getElementById('footerCols').value);
            for (let i = 1; i <= 4; i++) {
                document.getElementById('editor' + i).style.display = (i <= cols) ? 'block' : 'none';
            }
        }
        updateFooterCols(); // Init

        // MENU BUILDER (Simple JS)
        let menuItems = <?php echo $current_menu; ?>;

        function renderMenu() {
            const list = document.getElementById('menuList');
            list.innerHTML = '';
            menuItems.forEach((item, index) => {
                const el = document.createElement('div');
                el.className = 'menu-item';
                el.innerHTML = `
                    <span><b>${item.label}</b> <small style='color:#94a3b8'>(${item.type})</small></span>
                    <div class="actions">
                        ${index > 0 ? `<i class="fas fa-arrow-up" onclick="moveItem(${index}, -1)"></i>` : ''}
                        ${index < menuItems.length - 1 ? `<i class="fas fa-arrow-down" onclick="moveItem(${index}, 1)"></i>` : ''}
                        <i class="fas fa-trash" onclick="removeItem(${index})"></i>
                    </div>
                `;
                list.appendChild(el);
            });
        }

        function addPageToMenu() {
            const select = document.getElementById('pageSelect');
            if (!select.value) return;
            const option = select.options[select.selectedIndex];
            menuItems.push({
                type: 'page',
                label: option.getAttribute('data-title'),
                url: '?p=' + select.value,
                slug: select.value
            });
            renderMenu();
        }

        function addCustomLink() {
            const label = document.getElementById('customLinkTitle').value;
            const url = document.getElementById('customLinkUrl').value;
            if (!label || !url) return;
            menuItems.push({
                type: 'link',
                label: label,
                url: url
            });
            document.getElementById('customLinkTitle').value = '';
            document.getElementById('customLinkUrl').value = '';
            renderMenu();
        }

        function moveItem(index, direction) {
            const temp = menuItems[index];
            menuItems[index] = menuItems[index + direction];
            menuItems[index + direction] = temp;
            renderMenu();
        }

        function removeItem(index) {
            if (confirm('¿Quitar del menú?')) {
                menuItems.splice(index, 1);
                renderMenu();
            }
        }

        function prepareMenuSave() {
            document.getElementById('menuItemsInput').value = JSON.stringify(menuItems);
        }

        // Initialize Menu
        renderMenu();

        // FOOTER HELPER Logic
        let lastFocusedFooter = null;

        // Auto-focus first editor if none selected
        window.addEventListener('load', () => {
            const firstEd = document.querySelector('#footerEditors textarea');
            if (firstEd) lastFocusedFooter = firstEd;
        });

        function insertTag(content) {
            if (!lastFocusedFooter) { alert('Haz clic en una columna primero.'); return; }
            insertAtCursor(lastFocusedFooter, content);
        }

        function insertFooterLink(type) {
            if (!lastFocusedFooter) { alert('Haz clic en una columna primero.'); return; }

            const select = document.getElementById('footerPageSelect');
            if (!select.value) { alert('Selecciona una página de la lista.'); return; }

            const slug = select.value;
            const title = select.options[select.selectedIndex].getAttribute('data-title');

            let html = '';
            if (type === 'li') {
                html = `<li><a href="?p=${slug}">${title}</a></li>\n`;
            } else {
                html = `<a href="?p=${slug}">${title}</a>`;
            }

            insertAtCursor(lastFocusedFooter, html);
        }

        function insertAtCursor(myField, myValue) {
            //IE support
            if (document.selection) {
                myField.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
            }
            //MOZILLA and others
            else if (myField.selectionStart || myField.selectionStart == '0') {
                var startPos = myField.selectionStart;
                var endPos = myField.selectionEnd;
                myField.value = myField.value.substring(0, startPos)
                    + myValue
                    + myField.value.substring(endPos, myField.value.length);

                // Move cursor to end of inserted text
                myField.selectionStart = startPos + myValue.length;
                myField.selectionEnd = startPos + myValue.length;
                myField.focus();
            } else {
                myField.value += myValue;
            }
        }
    </script>
</body>

</html>