<?php
$page_title = "Editar Producto";
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container mt-3'><div class='alert alert-error'>Acceso denegado. Debes ser administrador para acceder a esta página.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$product_id = $title = $author = $description = $price = $stock_quantity = $publication_date = "";
$title_err = $price_err = $stock_quantity_err = "";
$success_msg = $error_msg = "";
$product_current_categories = []; 
$selected_categories_from_form = []; 

$all_categories = [];
$sql_get_all_categories = "SELECT id, name FROM categories ORDER BY name ASC";

if ($result_all_cats = $mysqli->query($sql_get_all_categories)) {
    while ($cat_row = $result_all_cats->fetch_assoc()) {
        $all_categories[] = $cat_row;
    }

    $result_all_cats->free();
} else {
    $error_msg = "Error al obtener la lista completa de categorías: " . htmlspecialchars($mysqli->error);
}

if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && ctype_digit(trim($_GET["id"]))) {
    $product_id = trim($_GET["id"]);

    if ($_SERVER["REQUEST_METHOD"] != "POST") { 
        $sql_select = "SELECT title, author, description, price, stock_quantity, publication_date FROM products WHERE id = ?";
        
        if ($stmt_select = $mysqli->prepare($sql_select)) {
            $stmt_select->bind_param("i", $product_id);
            
            if ($stmt_select->execute()) {
                $result = $stmt_select->get_result();
                
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $title = $row['title'];
                    $author = $row['author'];
                    $description = $row['description'];
                    $price = $row['price'];
                    $stock_quantity = $row['stock_quantity'];
                    $publication_date = $row['publication_date'];
                    
                    if ($publication_date === null) $publication_date = '';

                    $sql_prod_cats = "SELECT category_id FROM product_categories WHERE product_id = ?";
                    
                    if ($stmt_prod_cats = $mysqli->prepare($sql_prod_cats)) {
                        $stmt_prod_cats->bind_param("i", $product_id);
                        
                        if ($stmt_prod_cats->execute()) {
                            $result_prod_cats = $stmt_prod_cats->get_result();
                            
                            while ($cat_data = $result_prod_cats->fetch_assoc()) {
                                $product_current_categories[] = $cat_data['category_id'];
                            }
                        }
                        
                        $stmt_prod_cats->close();
                    } 
                } else {
                    $error_msg = "No se encontró ningún producto con ese ID.";
                }
            } else {
                $error_msg = "Error al obtener datos del producto.";
            }
            
            $stmt_select->close();
        } else {
            $error_msg = "Error al preparar la consulta de selección: " . htmlspecialchars($mysqli->error);
        }
    }
} else {
    $error_msg = "ID de producto no válido o no proporcionado.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["product_id"])) && isset($_GET["id"])){
        $product_id = trim($_GET["id"]); 
    } else {
        $product_id = trim($_POST["product_id"]);
    }

    if (empty($product_id) || !ctype_digit($product_id)) {
        $error_msg = "ID de producto no válido para la actualización.";
    } else {
        if (empty(trim($_POST["title"]))) {
            $title_err = "Por favor, ingresa el título del producto.";
        }

        $title = trim($_POST["title"]);

        $author = trim($_POST["author"]);
        $description = trim($_POST["description"]);

        if (empty(trim($_POST["price"]))) {
            $price_err = "Por favor, ingresa el precio.";
        } elseif (!is_numeric(trim($_POST["price"])) || floatval(trim($_POST["price"])) < 0) {
            $price_err = "Por favor, ingresa un precio válido.";
        }

        $price = trim($_POST["price"]);

        if (empty(trim($_POST["stock_quantity"]))) {
            $stock_quantity_err = "Por favor, ingresa la cantidad en stock.";
        } elseif (!ctype_digit(trim($_POST["stock_quantity"])) || intval(trim($_POST["stock_quantity"])) < 0) {
            $stock_quantity_err = "Por favor, ingresa una cantidad válida.";
        }

        $stock_quantity = trim($_POST["stock_quantity"]);

        $publication_date = trim($_POST["publication_date"]);
        if (empty($publication_date)) $publication_date = null;

        if (!empty($_POST['product_categories']) && is_array($_POST['product_categories'])) {
            $selected_categories_from_form = $_POST['product_categories'];
        } else {
            $selected_categories_from_form = []; 
        }

        if (empty($title_err) && empty($price_err) && empty($stock_quantity_err)) {
            $sql_update = "UPDATE products SET title = ?, author = ?, description = ?, price = ?, stock_quantity = ?, publication_date = ? WHERE id = ?";

            if ($stmt_update = $mysqli->prepare($sql_update)) {
                $stmt_update->bind_param("sssdisi", $title, $author, $description, $price, $stock_quantity, $publication_date, $product_id);

                if ($stmt_update->execute()) {
                    $stmt_update->close(); 

                    $sql_delete_old_cats = "DELETE FROM product_categories WHERE product_id = ?";
                    if ($stmt_delete_cats = $mysqli->prepare($sql_delete_old_cats)) {
                        $stmt_delete_cats->bind_param("i", $product_id);
                        $stmt_delete_cats->execute(); 
                        $stmt_delete_cats->close();
                    }

                    if (!empty($selected_categories_from_form)) {
                        $sql_insert_cat = "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)";
                        
                        if ($stmt_insert_cat = $mysqli->prepare($sql_insert_cat)) {
                            foreach ($selected_categories_from_form as $category_id_form) {
                                if (ctype_digit((string)$category_id_form)) {
                                    $stmt_insert_cat->bind_param("ii", $product_id, $category_id_form);
                                    if (!$stmt_insert_cat->execute()) {
                                         $error_msg .= " Error al asignar categoría ID: " . $category_id_form . ". ";
                                    }
                                }
                            }

                            $stmt_insert_cat->close();
                        }
                    }
                    
                    $success_msg = "Producto actualizado exitosamente. <a href='products.php'>Volver a la lista</a>.";
                    $product_current_categories = $selected_categories_from_form;
                } else {
                    $error_msg = "Error al actualizar el producto: " . htmlspecialchars($stmt_update->error);
                    if(isset($stmt_update)) $stmt_update->close();
                }
            } else {
                 $error_msg = "Error al preparar la consulta de actualización: " . htmlspecialchars($mysqli->error);
            }
        } else {
            $product_current_categories = $selected_categories_from_form;
        }
    }
}

