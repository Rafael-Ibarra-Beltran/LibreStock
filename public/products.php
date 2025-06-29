<?php
$page_title = "Gestión de Productos";
require_once '../includes/header.php';

$search_query = $_GET['q'] ?? '';
$filter_category_id = $_GET['filter_category_id'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'p.title'; 
$sort_dir = $_GET['sort_dir'] ?? 'ASC';  

$all_filter_categories = [];
$sql_get_filter_categories = "SELECT id, name FROM categories ORDER BY name ASC";

if ($result_filter_cats = $mysqli->query($sql_get_filter_categories)) {
    while ($cat_row = $result_filter_cats->fetch_assoc()) {
        $all_filter_categories[] = $cat_row;
    }

    $result_filter_cats->free();
} else {
    echo "<div class='container mt-3'><div class='alert alert-error'>Error al obtener las categorías para el filtro: " . htmlspecialchars($mysqli->error) . "</div></div>";
}


$sql_base = "SELECT p.id, p.title, p.author, p.price, p.stock_quantity, p.publication_date, 
                  GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories_list
           FROM products p
           LEFT JOIN product_categories pc ON p.id = pc.product_id
           LEFT JOIN categories c ON pc.category_id = c.id";

$where_clauses = [];
$params = [];
$types = '';

if (!empty($search_query)) {
    $where_clauses[] = "(p.title LIKE ? OR p.author LIKE ? OR p.description LIKE ?)";
    $search_param = "%{$search_query}%";
    array_push($params, $search_param, $search_param, $search_param);
    $types .= 'sss';
}

if (!empty($filter_category_id) && ctype_digit($filter_category_id)) {
    $sql_base .= " JOIN product_categories pc_filter ON p.id = pc_filter.product_id"; 
    $where_clauses[] = "pc_filter.category_id = ?";
    $params[] = $filter_category_id;
    $types .= 'i';
}

$sql_final = $sql_base;
if (!empty($where_clauses)) {
    $sql_final .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql_final .= " GROUP BY p.id";

$allowed_sort_fields = ['p.title', 'p.author', 'p.price', 'p.stock_quantity', 'p.publication_date'];
$allowed_sort_dirs = ['ASC', 'DESC'];
if (in_array($sort_by, $allowed_sort_fields) && in_array(strtoupper($sort_dir), $allowed_sort_dirs)) {
    $sql_final .= " ORDER BY {$sort_by} " . strtoupper($sort_dir);
} else {
    $sql_final .= " ORDER BY p.title ASC"; 
}


$products = [];
$low_stock_products_exist = false; 

if ($stmt = $mysqli->prepare($sql_final)) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
                
                if ($row['stock_quantity'] <= LOW_STOCK_THRESHOLD) {
                    $low_stock_products_exist = true;
                }
            }
        }

        $stmt->close();
    } else {
        echo "<div class='container mt-3'><div class='alert alert-error'>Error al ejecutar la consulta de productos: " . htmlspecialchars($stmt->error) . "</div></div>";
    }
} else {
    echo "<div class='container mt-3'><div class='alert alert-error'>Error al preparar la consulta de productos: " . htmlspecialchars($mysqli->error) . "</div></div>";
}

$is_admin = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>

<div class="container mt-3 products-page">
    <?php
    if (isset($_SESSION['message'])) {
        $message_type_class = 'alert-info'; 
        
        if (isset($_SESSION['message_type'])) {
            if ($_SESSION['message_type'] === 'success') {
                $message_type_class = 'alert-success';
            } elseif ($_SESSION['message_type'] === 'error') {
                $message_type_class = 'alert-error';
            }
        }
        
        echo '<div class="alert ' . $message_type_class . '" role="alert">';
        echo htmlspecialchars($_SESSION['message']);
        echo '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }

    if ($is_admin && $low_stock_products_exist) {
        echo '<div class="alert alert-warning" role="alert">¡Atención! Hay productos con bajo stock (cantidad menor o igual a ' . LOW_STOCK_THRESHOLD . ' unidades).</div>';
    }
    ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?php echo $page_title; ?></h1>
        <?php if ($is_admin): ?>
            <a href="add_product.php" class="btn btn-primary">Añadir Nuevo Producto</a>
        <?php endif; ?>
    </div>

    <div class="search-card mb-4">
        <form action="products.php" method="GET" class="mb-0 search-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="q" class="form-label">Buscar Producto</label>
                    <input type="text" class="form-control" id="q" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Título, autor, descripción...">
                </div>
                <div class="col-md-3">
                    <label for="filter_category_id" class="form-label">Filtrar por Categoría</label>
                    <select class="form-select" id="filter_category_id" name="filter_category_id">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($all_filter_categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($filter_category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort_by" class="form-label">Ordenar por</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="p.title" <?php echo ($sort_by == 'p.title') ? 'selected' : ''; ?>>Título</option>
                        <option value="p.author" <?php echo ($sort_by == 'p.author') ? 'selected' : ''; ?>>Autor</option>
                        <option value="p.price" <?php echo ($sort_by == 'p.price') ? 'selected' : ''; ?>>Precio</option>
                        <option value="p.stock_quantity" <?php echo ($sort_by == 'p.stock_quantity') ? 'selected' : ''; ?>>Stock</option>
                        <option value="p.publication_date" <?php echo ($sort_by == 'p.publication_date') ? 'selected' : ''; ?>>Fecha Pub.</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort_dir" class="form-label">Dirección</label>
                    <select class="form-select" id="sort_dir" name="sort_dir">
                        <option value="ASC" <?php echo (strtoupper($sort_dir) == 'ASC') ? 'selected' : ''; ?>>Ascendente</option>
                        <option value="DESC" <?php echo (strtoupper($sort_dir) == 'DESC') ? 'selected' : ''; ?>>Descendente</option>
                    </select>
                </div>
                <div class="col-md-auto align-self-end">
                    <button type="submit" class="btn btn-primary btn-search w-100">Buscar</button>
                </div>
            </div>
        </form>
    </div> 

    <div class="results-card mb-4">
        <?php if (!empty($products)): ?>
        <table class="table products-table mb-0">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categorías</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Publicación</th>
                    <?php if ($is_admin): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): 
                    $row_class = '';
                    $stock_display_class = '';
                    if ($product['stock_quantity'] <= LOW_STOCK_THRESHOLD) {
                        $row_class = 'table-warning'; 
                        $stock_display_class = 'text-danger fw-bold'; 
                    }
                ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><?php echo htmlspecialchars($product['title']); ?></td>
                        <td><?php echo htmlspecialchars($product['author'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($product['categories_list'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(number_format($product['price'], 2)); ?> $</td>
                        <td class="<?php echo $stock_display_class; ?>"><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                        <td><?php echo $product['publication_date'] ? htmlspecialchars(date("d/m/Y", strtotime($product['publication_date']))) : 'N/D'; ?></td>
                        <?php if ($is_admin): ?>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info mb-0">
                No se encontraron productos que coincidan con tus criterios de búsqueda/filtro.
                <?php if ($is_admin && empty($search_query) && empty($filter_category_id)) { echo ' Puedes <a href="add_product.php">añadir uno nuevo</a>.'; } ?>
            </div>
        <?php endif; ?>
    </div> 

</div>

<?php 
require_once '../includes/footer.php'; 
?> 