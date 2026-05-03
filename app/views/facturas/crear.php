<div class="page-header">
    <h1>Nueva Factura</h1>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<form method="post" action="index.php?controller=factura&action=crear" class="form" id="form-factura">
    <div class="form-group">
        <label for="cliente_id">Cliente *</label>
        <select id="cliente_id" name="cliente_id" required>
            <option value="">Seleccionar cliente...</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['razon_social']) ?> (<?= htmlspecialchars($cliente['nif']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="descuento">Descuento (%)</label>
        <input type="number" id="descuento" name="descuento" value="0" min="0" max="100" step="0.1">
    </div>

    <div class="form-group">
        <label>Productos</label>
        <div id="lineas-producto">
            <div class="linea-producto" data-index="0">
                <select name="lineas[0][producto_id]" class="producto-select" required>
                    <option value="">Seleccionar producto...</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>" data-stock="<?= $producto['stock_actual'] ?>" data-precio="<?= $producto['pvp'] ?>">
                            <?= htmlspecialchars($producto['nombre']) ?> - Stock: <?= $producto['stock_actual'] ?> - <?= number_format($producto['pvp'], 2, ',', '.') ?> €
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="lineas[0][cantidad]" class="cantidad-input" value="1" min="1" placeholder="Cantidad">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarLinea(this)">X</button>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="agregarLinea()">+ Añadir producto</button>
    </div>

    <div class="totales-box">
        <div class="total-line">
            <span>Base Imponible:</span>
            <span id="base-imponible">0.00 €</span>
        </div>
        <div class="total-line">
            <span>IVA (21%):</span>
            <span id="iva">0.00 €</span>
        </div>
        <div class="total-line total-final">
            <span>Total:</span>
            <span id="total">0.00 €</span>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Emitir Factura</button>
        <a href="index.php?controller=factura&action=index" class="btn">Cancelar</a>
    </div>
</form>

<style>
.totales-box {
    background: var(--neutral);
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}
.total-line {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
}
.total-final {
    border-top: 2px solid var(--border);
    font-weight: 600;
    font-size: 1.125rem;
}
.linea-producto {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.linea-producto select {
    flex: 2;
}
.linea-producto input {
    flex: 1;
}
.producto-select, .cantidad-input {
    padding: 0.5rem;
    border: 1px solid var(--border);
    border-radius: 6px;
}
</style>

<script>
let numLineas = 1;

function agregarLinea() {
    const container = document.getElementById('lineas-producto');
    const opciones = container.querySelector('select').innerHTML;
    
    const div = document.createElement('div');
    div.className = 'linea-producto';
    div.dataset.index = numLineas;
    div.innerHTML = `
        <select name="lineas[${numLineas}][producto_id]" required>${opciones}</select>
        <input type="number" name="lineas[${numLineas}][cantidad]" value="1" min="1" placeholder="Cantidad">
        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarLinea(this)">X</button>
    `;
    container.appendChild(div);
    numLineas++;
    
    div.querySelector('select').addEventListener('change', calcularTotales);
    div.querySelector('input').addEventListener('input', calcularTotales);
}

function eliminarLinea(btn) {
    const container = document.getElementById('lineas-producto');
    if (container.children.length > 1) {
        btn.parentElement.remove();
        calcularTotales();
    }
}

function calcularTotales() {
    let base = 0;
    const lineas = document.querySelectorAll('.linea-producto');
    
    lineas.forEach(linea => {
        const select = linea.querySelector('select');
        const cantidad = parseInt(linea.querySelector('input').value) || 0;
        const precio = parseFloat(select.options[select.selectedIndex]?.dataset?.precio) || 0;
        base += cantidad * precio;
    });
    
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    base = base - (base * descuento / 100);
    const iva = base * 0.21;
    const total = base + iva;
    
    document.getElementById('base-imponible').textContent = base.toFixed(2) + ' €';
    document.getElementById('iva').textContent = iva.toFixed(2) + ' €';
    document.getElementById('total').textContent = total.toFixed(2) + ' €';
}

document.getElementById('descuento').addEventListener('input', calcularTotales);

document.querySelectorAll('.producto-select').forEach(select => {
    select.addEventListener('change', calcularTotales);
});

document.querySelectorAll('.cantidad-input').forEach(input => {
    input.addEventListener('input', calcularTotales);
});
</script>