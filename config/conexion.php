<?php

class Conexion {
    public static function conectar() {
        // Obtener las credenciales de la base de datos desde las variables de entorno
        // Usamos $_ENV directamente ya que getenv() a veces no funciona consistentemente
        // en algunos entornos de servidor web sin configuraciones adicionales.
        $db_host = $_ENV['DB_HOST'] ?? 'localhost'; // Fallback a localhost si no se encuentra
        $db_name = $_ENV['DB_NAME'] ?? 'tienda';     // Fallback a 'tienda'
        $db_user = $_ENV['DB_USER'] ?? 'root';       // Fallback a 'root'
        $db_pass = $_ENV['DB_PASS'] ?? '';           // Fallback a vacío

        try {
            $pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("SET NAMES 'utf8mb4'"); // Asegura el soporte de UTF-8
            return $pdo;
        } catch (PDOException $e) {
            // En un entorno de producción, no mostrarías el mensaje de error directamente al usuario.
            // En su lugar, lo registrarías en un archivo de log.
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        }
    }
}