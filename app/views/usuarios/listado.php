<?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
<div class="page-header">
    <h1>Gestión de Usuarios</h1>
    <a href="<?= $basePath ?>/index.php?controller=usuario&action=nuevo" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nuevo Usuario
    </a>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<?php if (empty($usuarios)): ?>
    <div class="empty-state">
        <p>No hay usuarios registrados</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Último Acceso</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td>
                        <span class="badge badge-<?= $usuario['rol'] ?>"><?= ucfirst($usuario['rol']) ?></span>
                    </td>
                    <td><?= $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : '-' ?></td>
                    <td>
                        <span class="badge badge-<?= $usuario['activo'] ? 'activo' : 'inactivo' ?>">
                            <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="<?= $basePath ?>/index.php?controller=usuario&action=editar&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                        <?php if ($usuario['activo']): ?>
                            <a href="<?= $basePath ?>/index.php?controller=usuario&action=eliminar&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Desactivar este usuario?')">Desactivar</a>
                        <?php else: ?>
                            <a href="<?= $basePath ?>/index.php?controller=usuario&action=activar&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-primary" onclick="return confirm('¿Activar este usuario?')">Activar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<style>
.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}
.badge-admin {
    background: #dbeafe;
    color: #1e40af;
}
.badge-operario {
    background: #f3f4f6;
    color: #374151;
}
.badge-activo {
    background: #dcfce7;
    color: #166534;
}
.badge-inactivo {
    background: #fef2f2;
    color: #991b1b;
}
</style>