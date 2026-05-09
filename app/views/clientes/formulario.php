<?php $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tarde/tfg-erp'; ?>
<div class="page-header">
    <h1><?= $accion === 'nuevo' ? 'Nuevo Cliente' : 'Editar Cliente' ?></h1>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<form method="post" action="<?= $basePath ?>/index.php?controller=cliente&action=<?= $accion === 'nuevo' ? 'guardar' : 'actualizar' ?>" class="form">
    <?php if ($accion === 'editar'): ?>
        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="razon_social">Razón Social *</label>
        <input type="text" id="razon_social" name="razon_social" value="<?= htmlspecialchars($cliente['razon_social'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="nif">NIF *</label>
        <input type="text" id="nif" name="nif" value="<?= htmlspecialchars($cliente['nif'] ?? '') ?>" required maxlength="9">
    </div>

    <div class="form-group">
        <label for="direccion">Dirección Fiscal</label>
        <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="cp">Código Postal</label>
            <input type="text" id="cp" name="cp" value="<?= htmlspecialchars($cliente['cp'] ?? '') ?>" maxlength="5">
        </div>

        <div class="form-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" value="<?= htmlspecialchars($cliente['ciudad'] ?? '') ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($cliente['email'] ?? '') ?>">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $accion === 'nuevo' ? 'Crear Cliente' : 'Guardar Cambios' ?></button>
        <a href="<?= $basePath ?>/index.php?controller=cliente&action=index" class="btn">Cancelar</a>
    </div>
</form>