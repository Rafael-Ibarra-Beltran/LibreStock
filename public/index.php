<?php
$page_title = "Bienvenido a LibreStock";
require_once '../includes/header.php';

$company_info = [
    [
        'icon' => '&#127968;',
        'title' => 'Ubicación Estratégica',
        'text' => 'Calle Segunda 123, Zona Centro, Tijuana, Baja California, México.'
    ],
    [
        'icon' => '&#128218;',
        'title' => 'Amplio Catálogo',
        'text' => 'Más de 50,000 títulos disponibles, incluyendo libros en español e inglés, perfectos para nuestra comunidad binacional.'
    ],
    [
        'icon' => '&#128187;',
        'title' => 'Experiencia Digital',
        'text' => 'Explora nuestro catálogo en línea y encuentra tu próximo libro favorito de manera fácil y rápida.'
    ]
];

$testimonials = [
    [
        'name' => 'Danna Barrón',
        'role' => 'Lectora Frecuente',
        'text' => 'LibreStock tiene una selección increíble de libros. Siempre encuentro lo que busco y el ambiente es acogedor.',
        'image' => '../images/foto-perfil1.webp'
    ],
    [
        'name' => 'Jorge Sandoval',
        'role' => 'Estudiante',
        'text' => 'La mejor librería de Tijuana. Tienen libros en español e inglés, perfecto para mis estudios.',
        'image' => '../images/foto-perfil2.webp'
    ],
    [
        'name' => 'Elizabeth Castillo',
        'role' => 'Escritora',
        'text' => 'Un espacio maravilloso para encontrar inspiración. Su colección de literatura es impresionante.',
        'image' => '../images/foto-perfil3.webp'
    ]
];
?>

<?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <div class="container mt-3">
        <div class="welcome-section">
            <div class="welcome-header text-center mb-4">
                <h1>¡Bienvenido de nuevo, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
                <p class="lead">Tu librería favorita en Tijuana</p>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="quick-actions-card">
                        <h3>Acciones Rápidas</h3>
                        <div class="quick-actions-grid">
                            <a href="products.php" class="quick-action-item">
                                <span class="action-icon">&#128218;</span>
                                <span class="action-text">Explorar Libros</span>
                            </a>
                            <a href="categories.php" class="quick-action-item">
                                <span class="action-icon">&#128193;</span>
                                <span class="action-text">Ver Categorías</span>
                            </a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="add_product.php" class="quick-action-item">
                                    <span class="action-icon">&#10133;</span>
                                    <span class="action-text">Añadir Libro</span>
                                </a>
                                <a href="add_category.php" class="quick-action-item">
                                    <span class="action-icon">&#128221;</span>
                                    <span class="action-text">Añadir Categoría</span>
                                </a>
                                <a href="users.php" class="quick-action-item">
                                    <span class="action-icon">&#128100;</span>
                                    <span class="action-text">Ver Usuarios</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="info-card">
                        <h3>Información de la Librería</h3>
                        <div class="info-content">
                            <div class="info-item">
                                <span class="info-icon">&#127968;</span>
                                <div class="info-text">
                                    <h4>Ubicación</h4>
                                    <p>Calle Segunda 123, Zona Centro, Tijuana, B.C.</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-icon">&#128222;</span>
                                <div class="info-text">
                                    <h4>Contacto</h4>
                                    <p>(664) 123-4567</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-icon">&#128231;</span>
                                <div class="info-text">
                                    <h4>Email</h4>
                                    <p>contacto@librestock.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <?php
                    $total_products = 0;
                    $total_categories = 0;
                    $low_stock_products_count = 0;

                    if ($result = $mysqli->query("SELECT COUNT(*) AS count FROM products")) {
                        $total_products = $result->fetch_assoc()['count'];
                        $result->free();
                    }

                    if ($result = $mysqli->query("SELECT COUNT(*) AS count FROM categories")) {
                        $total_categories = $result->fetch_assoc()['count'];
                        $result->free();
                    }
                    
                    $sql_low_stock = "SELECT COUNT(*) AS count FROM products WHERE stock_quantity <= ?";
                    if ($stmt = $mysqli->prepare($sql_low_stock)) {
                        $low_stock_threshold_value = LOW_STOCK_THRESHOLD;
                        $stmt->bind_param("i", $low_stock_threshold_value);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $low_stock_products_count = $result->fetch_assoc()['count'];
                        $stmt->close();
                    }
                ?>
                <div class="stats-section mb-5">
                    <h3 class="text-center mb-4">Estadísticas del Inventario</h3>
                    <div class="row mb-4 d-flex align-items-stretch">
                        <div class="col-md-4 mb-3">
                            <div class="card text-white bg-primary h-100">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                    <h4 class="card-title display-4"><?php echo $total_products; ?></h4>
                                    <p class="card-text">Productos registrados</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-white bg-info h-100">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                    <h4 class="card-title display-4"><?php echo $total_categories; ?></h4>
                                    <p class="card-text">Categorías disponibles</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card <?php echo ($low_stock_products_count > 0) ? 'bg-warning text-dark' : 'bg-success text-white'; ?> h-100">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                    <h4 class="card-title display-4"><?php echo $low_stock_products_count; ?></h4>
                                    <p class="card-text">Productos con bajo stock</p>
                                    <?php if ($low_stock_products_count > 0): ?>
                                        <a href="products.php?sort_by=p.stock_quantity&sort_dir=ASC" class="btn btn-sm <?php echo ($low_stock_products_count > 0) ? 'btn-dark' : 'btn-light'; ?> mt-2">Ver Productos</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-5 mb-4">
                <a href="logout.php" class="btn btn-logout">
                    <span class="btn-icon">&#128274;</span>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">LibreStock: Tu Librería en Tijuana</h1>
            <p class="lead my-4">Descubre un mundo de historias y conocimiento.<br>Tu librería favorita en la frontera.</p>
            <div class="hero-buttons">
                <a href="products.php" class="btn btn-primary btn-lg me-3">
                    <span class="btn-icon">&#128218;</span>
                    Explorar Libros
                </a>
                <a href="register.php" class="btn btn-light btn-lg">
                    <span class="btn-icon">&#128100;</span>
                    Únete a Nosotros
                </a>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <h2 class="text-center mb-5">¿Por Qué Elegir LibreStock?</h2>
            <div class="row">
                <?php foreach ($company_info as $info): ?>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon"><?php echo $info['icon']; ?></div>
                        <h3><?php echo htmlspecialchars($info['title']); ?></h3>
                        <p><?php echo htmlspecialchars($info['text']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <div class="container">
            <h2 class="text-center mb-5">Lo Que Dicen Nuestros Lectores</h2>
            <div class="row">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-image">
                            <img src="<?php echo PROJECT_BASE_URL; ?>images/<?php echo basename(htmlspecialchars($testimonial['image'])); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                        </div>
                        <div class="testimonial-content">
                            <p class="testimonial-text"><?php echo htmlspecialchars($testimonial['text']); ?></p>
                            <div class="testimonial-author">
                                <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                                <p class="role"><?php echo htmlspecialchars($testimonial['role']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
                    
    <section class="cta-section text-center">
        <div class="container">
            <h2 class="mb-4">¿Listo para Descubrir Nuevas Historias?</h2>
            <p class="lead mb-4">Únete a nuestra comunidad de lectores y encuentra tu próximo libro favorito.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-light btn-lg me-3">
                    <span class="btn-icon">&#128100;</span>
                    Crear Cuenta
                </a>
                <a href="login.php" class="btn btn-outline-light btn-lg">
                    <span class="btn-icon">&#128274;</span>
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
