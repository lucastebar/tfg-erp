<?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
<div class="page-header">
    <h1>Factura <?= htmlspecialchars($factura['numero_factura']) ?></h1>
    <div>
        <a href="<?= $basePath ?>/index.php?controller=factura&action=index" class="btn">Volver</a>
        <a href="#imprimir" class="btn btn-primary" onclick="window.print()">Imprimir / PDF</a>
    </div>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<div class="factura-detalle">
    <div class="factura-header">
        <div class="empresa">
            <h2>TFG ERP</h2>
            <p>CIF: X12345678</p>
            <p>Dirección de la Empresa</p>
        </div>
        <div class="cliente">
            <h3>Cliente</h3>
            <p><strong><?= htmlspecialchars($factura['razon_social']) ?></strong></p>
            <p>NIF: <?= htmlspecialchars($factura['nif']) ?></p>
            <p><?= htmlspecialchars($factura['direccion'] ?? '') ?></p>
            <p><?= htmlspecialchars($factura['cp'] ?? '') ?> <?= htmlspecialchars($factura['ciudad'] ?? '') ?></p>
        </div>
        <div class="factura-info">
            <p><strong>Nº Factura:</strong> <?= htmlspecialchars($factura['numero_factura']) ?></p>
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura['fecha'])) ?></p>
            <p><strong>Estado:</strong> <span class="badge badge-<?= $factura['estado'] ?>"><?= ucfirst($factura['estado']) ?></span></p>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $detalle): ?>
                <tr>
                    <td><?= htmlspecialchars($detalle['codigo']) ?></td>
                    <td><?= htmlspecialchars($detalle['nombre']) ?></td>
                    <td><?= $detalle['cantidad'] ?></td>
                    <td><?= number_format($detalle['precio_unitario'], 2, ',', '.') ?> €</td>
                    <td><?= number_format($detalle['cantidad'] * $detalle['precio_unitario'], 2, ',', '.') ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totales">
        <div class="total-line">
            <span>Base Imponible:</span>
            <span><?= number_format($factura['base_imponible'], 2, ',', '.') ?> €</span>
        </div>
        <div class="total-line">
            <span>IVA (21%):</span>
            <span><?= number_format($factura['iva'], 2, ',', '.') ?> €</span>
        </div>
        <?php if ($factura['descuento'] > 0): ?>
        <div class="total-line">
            <span>Descuento (<?= $factura['descuento'] ?>%):</span>
            <span>-<?= number_format($factura['base_imponible'] * ($factura['descuento'] / 100), 2, ',', '.') ?> €</span>
        </div>
        <?php endif; ?>
        <div class="total-line total-final">
            <span>Total:</span>
            <span><?= number_format($factura['total'], 2, ',', '.') ?> €</span>
        </div>
    </div>

    <?php if ($factura['estado'] === 'pendiente'): ?>
    <div class="factura-acciones">
        <a href="<?= $basePath ?>/index.php?controller=factura&action=marcarPagada&id=<?= $factura['id'] ?>" class="btn btn-primary" onclick="return confirm('¿Marcar como pagada?')">Marcar como Pagada</a>
        <a href="<?= $basePath ?>/index.php?controller=factura&action=cancelar&id=<?= $factura['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Cancelar factura? Se devolverá el stock.')">Cancelar Factura</a>
    </div>
    <?php endif; ?>
</div>

<style>
.factura-detalle {
    background: var(--white);
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    max-width: 800px;
    margin: 0 auto;
}
.factura-header {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--primary);
}
.factura-header h2, .factura-header h3 {
    margin-bottom: 0.5rem;
}
.empresa {
    color: var(--primary);
}
.totales {
    max-width: 300px;
    margin-left: auto;
    margin-top: 1rem;
}
.total-line {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
}
.total-final {
    border-top: 2px solid var(--primary);
    font-weight: 600;
    font-size: 1.125rem;
}
.factura-acciones {
    margin-top: 1.5rem;
    display: flex;
    gap: 1rem;
}
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
@media print {
    .page-header, .factura-acciones, .search-box {
        display: none;
    }
}
</style>