?>

<div class="container mt-3 products-page">
    <h2><?php echo $page_title; ?></h2>

    <?php 
    if(!empty($success_msg)) echo '<div class="alert alert-success">' . $success_msg . '</div>';
    if(!empty($error_msg) && (empty($success_msg) || (!isset($result) || $result->num_rows != 1)) ) {
        echo '<div class="alert alert-error">' . $error_msg . '</div>';
    }
    ?>

    <?php if (!empty($product_id) && ( (isset($result) && $result->num_rows == 1) || $_SERVER["REQUEST_METHOD"] == "POST" ) ) : ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo htmlspecialchars($product_id); ?>" method="post">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">

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
            <?php if (!empty($all_categories)): ?>
                <?php foreach ($all_categories as $category): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="product_categories[]" value="<?php echo $category['id']; ?>" id="cat_<?php echo $category['id']; ?>"
                            <?php
                            if (in_array($category['id'], $product_current_categories)) echo 'checked'; 
                            ?>>
                        <label class="form-check-label" for="cat_<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay categorías disponibles. Puedes <a href="add_category.php" target="_blank">añadir una nueva</a>.</p>
            <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Actualizar Producto">
            <a href="products.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
    <?php elseif (!empty($error_msg)): ?>
        <p><a href="products.php" class="btn btn-secondary">Volver a la lista de productos</a></p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>