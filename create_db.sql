-- Crear la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS tienda;
USE tienda;

-- Tabla de categorías
CREATE TABLE categoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Tabla de productos con clave foránea y campo para imagen
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    foto VARCHAR(255),
    categoria_id INT NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categoria(id)
);

-- Poblado inciial de categorías
INSERT INTO categoria (nombre) VALUES
('Tecnología'),
('Moda'),
('Hogar'),
('Libros');