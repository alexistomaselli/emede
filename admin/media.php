<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Upload Configuration
$upload_dir = '../uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$message = '';

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_image'])) {
    $file = $_FILES['new_image'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_type = mime_content_type($file['tmp_name']);

        if (in_array($file_type, $allowed_types)) {
            // Sanitize filename
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
            // Avoid duplicates
            if (file_exists($upload_dir . $filename)) {
                $filename = time() . '_' . $filename;
            }

            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                // Redirect on success
                header("Location: media.php?msg=" . urlencode("Imagen subida: $filename"));
                exit;
            } else {
                $message = "Error al mover el archivo.";
            }
        } else {
            $message = "Tipo de archivo no permitido. Solo JPG, PNG, WEBP, GIF.";
        }
    } else {
        $message = "Error en la subida: " . $file['error'];
    }
}

// Handle Delete (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $file_to_delete = $upload_dir . basename($_POST['delete_file']);
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
        header("Location: media.php?msg=" . urlencode("Archivo eliminado."));
        exit;
    }
}

// Get Message from URL
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

// List Files
$images = glob($upload_dir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Medios - Admin</title>
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
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.2s;
        }

        .upload-area:hover {
            border-color: #4f46e5;
            background: #f1f5f9;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
        }

        .gallery-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }

        .gallery-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        .gallery-actions {
            padding: 10px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .btn-copy {
            background: #e2e8f0;
            border: none;
            padding: 5px 10px;
            font-size: 0.8rem;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-copy:hover {
            background: #cbd5e1;
        }

        .btn-del {
            color: #ef4444;
            text-decoration: none;
            font-size: 0.8rem;
            margin-left: 10px;
        }

        .alert {
            padding: 15px;
            background: #dcfce7;
            color: #166534;
            border-radius: 5px;
            margin-bottom: 20px;
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
            <a href="/admin/media.php" class="active"><i class="fas fa-images"></i> Medios</a>
            <a href="/admin/appearance.php"><i class="fas fa-paint-brush"></i> Apariencia</a>
            <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Sitio</a>
            <a href="/admin/logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fas fa-sign-out-alt"></i>
                Salir</a>
        </nav>
    </div>
    <div class="content">
        <h1>Gestor de Medios</h1>

        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="card">
            <h3>Subir Nueva Imagen</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="new_image" id="file" required accept="image/*" style="display: none;"
                    onchange="this.form.submit()">
                <div class="upload-area" onclick="document.getElementById('file').click()">
                    <i class="fas fa-cloud-upload-alt"
                        style="font-size: 2rem; color: #94a3b8; margin-bottom: 10px;"></i>
                    <p style="margin: 0; color: #64748b;">Haz clic para seleccionar una imagen</p>
                </div>
            </form>
        </div>

        <!-- Gallery -->
        <div class="gallery-grid">
            <?php foreach ($images as $img): ?>
                <?php $url = '/uploads/' . basename($img); ?>
                <div class="gallery-item">
                    <img src="<?php echo $url; ?>" alt="Image">
                    <div class="gallery-actions">
                        <button class="btn-copy" onclick="copyToClipboard('<?php echo $url; ?>', this)">Copiar URL</button>
                        <button class="btn-del" style="background:none; border:none; cursor:pointer;"
                            onclick="openDeleteModal('<?php echo htmlspecialchars(basename($img)); ?>')">Borrar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
        <div
            style="background: white; padding: 25px; border-radius: 8px; width: 90%; max-width: 400px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #f59e0b; margin-bottom: 15px;"></i>
            <h3 style="margin-top: 0; color: #1e293b;">¿Eliminar imagen?</h3>
            <p style="color: #64748b; margin-bottom: 25px;">Esta acción no se puede deshacer.</p>

            <form method="POST" id="deleteForm">
                <input type="hidden" name="delete_file" id="deleteFile">
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="button" onclick="closeModal()"
                        style="padding: 10px 20px; border: 1px solid #cbd5e1; background: white; border-radius: 5px; cursor: pointer; color: #64748b;">Cancelar</button>
                    <button type="submit"
                        style="padding: 10px 20px; border: none; background: #ef4444; color: white; border-radius: 5px; cursor: pointer;">Sí,
                        Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(filename) {
            document.getElementById('deleteModal').style.display = 'flex';
            document.getElementById('deleteFile').value = filename;
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close on click outside
        document.getElementById('deleteModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('deleteModal')) {
                closeModal();
            }
        });

        function copyToClipboard(text, btnElement) {
            navigator.clipboard.writeText(text).then(() => {
                // Remove existing tooltips
                const existing = document.querySelectorAll('.copy-tooltip');
                existing.forEach(e => e.remove());

                // Create tooltip
                const tooltip = document.createElement('span');
                tooltip.className = 'copy-tooltip';
                tooltip.innerText = '¡Copiado!';
                tooltip.style.position = 'absolute';
                tooltip.style.background = '#333';
                tooltip.style.color = '#fff';
                tooltip.style.padding = '5px 10px';
                tooltip.style.borderRadius = '4px';
                tooltip.style.fontSize = '12px';
                tooltip.style.top = '-30px';
                tooltip.style.left = '50%';
                tooltip.style.transform = 'translateX(-50%)';
                tooltip.style.zIndex = '1000';
                tooltip.style.opacity = '0';
                tooltip.style.transition = 'opacity 0.3s';

                // Position relative to button
                btnElement.style.position = 'relative';
                btnElement.appendChild(tooltip);

                // Show
                setTimeout(() => tooltip.style.opacity = '1', 10);

                // Hide and remove
                setTimeout(() => {
                    tooltip.style.opacity = '0';
                    setTimeout(() => tooltip.remove(), 300);
                }, 2000);

            }, (err) => {
                console.error('Error al copiar: ', err);
            });
        }
    </script>
</body>

</html>