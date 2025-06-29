<?php
$page_title = "Editar Categoría";
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container mt-3'><div class='alert alert-error'>Acceso denegado. Debes ser administrador.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$category_id = $category_name = "";
$name_err = "";
$success_msg = $error_msg = "";

if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && ctype_digit(trim($_GET["id"]))) {
    $category_id = trim($_GET["id"]);

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        $sql_select = "SELECT name FROM categories WHERE id = ?";
        
        if ($stmt_select = $mysqli->prepare($sql_select)) {
            $stmt_select->bind_param("i", $category_id);
            
            if ($stmt_select->execute()) {
                $result = $stmt_select->get_result();
                
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $category_name = $row['name'];
                } else {
                    $error_msg = "No se encontró ninguna categoría con ese ID.";
                }
            } else {
                $error_msg = "Error al obtener datos de la categoría.";
            }

            $stmt_select->close();
        } else {
            $error_msg = "Error al preparar la consulta de selección: " . htmlspecialchars($mysqli->error);
        }
    }
} else {
    $error_msg = "ID de categoría no válido o no proporcionado.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = trim($_POST["category_id"]); 

    if (empty($category_id) || !ctype_digit($category_id)) {
        $error_msg = "ID de categoría no válido para la actualización.";
    } else {
        if (empty(trim($_POST["category_name"]))) {
            $name_err = "Por favor, ingresa el nombre de la categoría.";
        }
        
        $category_name_post = trim($_POST["category_name"]);

        if (empty($name_err)) {
            $sql_check = "SELECT id FROM categories WHERE name = ? AND id != ?";
            
            if ($stmt_check = $mysqli->prepare($sql_check)) {
                $stmt_check->bind_param("si", $category_name_post, $category_id);
                $stmt_check->execute();
                $stmt_check->store_result();
                
                if ($stmt_check->num_rows > 0) {
                    $name_err = "Este nombre de categoría ya está en uso por otra categoría.";
                }

                $stmt_check->close();
            }
        }
        
        if (empty($name_err)) {
            $sql_update = "UPDATE categories SET name = ? WHERE id = ?";
            
            if ($stmt_update = $mysqli->prepare($sql_update)) {
                $stmt_update->bind_param("si", $category_name_post, $category_id);
                
                if ($stmt_update->execute()) {
                    $success_msg = "Categoría actualizada exitosamente. <a href='categories.php'>Volver a la lista</a>.";
                    $category_name = $category_name_post; 
                } else {
                    $error_msg = "Error al actualizar la categoría: " . htmlspecialchars($stmt_update->error);
                }
                
                $stmt_update->close();
            } else {
                $error_msg = "Error al preparar la consulta de actualización: " . htmlspecialchars($mysqli->error);
            }
        } else {
             $category_name = $category_name_post;
        }
    }
}

?>

<div class="container mt-3">
    <h2><?php echo $page_title; ?></h2>

    <?php 
    if(!empty($success_msg)) echo '<div class="alert alert-success">' . $success_msg . '</div>';
    if(!empty($error_msg) && (empty($success_msg) || (!isset($result) || $result->num_rows != 1)) ) {
        echo '<div class="alert alert-error">' . $error_msg . '</div>';
    }
    ?>

    <?php if (!empty($category_id) && (isset($result) && $result->num_rows == 1 || $_SERVER["REQUEST_METHOD"] == "POST")) : ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo htmlspecialchars($category_id); ?>" method="post">
        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">

        <div class="form-group">
            <label for="category_name">Nombre de la Categoría <span style="color:red;">*</span></label>
            <input type="text" name="category_name" id="category_name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($category_name); ?>" required>
            <span class="invalid-feedback"><?php echo $name_err; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Actualizar Categoría">
            <a href="categories.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
    <?php elseif (!empty($error_msg)): ?>
        <p><a href="categories.php" class="btn btn-secondary">Volver a la lista de categorías</a></p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?> 