<?php
define('PROJECT_BASE_URL', 'http://localhost/LibreStock/');
define('SITE_URL', PROJECT_BASE_URL . 'public/');

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); 
define('DB_NAME', 'librestock_db');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
    die("ERROR: No se pudo conectar a la base de datos. " . $mysqli->connect_error);
}

if (!$mysqli->set_charset("utf8mb4")) {
    printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", $mysqli->error);
} else {
    
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('LOW_STOCK_THRESHOLD', 5);
?> 