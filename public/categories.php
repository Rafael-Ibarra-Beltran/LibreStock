<?php
$page_title = "Gestión de Categorías";
require_once '../includes/header.php';

$sql = "SELECT id, name, created_at FROM categories ORDER BY name ASC";
$categories = [];

if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        $result->free();
    }
} else {
    echo "<div class='container mt-3'><div class='alert alert-error'>Error al obtener las categorías: " . htmlspecialchars($mysqli->error) . "</div></div>";
}

$is_admin = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>

<div class="container mt-3 categories-page">
    <?php       
    if (isset($_SESSION['message'])) {
        $message_type_class = 'alert-info'; 
        
        if (isset($_SESSION['message_type'])) {
            if ($_SESSION['message_type'] === 'success') $message_type_class = 'alert-success';
            elseif ($_SESSION['message_type'] === 'error') $message_type_class = 'alert-error';
        }

        echo '<div class="alert ' . $message_type_class . '" role="alert">';
        echo htmlspecialchars($_SESSION['message']);
        echo '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?php echo $page_title; ?></h1>
        <?php if ($is_admin): ?>
            <a href="add_category.php" class="btn btn-primary">Añadir Nueva Categoría</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>ID</th>
                        <th>Nombre de la Categoría</th>
                        <th>Fecha de Creación</th>
                        <?php if ($is_admin): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($category['created_at']))); ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
                                    <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta categoría? Esto también la desvinculará de cualquier producto asociado.');">Eliminar</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay categorías para mostrar en este momento. <?php if ($is_admin) { echo 'Puedes <a href="add_category.php">añadir una nueva</a>.'; } ?></div>
    <?php endif; ?>

</div>

<?php require_once '../includes/footer.php'; ?> 