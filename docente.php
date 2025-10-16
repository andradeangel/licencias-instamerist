<?php
require 'session.php';
require 'db.php';

if ($_SESSION['user_rol'] !== 'docente') {
    header('Location: login.php');
    exit;
}

// Procesar asignación estudiante-padre
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asignar_padre'])) {
    $estudiante_id = $_POST['estudiante_id'];
    $padre_id = $_POST['padre_id'];

    $stmt = $pdo->prepare("INSERT INTO estudiante_padre (estudiante_id, padre_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE padre_id = ?");
    if ($stmt->execute([$estudiante_id, $padre_id, $padre_id])) {
        $success = "Estudiante asignado a padre exitosamente.";
    } else {
        $error = "Error al asignar.";
    }
}

// Procesar añadir licencia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_licencia'])) {
    $estudiante_id = $_POST['estudiante_id'];
    $fecha = $_POST['fecha'];
    $motivo = $_POST['motivo'];

    $stmt = $pdo->prepare("INSERT INTO licencias (estudiante_id, fecha_licencia, motivo, creada_por) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$estudiante_id, $fecha, $motivo, $_SESSION['user_id']])) {
        $success = "Licencia añadida exitosamente.";
    } else {
        $error = "Error al añadir licencia.";
    }
}

// Procesar editar licencia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_licencia'])) {
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $motivo = $_POST['motivo'];

    $stmt = $pdo->prepare("UPDATE licencias SET fecha_licencia = ?, motivo = ? WHERE id = ?");
    if ($stmt->execute([$fecha, $motivo, $id])) {
        $success = "Licencia editada exitosamente.";
    } else {
        $error = "Error al editar licencia.";
    }
}

// Procesar cambio de estado de licencia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_estado'])) {
    $id = $_POST['licencia_id'];
    $nuevo_estado = $_POST['estado'];
    
    $stmt = $pdo->prepare("UPDATE licencias SET estado = ? WHERE id = ?");
    if ($stmt->execute([$nuevo_estado, $id])) {
        $success = "Estado de licencia actualizado exitosamente.";
    } else {
        $error = "Error al actualizar estado.";
    }
}

// Procesar eliminar licencia
if (isset($_GET['delete_licencia'])) {
    $id = $_GET['delete_licencia'];
    $stmt = $pdo->prepare("DELETE FROM licencias WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "Licencia eliminada exitosamente.";
    } else {
        $error = "Error al eliminar licencia.";
    }
}

// Obtener estudiantes con padres
$stmt = $pdo->query("
    SELECT e.id, e.nombre, e.apellido, e.ci, u.nombre as padre_nombre, u.apellido as padre_apellido
    FROM estudiantes e
    LEFT JOIN estudiante_padre ep ON e.id = ep.estudiante_id
    LEFT JOIN usuarios u ON ep.padre_id = u.id
");
$estudiantes = $stmt->fetchAll();

// Obtener padres
$padres = $pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'padre'")->fetchAll();

// Obtener licencias
$licencias = $pdo->query("
    SELECT l.id, l.fecha_licencia, l.motivo, l.estado, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido
    FROM licencias l
    JOIN estudiantes e ON l.estudiante_id = e.id
    ORDER BY l.fecha_creacion DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Docente - Instituto Americano Amerinst</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Instituto Americano "Amerinst"</h1>
        <p>Panel de Docente - Bienvenido, <?php echo $_SESSION['user_name']; ?></p>
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

        <h2>Estudiantes y Asignación de Padres</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>CI</th>
                    <th>Nombre</th>
                    <th>Padre Asignado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $est): ?>
                    <tr>
                        <td><?php echo $est['ci']; ?></td>
                        <td><?php echo $est['nombre'] . ' ' . $est['apellido']; ?></td>
                        <td><?php echo $est['padre_nombre'] ? $est['padre_nombre'] . ' ' . $est['padre_apellido'] : 'Sin asignar'; ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="estudiante_id" value="<?php echo $est['id']; ?>">
                                <select name="padre_id" required>
                                    <option value="">Seleccionar Padre</option>
                                    <?php foreach ($padres as $padre): ?>
                                        <option value="<?php echo $padre['id']; ?>"><?php echo $padre['nombre'] . ' ' . $padre['apellido']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="asignar_padre">Asignar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Gestionar Licencias</h2>
        <h3>Añadir Licencia</h3>
        <form method="post" class="form-container">
            <div class="form-group">
                <label for="estudiante_id">Estudiante:</label>
                <select id="estudiante_id" name="estudiante_id" required>
                    <option value="">Seleccionar Estudiante</option>
                    <?php foreach ($estudiantes as $est): ?>
                        <option value="<?php echo $est['id']; ?>"><?php echo $est['nombre'] . ' ' . $est['apellido']; ?></option>
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
            <button type="submit" name="add_licencia">Añadir Licencia</button>
        </form>

        <h3>Licencias Existentes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($licencias as $lic): ?>
                    <tr>
                        <td><?php echo $lic['estudiante_nombre'] . ' ' . $lic['estudiante_apellido']; ?></td>
                        <td><?php echo $lic['fecha_licencia']; ?></td>
                        <td><?php echo $lic['motivo']; ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="licencia_id" value="<?php echo $lic['id']; ?>">
                                <select name="estado" onchange="this.form.submit()">
                                    <option value="pendiente" <?php echo $lic['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="aceptado" <?php echo $lic['estado'] == 'aceptado' ? 'selected' : ''; ?>>Aceptado</option>
                                    <option value="rechazado" <?php echo $lic['estado'] == 'rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                                </select>
                                <input type="hidden" name="change_estado" value="1">
                            </form>
                        </td>
                        <td>
                            <button type="button" onclick="editLicencia(<?php echo $lic['id']; ?>, '<?php echo $lic['fecha_licencia']; ?>', <?php echo htmlspecialchars(json_encode($lic['motivo']), ENT_QUOTES); ?>)">Editar</button>
                            <a href="?delete_licencia=<?php echo $lic['id']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Formulario de edición oculto -->
        <div id="editForm" style="display:none;" class="form-container">
            <h3>Editar Licencia</h3>
            <form method="post">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label for="edit_fecha">Fecha:</label>
                    <input type="date" id="edit_fecha" name="fecha" required>
                </div>
                <div class="form-group">
                    <label for="edit_motivo">Motivo:</label>
                    <textarea id="edit_motivo" name="motivo" required></textarea>
                </div>
                <button type="submit" name="edit_licencia">Guardar Cambios</button>
                <button type="button" onclick="cancelEdit()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function editLicencia(id, fecha, motivo) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_fecha').value = fecha;
            document.getElementById('edit_motivo').value = motivo;
            document.getElementById('editForm').style.display = 'block';
            // Desplazar hacia el formulario de edición
            document.getElementById('editForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        function cancelEdit() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>