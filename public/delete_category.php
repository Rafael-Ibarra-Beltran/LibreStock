<?php
require_once '../src/config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message_type'] = 'error';
    $_SESSION['message'] = 'Acceso denegado. Debes ser administrador.';
    header("location: categories.php");
    exit;
}

if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && ctype_digit(trim($_GET["id"]))) {
    $category_id = trim($_GET["id"]);

    $sql_delete_category = "DELETE FROM categories WHERE id = ?";

    if ($stmt_cat = $mysqli->prepare($sql_delete_category)) {
        $stmt_cat->bind_param("i", $category_id);

        if ($stmt_cat->execute()) {
            if ($stmt_cat->affected_rows > 0) {
                $_SESSION['message_type'] = 'success';
                $_SESSION['message'] = "Categoría eliminada exitosamente. Las asociaciones con productos también fueron eliminadas.";
            } else {
                $_SESSION['message_type'] = 'error';
                $_SESSION['message'] = "No se encontró ninguna categoría con ese ID o no se pudo eliminar.";
            }
        } else {
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = "Error al intentar eliminar la categoría: " . htmlspecialchars($stmt_cat->error);
        }
        
        $stmt_cat->close();
    } else {
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = "Error al preparar la consulta de eliminación de categoría: " . htmlspecialchars($mysqli->error);
    }
} else {
    $_SESSION['message_type'] = 'error';
    $_SESSION['message'] = "ID de categoría no válido o no proporcionado para la eliminación.";
}

header("location: categories.php");
exit;
?> 