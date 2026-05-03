<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'TFG ERP' ?></title>
    <link rel="stylesheet" href="/tarde/tfg-erp/public/css/style.css">
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <div class="header-logo">TFG ERP</div>
            <nav class="header-nav">
                <a href="index.php?controller=main&action=index">Panel</a>
                <a href="index.php?controller=cliente&action=index">Clientes</a>
                <a href="index.php?controller=auth&action=perfil">Mi Perfil</a>
                <a href="index.php?controller=auth&action=logout">Cerrar Sesión</a>
            </nav>
            <div class="header-user">
                <span><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></span>
                <span class="user-badge"><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></span>
            </div>
        </header>

        <main class="app-main">
            <?= $content ?? '' ?>
        </main>
    </div>
</body>
</html>