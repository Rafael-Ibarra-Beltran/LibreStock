<?php
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = 'Acceso denegado. Debes ser administrador.';
    $_SESSION['message_type'] = 'error';
    header('Location: users.php');
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    $_SESSION['message'] = 'ID de usuario no válido.';
    $_SESSION['message_type'] = 'error';
    header('Location: users.php');
    exit;
}

$user_id = $_GET['id'];

if ($_SESSION['user_id'] == $user_id) {
    $_SESSION['message'] = 'No puedes eliminar tu propio usuario.';
    $_SESSION['message_type'] = 'error';
    header('Location: users.php');
    exit;
}

$sql = "DELETE FROM users WHERE id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Usuario eliminado correctamente.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error al eliminar el usuario.';
        $_SESSION['message_type'] = 'error';
    }
    
    $stmt->close();
} else {
    $_SESSION['message'] = 'Error al preparar la consulta de eliminación.';
    $_SESSION['message_type'] = 'error';
}
header('Location: users.php');
exit; 