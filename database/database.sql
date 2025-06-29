CREATE DATABASE IF NOT EXISTS librestock_db;
USE librestock_db;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_plain` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `publication_date` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_categories` (
  `product_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  PRIMARY KEY (`product_id`, `category_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `users` (`username`, `password_plain`, `role`) VALUES
('Admin', 'Adminpassword', 'admin'),
('User', 'Userpassword', 'user');

INSERT INTO `categories` (`name`) VALUES
('Programación'),
('Desarrollo Web'),
('Lenguajes de Programación'),
('Aprendizaje Automático'),
('Ciencia de Datos'),
('Bases de Datos'),
('Frameworks y Bibliotecas'),
('Buenas Prácticas de Software'),
('DevOps y Automatización'),
('Seguridad Informática');

INSERT INTO `products` (`title`, `author`, `description`, `price`, `stock_quantity`, `publication_date`) VALUES
('Python Crash Course, 3rd Edition', 'Eric Matthes', 'Guía práctica basada en proyectos para aprender Python 3, cubriendo VS Code, manejo de archivos con pathlib, pruebas con pytest y las versiones más recientes de Matplotlib, Plotly y Django.', 39.99, 40, '2023-01-10'),
('Effective TypeScript: 83 Specific Ways to Improve Your TypeScript', 'Dan Vanderkam', 'Recopila 83 recomendaciones concretas para mejorar tu uso de TypeScript, con mejores prácticas y técnicas de la versión actual, ejemplos claros y casos de uso en proyectos reales.', 45.00, 30, '2024-06-04'),
('Hands-On Machine Learning with Scikit-Learn, Keras, and TensorFlow, 3rd Edition', 'Aurélien Géron', 'Manual completo para construir sistemas inteligentes usando scikit-learn, Keras y TensorFlow; ejemplos prácticos, mínima teoría y enfoque en proyectos reales de aprendizaje automático.', 49.50, 20, '2022-11-08'),
('Clean Code in Python: Refactor Your Legacy Code and Architect Pythonic Applications', 'Mariano Anaya', 'Estrategias para refactorizar código en Python, diseñar aplicaciones "pythónicas" y aplicar buenas prácticas de ingeniería de software en proyectos existentes.', 37.50, 25, '2023-03-15'),
('Programming Rust, 2nd Edition', 'Jim Blandy y Jason Orendorff', 'Introducción avanzada a Rust 1.50+, cubre gestión de memoria sin recolector, concurrencia segura, tipos avanzados y ecosistema de crates recientes.', 54.00, 15, '2021-08-10'),
('Mastering Go, 3rd Edition', 'Mihalis Tsoukalos', 'Guía para desarrollar software concurrente y escalable con Go 1.18+, incluye módulos, testing, manejo de errores, patrones de diseño y despliegue en contenedores.', 42.75, 18, '2023-09-30'),
('Kubernetes Up & Running: Dive into the Future of Infrastructure, 3rd Edition', 'Kelsey Hightower, Brendan Burns y Joe Beda', 'Cobertura de Kubernetes 1.25+, despliegue, configuración de pods, servicios, operadores y mejores prácticas para entornos de producción.', 48.00, 22, '2023-04-01'),
('Fullstack TypeScript: Build Cloud-Ready Web Applications Using React and Node.js', 'Houssein Djirdeh', 'Tutorial paso a paso para crear aplicaciones web completas con TypeScript, React (incluyendo hooks y Redux Toolkit) y Node.js (Express), listo para desplegar en la nube.', 44.50, 28, '2023-07-20'),
('Kotlin in Action, 2nd Edition', 'Dmitry Jemerov y Svetlana Isakova', 'Cobertura de Kotlin 1.8+, diferencias con Java, programación orientada a objetos y funcional en Kotlin, desarrollo Android y corutinas.', 46.00, 16, '2023-05-07'),
('Mastering React: Build Modern Web Applications Using React and Redux', 'Adam Horton', 'Enfoque avanzado en React 18, Context API, Redux Toolkit y hooks personalizados para construir aplicaciones complejas y mantenibles.', 41.25, 19, '2023-11-10');

INSERT INTO `product_categories` (`product_id`, `category_id`) VALUES
(1, 1), (1, 3),
(2, 1), (2, 3), (2, 8),
(3, 1), (3, 4), (3, 5),
(4, 1), (4, 3), (4, 8),
(5, 1), (5, 3), (5, 8),
(6, 1), (6, 3),
(7, 1), (7, 9), (7, 7),
(8, 1), (8, 2), (8, 7),
(9, 1), (9, 3), (9, 2),
(10, 1), (10, 2), (10, 7);