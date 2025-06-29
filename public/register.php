<?php 
$page_title = "Registro de Usuario";
require_once '../includes/header.php';

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$registration_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor, ingresa un nombre de usuario.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            
            $param_username = trim($_POST["username"]);
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $username_err = "Este nombre de usuario ya está en uso.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "¡Ups! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }

            $stmt->close();
        }
    }
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingresa una contraseña.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor, confirma la contraseña.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }
    
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        
        $sql = "INSERT INTO users (username, password_plain, role) VALUES (?, ?, ?)"; 
         
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("sss", $param_username, $param_password_plain, $param_role);
            
            $param_username = $username;
            $param_password_plain = $password; 
            $param_role = 'user'; 
            
            if ($stmt->execute()) {
                $registration_success = "¡Registro exitoso! Ahora puedes <a href='login.php'>iniciar sesión</a>.";
                $username = $password = $confirm_password = ""; 
            } else {
                echo "¡Ups! Algo salió mal al registrar. Por favor, inténtalo de nuevo más tarde.";
            }

            $stmt->close();
        }
    }
    
    $mysqli->close(); 
}
?>

<div class="container mt-3 register-page">
    <?php 
    if(!empty($registration_success)){
        echo '<div class="alert alert-success">' . $registration_success . '</div>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="register-form">
        <h2>Registro de Usuario</h2>
        <p>Por favor, completa este formulario para crear una cuenta.</p>
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
            <label>Confirmar Contraseña</label>
            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Registrarse">
            <input type="reset" class="btn btn-secondary" value="Limpiar">
        </div>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?> 