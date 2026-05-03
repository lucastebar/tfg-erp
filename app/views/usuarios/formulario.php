<div class="page-header">
    <h1><?= $accion === 'nuevo' ? 'Nuevo Usuario' : 'Editar Usuario' ?></h1>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<form method="post" action="index.php?controller=usuario&action=<?= $accion === 'nuevo' ? 'guardar' : 'actualizar' ?>" class="form">
    <?php if ($accion === 'editar'): ?>
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="password">
            Contraseña <?= $accion === 'nuevo' ? '*' : '(dejar vacío para mantener)' ?>
        </label>
        <input type="password" id="password" name="password" <?= $accion === 'nuevo' ? 'required' : '' ?>>
    </div>

    <div class="form-group">
        <label for="rol">Rol *</label>
        <select id="rol" name="rol" required>
            <option value="operario" <?= ($usuario['rol'] ?? 'operario') === 'operario' ? 'selected' : '' ?>>Operario</option>
            <option value="admin" <?= ($usuario['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </div>

    <?php if ($accion === 'editar'): ?>
    <div class="form-group">
        <label>
            <input type="checkbox" name="activo" value="1" <?= ($usuario['activo'] ?? 1) ? 'checked' : '' ?>>
            Usuario activo
        </label>
    </div>
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $accion === 'nuevo' ? 'Crear Usuario' : 'Guardar Cambios' ?></button>
        <a href="index.php?controller=usuario&action=index" class="btn">Cancelar</a>
    </div>
</form>