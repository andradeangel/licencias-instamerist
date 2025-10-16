<?php
require 'session.php';
require 'db.php';

if ($_SESSION['user_rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Procesar creación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_usuario'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar si el email ya existe
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
    $stmt_check->execute([$email]);
    if ($stmt_check->fetchColumn() > 0) {
        $error = "El correo electrónico ya está en uso.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $apellido, $email, $password, $rol])) {
            $success = "Usuario creado exitosamente.";
        } else {
            $error = "Error al crear usuario.";
        }
    }
}

// Procesar creación de estudiante
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_estudiante'])) {
    $nombre = trim($_POST['est_nombre']);
    $apellido = trim($_POST['est_apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $ci = trim($_POST['ci']);

    // Verificar si el CI ya existe
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM estudiantes WHERE ci = ?");
    $stmt_check->execute([$ci]);
    if ($stmt_check->fetchColumn() > 0) {
        $error_estudiante = "El CI ya está registrado.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre, apellido, fecha_nacimiento, ci) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $apellido, $fecha_nacimiento, $ci])) {
            $success_estudiante = "Estudiante creado exitosamente.";
        } else {
            $error_estudiante = "Error al crear estudiante.";
        }
    }
}

// Estadísticas
$stmt = $pdo->query("SELECT rol, COUNT(*) as count FROM usuarios GROUP BY rol");
$usuarios_por_rol = $stmt->fetchAll();

$total_estudiantes = $pdo->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();
$total_licencias = $pdo->query("SELECT COUNT(*) FROM licencias")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador - Instituto Americano Amerinst</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Instituto Americano "Amerinst"</h1>
        <p>Panel de Administrador - Bienvenido, <?php echo $_SESSION['user_name']; ?></p>
    </header>
    <nav>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
    <div class="container">
        <h2>Estadísticas</h2>
        <p>Total de Estudiantes: <?php echo $total_estudiantes; ?></p>
        <p>Total de Licencias: <?php echo $total_licencias; ?></p>
        <h3>Usuarios por Rol:</h3>
        <ul>
            <?php foreach ($usuarios_por_rol as $stat): ?>
                <li><?php echo ucfirst($stat['rol']); ?>: <?php echo $stat['count']; ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>Crear Nuevo Usuario</h2>
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" class="form-container">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="admin">Administrador</option>
                    <option value="docente">Docente</option>
                    <option value="padre">Padre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="crear_usuario">Crear Usuario</button>
        </form>

        <h2>Crear Nuevo Estudiante</h2>
        <?php if (isset($success_estudiante)): ?>
            <div class="success"><?php echo $success_estudiante; ?></div>
        <?php endif; ?>
        <?php if (isset($error_estudiante)): ?>
            <div class="error"><?php echo $error_estudiante; ?></div>
        <?php endif; ?>
        <form method="post" class="form-container">
            <div class="form-group">
                <label for="est_nombre">Nombre:</label>
                <input type="text" id="est_nombre" name="est_nombre" required>
            </div>
            <div class="form-group">
                <label for="est_apellido">Apellido:</label>
                <input type="text" id="est_apellido" name="est_apellido" required>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="form-group">
                <label for="ci">Cédula de Identidad (CI):</label>
                <input type="text" id="ci" name="ci" required>
            </div>
            <button type="submit" name="crear_estudiante">Crear Estudiante</button>
        </form>
    </div>
</body>
</html>