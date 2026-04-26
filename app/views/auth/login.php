<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TFG ERP</title>
    <link rel="stylesheet" href="/tarde/tfg-erp/public/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>TFG ERP</h1>
            <p class="login-subtitle">Iniciar sesión</p>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            <?php endif; ?>

            <form method="post" action="index.php?controller=auth&action=login">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>