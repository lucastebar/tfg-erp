<?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
<div class="page-header">
    <h1>Gestión de Clientes</h1>
    <a href="<?= $basePath ?>/index.php?controller=cliente&action=nuevo" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nuevo Cliente
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
        <input type="hidden" name="controller" value="cliente">
        <input type="hidden" name="action" value="index">
        <input type="text" name="busqueda" placeholder="Buscar por Razón Social, NIF o Email..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if ($busqueda): ?>
            <a href="<?= $basePath ?>/index.php?controller=cliente&action=index" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($clientes)): ?>
    <div class="empty-state">
        <p>No hay clientes registrados</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Razón Social</th>
                <th>NIF</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Ciudad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= htmlspecialchars($cliente['razon_social']) ?></td>
                    <td><?= htmlspecialchars($cliente['nif']) ?></td>
                    <td><?= htmlspecialchars($cliente['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($cliente['telefono'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($cliente['ciudad'] ?? '-') ?></td>
                    <td class="actions">
                        <a href="<?= $basePath ?>/index.php?controller=cliente&action=editar&id=<?= $cliente['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                        <a href="<?= $basePath ?>/index.php?controller=cliente&action=eliminar&id=<?= $cliente['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este cliente?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>