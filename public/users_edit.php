<?php
$page_title = "Editar Usuario";
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container mt-3'><div class='alert alert-error'>Acceso denegado. Debes ser administrador para acceder a esta página.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo "<div class='container mt-3'><div class='alert alert-error'>ID de usuario no válido.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$user_id = $_GET['id'];

$username = $role = '';
$error_msg = $success_msg = '';

$sql = "SELECT username, role FROM users WHERE id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role);
    
    if (!$stmt->fetch()) {
        echo "<div class='container mt-3'><div class='alert alert-error'>Usuario no encontrado.</div></div>";
        require_once '../includes/footer.php';
        exit;
    }

    $stmt->close();
} else {
    echo "<div class='container mt-3'><div class='alert alert-error'>Error al obtener datos del usuario.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    
    if (empty($new_username)) {
        $error_msg = 'El nombre de usuario no puede estar vacío.';
    } else {
        $sql_check = "SELECT id FROM users WHERE username = ? AND id != ?";
        
        if ($stmt_check = $mysqli->prepare($sql_check)) {
            $stmt_check->bind_param("si", $new_username, $user_id);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $error_msg = 'El nombre de usuario ya está en uso.';
            }

            $stmt_check->close();
        }
    }
    if (empty($error_msg)) {
        $sql_update = "UPDATE users SET username = ?, role = ? WHERE id = ?";
        
        if ($stmt_update = $mysqli->prepare($sql_update)) {
            $stmt_update->bind_param("ssi", $new_username, $new_role, $user_id);
            
            if ($stmt_update->execute()) {
                $_SESSION['message'] = 'Usuario actualizado correctamente.';
                $_SESSION['message_type'] = 'success';
                header('Location: users.php');
                exit;
            } else {
                $error_msg = 'Error al actualizar el usuario.';
            }

            $stmt_update->close();
        } else {
            $error_msg = 'Error al preparar la consulta de actualización.';
        }
    }  
     
    $username = $new_username;
    $role = $new_role;
}
?>
<div class="container mt-3">
    <h2><?php echo $page_title; ?></h2>
    <?php if (!empty($error_msg)) echo '<div class="alert alert-error">' . $error_msg . '</div>'; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $user_id); ?>" method="post">
        <div class="form-group">
            <label for="username">Nombre de Usuario <span style="color:red;">*</span></label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="role">Rol <span style="color:red;">*</span></label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" <?php if ($role === 'user') echo 'selected'; ?>>Usuario</option>
                <option value="admin" <?php if ($role === 'admin') echo 'selected'; ?>>Administrador</option>
            </select>
        </div>
        <div class="form-group mt-3">
            <input type="submit" class="btn btn-primary" value="Actualizar Usuario">
            <a href="users.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?> 