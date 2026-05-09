<?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
<div class="dashboard">
    <div class="welcome-section">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
        <p class="welcome-subtitle">Qué deseas gestionar hoy?</p>
    </div>
    
    <?php if (!empty($productosBajoStock)): ?>
    <div class="alert alert-warning">
        <strong>Alerta de stock:</strong> Los siguientes productos están por debajo del stock mínimo:
        <ul>
            <?php foreach ($productosBajoStock as $p): ?>
                <li><?php echo htmlspecialchars($p['nombre']); ?> - Stock: <?php echo $p['stock_actual']; ?> / Mín: <?php echo $p['stock_minimo']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="dashboard-cards">
        <a href="<?= $basePath ?>/index.php?controller=cliente&action=index" class="dashboard-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <h3>Clientes</h3>
            <p>Gestionar clientes de la empresa</p>
        </a>
        <a href="<?= $basePath ?>/index.php?controller=producto&action=index" class="dashboard-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <h3>Stock</h3>
            <p>Gestionar inventario de productos</p>
        </a>
        <a href="<?= $basePath ?>/index.php?controller=factura&action=index" class="dashboard-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
            <h3>Facturas</h3>
            <p>Gestionar facturación</p>
        </a>
    </div>
</div>