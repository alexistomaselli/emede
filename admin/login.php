<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    // Simple hardcoded password for now: 'admin'
    // In a real scenario, this should be in the DB hashed.
    if ($password === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Contraseña incorrecta";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Admin - Gráfica Emede</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f1f5f9;
            margin: 0;
        }

        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            margin-top: 0;
            text-align: center;
            color: #0f172a;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #4338ca;
        }

        .error {
            color: #ef4444;
            text-align: center;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Admin Panel</h2>
        <?php if (isset($error))
            echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Contraseña (admin)" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>

</html>