<div class="page-header">
    <h1>Facturas</h1>
    <a href="index.php?controller=factura&action=nuevo" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nueva Factura
    </a>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<div class="search-box">
    <form method="get" action="index.php">
        <input type="hidden" name="controller" value="factura">
        <input type="hidden" name="action" value="index">
        <input type="text" name="busqueda" placeholder="Buscar por número o cliente..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if ($busqueda): ?>
            <a href="index.php?controller=factura&action=index" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($facturas)): ?>
    <div class="empty-state">
        <p>No hay facturas registradas</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Base</th>
                <th>IVA</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facturas as $factura): ?>
                <tr>
                    <td><?= htmlspecialchars($factura['numero_factura']) ?></td>
                    <td><?= htmlspecialchars($factura['razon_social']) ?></td>
                    <td><?= date('d/m/Y', strtotime($factura['fecha'])) ?></td>
                    <td><?= number_format($factura['base_imponible'], 2, ',', '.') ?> €</td>
                    <td><?= number_format($factura['iva'], 2, ',', '.') ?> €</td>
                    <td><?= number_format($factura['total'], 2, ',', '.') ?> €</td>
                    <td>
                        <span class="badge badge-<?= $factura['estado'] ?>"><?= ucfirst($factura['estado']) ?></span>
                    </td>
                    <td class="actions">
                        <a href="index.php?controller=factura&action=ver&id=<?= $factura['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
                        <?php if ($factura['estado'] === 'pendiente'): ?>
                            <a href="index.php?controller=factura&action=marcarPagada&id=<?= $factura['id'] ?>" class="btn btn-sm btn-primary" onclick="return confirm('¿Marcar como pagada?')">Pagar</a>
                        <?php endif; ?>
                        <?php if ($factura['estado'] !== 'cancelada'): ?>
                            <a href="index.php?controller=factura&action=cancelar&id=<?= $factura['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar factura? Se devolverá el stock.')">Cancelar</a>
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
.badge-pendiente {
    background: #fef3c7;
    color: #92400e;
}
.badge-pagada {
    background: #dcfce7;
    color: #166534;
}
.badge-cancelada {
    background: #fef2f2;
    color: #991b1b;
}
</style>