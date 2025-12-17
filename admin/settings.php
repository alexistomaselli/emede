<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed_keys = ['site_title', 'logo_text', 'phone', 'email', 'address', 'hero_title', 'hero_desc', 'hero_cta'];

    foreach ($allowed_keys as $key) {
        if (isset($_POST[$key])) {
            set_setting($key, $_POST[$key]);
        }
    }
    $message = "Configuración guardada correctamente.";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Configuración - Admin</title>
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
            max-width: 800px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #475569;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            font-family: inherit;
        }

        button {
            padding: 12px 25px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
        }

        button:hover {
            background: #4338ca;
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
            <a href="/admin/settings.php" class="active"><i class="fas fa-cog"></i> Configuración Global</a>
            <a href="/admin/pages.php"><i class="fas fa-file-alt"></i> Páginas</a>
            <a href="/admin/media.php"><i class="fas fa-images"></i> Medios</a>
            <a href="/admin/appearance.php"><i class="fas fa-paint-brush"></i> Apariencia</a>
            <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Sitio</a>
            <a href="/admin/logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fas fa-sign-out-alt"></i>
                Salir</a>
        </nav>
    </div>
    <div class="content">
        <h1>Configuración Global</h1>
        <?php if ($message)
            echo "<div class='alert'>$message</div>"; ?>

        <div class="card">
            <form method="POST">
                <h3>Información Básica</h3>
                <div class="form-group">
                    <label>Nombre del Sitio</label>
                    <input type="text" name="site_title"
                        value="<?php echo htmlspecialchars(get_setting('site_title')); ?>">
                </div>
                <div class="form-group">
                    <label>Logo (Texto)</label>
                    <input type="text" name="logo_text"
                        value="<?php echo htmlspecialchars(get_setting('logo_text')); ?>">
                </div>

                <h3>Contacto</h3>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars(get_setting('phone')); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" value="<?php echo htmlspecialchars(get_setting('email')); ?>">
                </div>
                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars(get_setting('address')); ?>">
                </div>

                <h3>Sección Hero (Inicio)</h3>
                <div class="form-group">
                    <label>Título Principal (Admite HTML)</label>
                    <input type="text" name="hero_title"
                        value="<?php echo htmlspecialchars(get_setting('hero_title')); ?>">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="hero_desc"
                        rows="3"><?php echo htmlspecialchars(get_setting('hero_desc')); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Texto Botón CTA</label>
                    <input type="text" name="hero_cta" value="<?php echo htmlspecialchars(get_setting('hero_cta')); ?>">
                </div>

                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>

</html>