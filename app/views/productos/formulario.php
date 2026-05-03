<div class="page-header">
    <h1><?= $accion === 'nuevo' ? 'Nuevo Producto' : 'Editar Producto' ?></h1>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<form method="post" action="index.php?controller=producto&action=<?= $accion === 'nuevo' ? 'guardar' : 'actualizar' ?>" class="form">
    <?php if ($accion === 'editar'): ?>
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="codigo">Código *</label>
        <input type="text" id="codigo" name="codigo" value="<?= htmlspecialchars($producto['codigo'] ?? '') ?>" required maxlength="50">
    </div>

    <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="precio_coste">Precio de Coste</label>
            <input type="number" id="precio_coste" name="precio_coste" value="<?= $producto['precio_coste'] ?? '' ?>" step="0.01" min="0">
        </div>

        <div class="form-group">
            <label for="pvp">PVP *</label>
            <input type="number" id="pvp" name="pvp" value="<?= $producto['pvp'] ?? '' ?>" step="0.01" min="0" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="stock_actual">Stock Actual</label>
            <input type="number" id="stock_actual" name="stock_actual" value="<?= $producto['stock_actual'] ?? 0 ?>" min="0">
        </div>

        <div class="form-group">
            <label for="stock_minimo">Stock Mínimo</label>
            <input type="number" id="stock_minimo" name="stock_minimo" value="<?= $producto['stock_minimo'] ?? 5 ?>" min="0">
        </div>
    </div>

    <div class="form-group">
        <label for="tipo_iva">Tipo de IVA</label>
        <select id="tipo_iva" name="tipo_iva">
            <option value="21" <?= ($producto['tipo_iva'] ?? '21') === '21' ? 'selected' : '' ?>>21%</option>
            <option value="10" <?= ($producto['tipo_iva'] ?? '') === '10' ? 'selected' : '' ?>>10%</option>
            <option value="4" <?= ($producto['tipo_iva'] ?? '') === '4' ? 'selected' : '' ?>>4%</option>
            <option value="0" <?= ($producto['tipo_iva'] ?? '') === '0' ? 'selected' : '' ?>>0%</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $accion === 'nuevo' ? 'Crear Producto' : 'Guardar Cambios' ?></button>
        <a href="index.php?controller=producto&action=index" class="btn">Cancelar</a>
    </div>
</form>