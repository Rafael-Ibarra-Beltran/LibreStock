<?php
$page_title = "Añadir Usuario";
require_once '../includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container mt-3'><div class='alert alert-error'>Acceso denegado. Debes ser administrador para acceder a esta página.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$username = $role = '';
$error_msg = $success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    
    if (empty($username) || empty($password)) {
        $error_msg = 'El nombre de usuario y la contraseña son obligatorios.';
    } else {        
        $sql_check = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt_check = $mysqli->prepare($sql_check)) {
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $error_msg = 'El nombre de usuario ya está en uso.';
            }

            $stmt_check->close();
        }
    }

    if (empty($error_msg)) {
        $sql_insert = "INSERT INTO users (username, password_plain, role) VALUES (?, ?, ?)";
        
        if ($stmt_insert = $mysqli->prepare($sql_insert)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert->bind_param("sss", $username, $hashed_password, $role);
            
            if ($stmt_insert->execute()) {
                $_SESSION['message'] = 'Usuario añadido correctamente.';
                $_SESSION['message_type'] = 'success';
                header('Location: users.php');
                exit;
            } else {
                $error_msg = 'Error al añadir el usuario.';
            }
            
            $stmt_insert->close();
        } else {
            $error_msg = 'Error al preparar la consulta de inserción.';
        }
    }
}
?>
<div class="container mt-3">
    <h2><?php echo $page_title; ?></h2>
    <?php if (!empty($error_msg)) echo '<div class="alert alert-error">' . $error_msg . '</div>'; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="username">Nombre de Usuario <span style="color:red;">*</span></label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña <span style="color:red;">*</span></label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="role">Rol <span style="color:red;">*</span></label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" <?php if ($role === 'user') echo 'selected'; ?>>Usuario</option>
                <option value="admin" <?php if ($role === 'admin') echo 'selected'; ?>>Administrador</option>
            </select>
        </div>
        <div class="form-group mt-3">
            <input type="submit" class="btn btn-primary" value="Añadir Usuario">
            <a href="users.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?> 