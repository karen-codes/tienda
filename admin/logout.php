<?php
// Inicia la sesión si aún no está iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destruye todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
// Nota: Esto destruirá la sesión, y no solo los datos de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión.
session_destroy();

// Redirige al usuario a la página de login del administrador.
header("Location: index.php?action=login");
exit();
?>
