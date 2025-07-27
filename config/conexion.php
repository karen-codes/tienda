<?php
class Conexion {
    public static function conectar() {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=tienda", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }
}