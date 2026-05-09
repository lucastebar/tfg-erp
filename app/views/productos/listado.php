<?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
<div class="page-header">
    <h1>Gestión de Productos</h1>
    <a href="<?= $basePath ?>/index.php?controller=producto&action=nuevo" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nuevo Producto
    </a>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<div class="search-box">
    <form method="get" action="<?= $basePath ?>/index.php">
        <input type="hidden" name="controller" value="producto">
        <input type="hidden" name="action" value="index">
        <input type="text" name="busqueda" placeholder="Buscar por Código o Nombre..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if ($busqueda): ?>
            <a href="<?= $basePath ?>/index.php?controller=producto&action=index" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($productos)): ?>
    <div class="empty-state">
        <p>No hay productos registrados</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>PVP</th>
                <th>Stock</th>
                <th>Mín.</th>
                <th>IVA</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr class="<?= ($producto['stock_actual'] <= $producto['stock_minimo']) ? 'row-warning' : '' ?>">
                    <td><?= htmlspecialchars($producto['codigo']) ?></td>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td><?= number_format($producto['pvp'], 2, ',', '.') ?> €</td>
                    <td><?= $producto['stock_actual'] ?></td>
                    <td><?= $producto['stock_minimo'] ?></td>
                    <td><?= $producto['tipo_iva'] ?>%</td>
                    <td class="actions">
                        <a href="<?= $basePath ?>/index.php?controller=producto&action=editar&id=<?= $producto['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                        <a href="<?= $basePath ?>/index.php?controller=producto&action=eliminar&id=<?= $producto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>