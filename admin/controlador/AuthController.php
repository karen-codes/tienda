<?php
require_once APP_PATH . "/admin/modelo/UsuarioDAO.php";

class AuthController {
    public function login() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = $_POST['correo'];
            $contrasena = $_POST['contrasena'];

            $dao = new UsuarioDAO();
            $usuario = $dao->autenticar($correo, $contrasena);

            if ($usuario) {
                session_start();
                $_SESSION['usuario'] = $usuario['correo'];
                header("Location: index.php?action=listar");
                exit();
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        }

        include "vista/login.php";
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
