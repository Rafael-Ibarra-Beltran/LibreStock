<?php ?> 
    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>LibreStock</h3>
                    <p>Tu librería favorita en Tijuana, con el mejor catálogo de libros en español e inglés.</p>
                </div>
                <div class="footer-section">
                    <h3>Enlaces Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="products.php">Libros</a></li>
                        <li><a href="categories.php">Categorías</a></li>
                        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                            <li><a href="logout.php">Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Iniciar Sesión</a></li>
                            <li><a href="register.php">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <ul class="footer-contact">
                        <li><span class="contact-icon">&#127968;</span> Calle Segunda 123, Zona Centro, Tijuana, B.C.</li>
                        <li><span class="contact-icon">&#128231;</span> contacto@librestock.com</li>
                        <li><span class="contact-icon">&#128222;</span> (664) 123-4567</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> LibreStock. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    <?php
        if (isset($mysqli) && $mysqli instanceof mysqli) {
            $mysqli->close();
        }
    ?>
</body>
</html> 