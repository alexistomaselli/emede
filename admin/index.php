<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gráfica Emede</title>
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
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        h1 {
            margin-top: 0;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <a href="index.php" class="logo">Gráfica Emede</a>
        <nav class="menu">
            <a href="/admin/index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="/admin/settings.php"><i class="fas fa-cog"></i> Configuración Global</a>
            <a href="/admin/pages.php"><i class="fas fa-file-alt"></i> Páginas</a>
            <a href="/admin/media.php"><i class="fas fa-images"></i> Medios</a>
            <a href="/admin/appearance.php"><i class="fas fa-paint-brush"></i> Apariencia</a>
            <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Sitio</a>
            <a href="/admin/logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fas fa-sign-out-alt"></i>
                Salir</a>
        </nav>
    </div>
    <div class="content">
        <h1>Bienvenido al Panel de Control</h1>
        <div class="card">
            <p>Desde aquí puedes gestionar el contenido de tu sitio web.</p>
            <ul>
                <li>Usa <strong>Configuración Global</strong> para cambiar el logo, teléfonos, textos del footer y del
                    Hero.</li>
                <li>Usa <strong>Páginas</strong> para editar textos de secciones específicas.</li>
            </ul>
        </div>
    </div>
</body>

</html>