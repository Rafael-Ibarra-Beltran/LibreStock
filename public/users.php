<?php
$page_title = "Gestión de Usuarios";
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container mt-3'><div class='alert alert-error'>Acceso denegado. Debes ser administrador para acceder a esta página.</div></div>";
    require_once '../includes/footer.php';
    exit;
}
    
$sql = "SELECT id, username, role, created_at FROM users ORDER BY created_at DESC";
$users = [];
if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $result->free();
    }
} else {
    echo "<div class='container mt-3'><div class='alert alert-error'>Error al obtener los usuarios: " . htmlspecialchars($mysqli->error) . "</div></div>";
}
?>

<div class="container mt-3 users-page">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?php echo $page_title; ?></h1>
        <a href="users_add.php" class="btn btn-primary">Añadir Usuario</a>
    </div>

    <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($user['created_at']))); ?></td>
                            <td>
                                <a href="users_edit.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
                                <a href="users_delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay usuarios para mostrar en este momento.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?> 