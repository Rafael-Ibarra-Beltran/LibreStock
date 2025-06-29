<?php
require_once '../src/config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message_type'] = 'error';
    $_SESSION['message'] = 'Acceso denegado. Debes ser administrador.';
    header("location: products.php");
    exit;
}

if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && ctype_digit(trim($_GET["id"]))) {
    $product_id = trim($_GET["id"]);

    $sql = "DELETE FROM products WHERE id = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['message_type'] = 'success';
                $_SESSION['message'] = "Producto eliminado exitosamente.";
            } else {
                $_SESSION['message_type'] = 'error';
                $_SESSION['message'] = "No se encontró ningún producto con ese ID o no se pudo eliminar.";
            }
        } else {
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = "Error al intentar eliminar el producto: " . htmlspecialchars($stmt->error);
        }
        
        $stmt->close();
    } else {
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = "Error al preparar la consulta de eliminación: " . htmlspecialchars($mysqli->error);
    }
} else {
    $_SESSION['message_type'] = 'error';
    $_SESSION['message'] = "ID de producto no válido o no proporcionado para la eliminación.";
}

header("location: products.php");
exit;
?> 