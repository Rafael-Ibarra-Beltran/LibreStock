<?php
$page_title = "Añadir Nuevo Producto";
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container mt-3'><div class='alert alert-error'>Acceso denegado. Debes ser administrador para acceder a esta página.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$all_categories = [];
$sql_get_categories = "SELECT id, name FROM categories ORDER BY name ASC";
if ($result_cats = $mysqli->query($sql_get_categories)) {
    while ($cat_row = $result_cats->fetch_assoc()) {
        $all_categories[] = $cat_row;
    }

    $result_cats->free();
} else {
    $error_msg = "Error al obtener la lista de categorías: " . htmlspecialchars($mysqli->error);
}

$title = $author = $description = $price = $stock_quantity = $publication_date = "";
$selected_categories = []; 
$title_err = $price_err = $stock_quantity_err = "";
$success_msg = $error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["title"]))) {
        $title_err = "Por favor, ingresa el título del producto.";
    }

    $title = trim($_POST["title"]);

    $author = trim($_POST["author"]);

    $description = trim($_POST["description"]);

    if (empty(trim($_POST["price"]))) {
        $price_err = "Por favor, ingresa el precio.";
    } elseif (!is_numeric(trim($_POST["price"])) || floatval(trim($_POST["price"])) < 0) {
        $price_err = "Por favor, ingresa un precio válido (número positivo).";
    }

    $price = trim($_POST["price"]);

    if (empty(trim($_POST["stock_quantity"]))) {
        $stock_quantity_err = "Por favor, ingresa la cantidad en stock.";
    } elseif (!ctype_digit(trim($_POST["stock_quantity"])) || intval(trim($_POST["stock_quantity"])) < 0) {
        $stock_quantity_err = "Por favor, ingresa una cantidad válida (número entero no negativo).";
    }

    $stock_quantity = trim($_POST["stock_quantity"]);

    $publication_date = trim($_POST["publication_date"]);

    if (empty($publication_date)) {
        $publication_date = null; 
    } elseif (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $publication_date)) {
        
    }

    if (!empty($_POST['product_categories']) && is_array($_POST['product_categories'])) {
        $selected_categories = $_POST['product_categories'];
    }

    if (empty($title_err) && empty($price_err) && empty($stock_quantity_err)) {
        $sql = "INSERT INTO products (title, author, description, price, stock_quantity, publication_date) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("sssdis", $title, $author, $description, $price, $stock_quantity, $publication_date);

            if ($stmt->execute()) {
                $new_product_id = $stmt->insert_id; 
                $stmt->close(); 

                if (!empty($selected_categories) && $new_product_id) {
                    $sql_cat = "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)";
                    
                    if ($stmt_cat = $mysqli->prepare($sql_cat)) {
                        foreach ($selected_categories as $category_id) {
                            if (ctype_digit((string)$category_id)) { 
                                $stmt_cat->bind_param("ii", $new_product_id, $category_id);
                                if (!$stmt_cat->execute()) {
                                    $error_msg .= " Error al asignar categoría ID: " . $category_id . ". "; 
                                }
                            }
                        }

                        $stmt_cat->close();
                    }
                }

                $success_msg = "Producto añadido exitosamente. <a href='products.php'>Ver todos los productos</a>.";
                $title = $author = $description = $price = $stock_quantity = $publication_date = "";
                $selected_categories = [];
            } else {
                $error_msg = "Error al añadir el producto: " . htmlspecialchars($stmt->error);
                if(isset($stmt)) $stmt->close(); 
            }
        } else {
            $error_msg = "Error al preparar la consulta del producto: " . htmlspecialchars($mysqli->error);
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
            <label for="title">Título del Producto <span style="color:red;">*</span></label>
            <input type="text" name="title" id="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($title); ?>" required>
            <span class="invalid-feedback"><?php echo $title_err; ?></span>
        </div>

        <div class="form-group">
            <label for="author">Autor</label>
            <input type="text" name="author" id="author" class="form-control" value="<?php echo htmlspecialchars($author); ?>">
        </div>

        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Precio <span style="color:red;">*</span></label>
            <input type="number" name="price" id="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($price); ?>" step="0.01" min="0" required>
            <span class="invalid-feedback"><?php echo $price_err; ?></span>
        </div>

        <div class="form-group">
            <label for="stock_quantity">Cantidad en Stock <span style="color:red;">*</span></label>
            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control <?php echo (!empty($stock_quantity_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($stock_quantity); ?>" min="0" required>
            <span class="invalid-feedback"><?php echo $stock_quantity_err; ?></span>
        </div>

        <div class="form-group">
            <label for="publication_date">Fecha de Publicación (YYYY-MM-DD)</label>
            <input type="date" name="publication_date" id="publication_date" class="form-control" value="<?php echo htmlspecialchars($publication_date); ?>">
        </div>

        <div class="form-group">
            <label>Categorías</label>
            <div class="categories-checkbox-group">
                <div class="row">
                    <?php if (!empty($all_categories)): ?>
                        <?php foreach ($all_categories as $category): ?>
                            <div class="col-md-3 col-sm-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="product_categories[]" value="<?php echo $category['id']; ?>" id="cat_<?php echo $category['id']; ?>"
                                        <?php if (in_array($category['id'], $selected_categories)) echo 'checked'; ?>>
                                    <label class="form-check-label" for="cat_<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                           <p>No hay categorías disponibles. Puedes <a href="add_category.php" target="_blank">añadir una nueva</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Añadir Producto">
            <a href="products.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?> 