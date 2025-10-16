<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Redirigir según rol si ya está logueado
    switch ($_SESSION['user_rol']) {
        case 'admin':
            header('Location: admin.php');
            break;
        case 'docente':
            header('Location: docente.php');
            break;
        case 'padre':
            header('Location: padre.php');
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Instituto Americano Amerinst</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Instituto Americano "Amerinst"</h1>
        <p>Sistema de Control de Licencias</p>
    </header>
    <div class="container">
        <div class="form-container">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form action="auth.php" method="post">
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>