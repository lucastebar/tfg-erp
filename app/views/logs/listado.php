<?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
<div class="page-header">
    <h1>Logs de Auditoría</h1>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<div class="search-box">
    <form method="get" action="<?= $basePath ?>/index.php">
        <input type="hidden" name="controller" value="log">
        <input type="hidden" name="action" value="index">
        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>" placeholder="Fecha inicio">
        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" placeholder="Fecha fin">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="<?= $basePath ?>/index.php?controller=log&action=index" class="btn btn-secondary">Limpiar</a>
    </form>
</div>

<?php if (empty($logs)): ?>
    <div class="empty-state">
        <p>No hay logs registrados</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Tabla</th>
                <th>Detalles</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                    <td><?= htmlspecialchars($log['usuario_nombre'] ?? 'Sistema') ?></td>
                    <td><span class="badge badge-accion"><?= htmlspecialchars($log['accion']) ?></span></td>
                    <td><?= htmlspecialchars($log['tabla_afectada'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($log['detalles'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($log['ip'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<style>
.badge-accion {
    background: #e0f2fe;
    color: #0369a1;
}
</style>