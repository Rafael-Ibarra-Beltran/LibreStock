<?php
$page_title = "Iniciar Sesión";
require_once '../includes/header.php';

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor, ingresa tu nombre de usuario.";
    }

    $username = trim($_POST["username"]);
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingresa tu contraseña.";
    }

    $password = trim($_POST["password"]);
    
    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password_plain, role FROM users WHERE username = ?";
        
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username_db, $password_db_plain, $role);
                   
                    if ($stmt->fetch()) {
                        if ($password === $password_db_plain) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $id;
                            $_SESSION["username"] = $username_db;
                            $_SESSION["role"] = $role;                            
                            
                            if ($role === 'admin') {
                                header("location: index.php"); 
                            } else {
                                header("location: index.php");
                            }
                            
                            exit; 
                        } else {        
                            $login_err = "Nombre de usuario o contraseña incorrectos.";
                        }
                    }
                } else {
                    $login_err = "Nombre de usuario o contraseña incorrectos.";
                }
            } else {
                echo "¡Ups! Algo salió mal al iniciar sesión. Por favor, inténtalo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
}
?>

<div class="container mt-3 register-page">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="register-form">
        <h2>Iniciar Sesión</h2>
        <p>Por favor, ingresa tus credenciales para iniciar sesión.</p>
        <?php 
        if (!empty($login_err)) {
            echo '<div class="alert alert-error">' . $login_err . '</div>';
        }
        ?>
        <div class="form-group">
            <label>Nombre de Usuario</label>
            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>    
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Iniciar Sesión">
        </div>
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>.</p>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?> 