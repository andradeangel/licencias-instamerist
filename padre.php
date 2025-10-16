<?php
require 'session.php';
require 'db.php';

if ($_SESSION['user_rol'] !== 'padre') {
    header('Location: login.php');
    exit;
}

// Procesar añadir licencia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_licencia'])) {
    $estudiante_id = $_POST['estudiante_id'];
    $fecha = $_POST['fecha'];
    $motivo = $_POST['motivo'];

    // Verificar que el estudiante esté asignado a este padre
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM estudiante_padre WHERE estudiante_id = ? AND padre_id = ?");
    $stmt->execute([$estudiante_id, $_SESSION['user_id']]);
    if ($stmt->fetchColumn() > 0) {
        $stmt = $pdo->prepare("INSERT INTO licencias (estudiante_id, fecha_licencia, motivo, creada_por) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$estudiante_id, $fecha, $motivo, $_SESSION['user_id']])) {
            $success = "Licencia creada exitosamente.";
        } else {
            $error = "Error al crear licencia.";
        }
    } else {
        $error = "No tienes permiso para crear licencia para este estudiante.";
    }
}

// Obtener hijos asignados
$stmt = $pdo->prepare("
    SELECT e.id, e.nombre, e.apellido, e.ci
    FROM estudiantes e
    JOIN estudiante_padre ep ON e.id = ep.estudiante_id
    WHERE ep.padre_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$hijos = $stmt->fetchAll();

// Obtener licencias de los hijos
$licencias = [];
foreach ($hijos as $hijo) {
    $stmt = $pdo->prepare("
        SELECT l.id, l.fecha_licencia, l.motivo, l.estado, l.fecha_creacion
        FROM licencias l
        WHERE l.estudiante_id = ?
        ORDER BY l.fecha_creacion DESC
    ");
    $stmt->execute([$hijo['id']]);
    $licencias[$hijo['id']] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Padre - Instituto Americano Amerinst</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Instituto Americano "Amerinst"</h1>
        <p>Panel de Padre - Bienvenido, <?php echo $_SESSION['user_name']; ?></p>
    </header>
    <nav>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
    <div class="container">
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <h2>Mis Hijos y sus Licencias</h2>
        <?php foreach ($hijos as $hijo): ?>
            <h3><?php echo $hijo['nombre'] . ' ' . $hijo['apellido']; ?> (CI: <?php echo $hijo['ci']; ?>)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha de Licencia</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($licencias[$hijo['id']]) && count($licencias[$hijo['id']]) > 0): ?>
                        <?php foreach ($licencias[$hijo['id']] as $lic): ?>
                            <tr>
                                <td><?php echo $lic['fecha_licencia']; ?></td>
                                <td><?php echo $lic['motivo']; ?></td>
                                <td>
                                    <span style="padding: 5px 10px; border-radius: 4px;
                                        <?php
                                        if ($lic['estado'] == 'aceptado') echo 'background-color: #d4edda; color: #155724;';
                                        elseif ($lic['estado'] == 'rechazado') echo 'background-color: #f8d7da; color: #721c24;';
                                        else echo 'background-color: #fff3cd; color: #856404;';
                                        ?>">
                                        <?php echo ucfirst($lic['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo $lic['fecha_creacion']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No hay licencias registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <h2>Crear Nueva Licencia</h2>
        <form method="post" class="form-container">
            <div class="form-group">
                <label for="estudiante_id">Seleccionar Hijo:</label>
                <select id="estudiante_id" name="estudiante_id" required>
                    <option value="">Seleccionar Hijo</option>
                    <?php foreach ($hijos as $hijo): ?>
                        <option value="<?php echo $hijo['id']; ?>"><?php echo $hijo['nombre'] . ' ' . $hijo['apellido']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha de Licencia:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="motivo">Motivo:</label>
                <textarea id="motivo" name="motivo" required></textarea>
            </div>
            <button type="submit" name="add_licencia">Crear Licencia</button>
        </form>
    </div>
</body>
</html>