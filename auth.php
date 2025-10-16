<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Por favor, complete todos los campos.';
        header('Location: login.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, nombre, apellido, password, rol FROM usuarios WHERE email = ? AND activo = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_rol'] = $user['rol'];

        // Redirigir según rol
        switch ($user['rol']) {
            case 'admin':
                header('Location: admin.php');
                break;
            case 'docente':
                header('Location: docente.php');
                break;
            case 'padre':
                header('Location: padre.php');
                break;
            default:
                header('Location: login.php');
        }
        exit;
    } else {
        $_SESSION['error'] = 'Credenciales incorrectas.';
        header('Location: login.php');
        exit;
    }
}
?>