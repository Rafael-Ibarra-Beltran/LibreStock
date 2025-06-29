<?php
if (file_exists(dirname(__DIR__) . '/src/config.php')) {
    require_once dirname(__DIR__) . '/src/config.php';
} else { 
    die("Error crítico: No se pudo cargar el archivo de configuración.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LibreStock - Tu librería favorita en Tijuana, Baja California. Amplio catálogo de libros en español e inglés.">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'LibreStock - Librería en Tijuana'; ?></title>
    
    <link rel="icon" type="image/x-icon" href="<?php echo PROJECT_BASE_URL; ?>images/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo PROJECT_BASE_URL; ?>images/favicon.ico">
    
    <link rel="stylesheet" href="<?php echo PROJECT_BASE_URL; ?>css/style.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a class="navbar-brand" href="<?php echo PROJECT_BASE_URL; ?>">
                    <img src="<?php echo PROJECT_BASE_URL; ?>images/logo.png" alt="Logo LibreStock" class="navbar-logo">
                    LibreStock
                </a>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo PROJECT_BASE_URL; ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo PROJECT_BASE_URL; ?>products.php">Libros</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo PROJECT_BASE_URL; ?>categories.php">Categorías</a></li>
                    <?php if (isset($_SESSION['user_id'])):
                        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?php echo PROJECT_BASE_URL; ?>users.php">Usuarios</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo PROJECT_BASE_URL; ?>logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo PROJECT_BASE_URL; ?>login.php">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo PROJECT_BASE_URL; ?>register.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container content-wrapper">