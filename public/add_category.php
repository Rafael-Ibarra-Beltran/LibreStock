<?php
$page_title = "Añadir Nueva Categoría";
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container mt-3'><div class='alert alert-error'>Acceso denegado. Debes ser administrador para acceder a esta página.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$category_name = "";
$name_err = "";
$success_msg = $error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["category_name"]))) {
        $name_err = "Por favor, ingresa el nombre de la categoría.";
    } else {
        $category_name = trim($_POST["category_name"]);
        $sql_check = "SELECT id FROM categories WHERE name = ?";
        
        if ($stmt_check = $mysqli->prepare($sql_check)) {
            $stmt_check->bind_param("s", $category_name);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $name_err = "Esta categoría ya existe.";
            }

            $stmt_check->close();
        }
    }

    if (empty($name_err)) {
        $sql = "INSERT INTO categories (name) VALUES (?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $category_name);

            if ($stmt->execute()) {
                $success_msg = "Categoría añadida exitosamente. <a href='categories.php'>Ver todas las categorías</a>.";
                $category_name = ""; 
            } else {
                $error_msg = "Error al añadir la categoría: " . htmlspecialchars($stmt->error);
            }
            
            $stmt->close();
        } else {
            $error_msg = "Error al preparar la consulta: " . htmlspecialchars($mysqli->error);
        }
    }
}
?>

<div class="container mt-3">
    <h2><?php echo $page_title; ?></h2>

    <?php 
    if(!empty($success_msg)) echo '<div class="alert alert-success">' . $success_msg . '</div>';
    if(!empty($error_msg)) echo '<div class="alert alert-error">' . $error_msg . '</div>';
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="category_name">Nombre de la Categoría <span style="color:red;">*</span></label>
            <input type="text" name="category_name" id="category_name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($category_name); ?>" required>
            <span class="invalid-feedback"><?php echo $name_err; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Añadir Categoría">
            <a href="categories.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?> 