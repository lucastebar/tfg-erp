<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'TFG ERP' ?></title>
    <?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
    <link rel="stylesheet" href="<?= $basePath ?>/public/css/style.css">
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <button type="button" class="sidebar-toggle" aria-label="Menú" onclick="document.querySelector('.app-body').classList.toggle('sidebar-collapsed')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <div class="header-logo">TFG ERP</div>
            <div class="header-user">
                <span><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></span>
                <span class="user-badge"><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></span>
            </div>
        </header>
        
        <div class="app-body">
            <aside class="app-sidebar">
                <nav class="sidebar-nav">
                    <a href="<?= $basePath ?>/index.php?controller=main&action=index" class="sidebar-link <?= ($_GET['controller'] ?? '') === 'main' ? 'active' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        Panel
                    </a>
                    <a href="<?= $basePath ?>/index.php?controller=cliente&action=index" class="sidebar-link <?= ($_GET['controller'] ?? '') === 'cliente' ? 'active' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        Clientes
                    </a>
                    <a href="<?= $basePath ?>/index.php?controller=producto&action=index" class="sidebar-link <?= ($_GET['controller'] ?? '') === 'producto' ? 'active' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        Inventario
                    </a>
                    <a href="<?= $basePath ?>/index.php?controller=factura&action=index" class="sidebar-link <?= ($_GET['controller'] ?? '') === 'factura' ? 'active' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Facturas
                    </a>
                    
                    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                    <div class="sidebar-section">
                        <span class="sidebar-section-title">Administración</span>
                        <a href="<?= $basePath ?>/index.php?controller=usuario&action=index" class="sidebar-link <?= ($_GET['controller'] ?? '') === 'usuario' ? 'active' : '' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                            Usuarios
                        </a>
                        <a href="<?= $basePath ?>/index.php?controller=log&action=index" class="sidebar-link <?= ($_GET['controller'] ?? '') === 'log' ? 'active' : '' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                            Logs
                        </a>
                    </div>
                    <?php endif; ?>
                </nav>
                <div class="sidebar-footer">
                    <a href="<?= $basePath ?>/index.php?controller=auth&action=logout" class="sidebar-link sidebar-link-logout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Cerrar Sesión
                    </a>
                </div>
            </aside>
            
            <main class="app-main">
                <?= $content ?? '' ?>
            </main>
        </div>
        
        <footer class="app-footer">
            <span>TFG ERP</span>
            <span>&copy; <?= date('Y') ?></span>
        </footer>
    </div>
    
    <script>
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && 
                !e.target.closest('.app-sidebar') && 
                !e.target.closest('.sidebar-toggle')) {
                document.querySelector('.app-body').classList.remove('sidebar-collapsed');
            }
        });
    </script>
</body>
</html>