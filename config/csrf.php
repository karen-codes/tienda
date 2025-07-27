<?php
// Asegura que la sesión esté iniciada para poder usar $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define el nombre de la clave de sesión para el token CSRF
if (!defined('CSRF_TOKEN_NAME')) {
    define('CSRF_TOKEN_NAME', 'csrf_token');
}

/**
 * Genera un nuevo token CSRF y lo almacena en la sesión.
 * @return string El token CSRF generado.
 */
function generate_csrf_token() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        // Genera un token aleatorio seguro
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Valida un token CSRF enviado en una solicitud POST.
 * @param string $token El token recibido de la solicitud POST.
 * @return bool True si el token es válido, false en caso contrario.
 */
function validate_csrf_token($token) {
    // Comprueba si el token de la sesión existe y coincide con el token enviado
    if (isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token)) {
        // Una vez validado, el token debe ser destruido para prevenir ataques de "re-uso" (one-time token)
        unset($_SESSION[CSRF_TOKEN_NAME]);
        return true;
    }
    return false;
}

/**
 * Obtiene el token CSRF actual de la sesión.
 * Si no existe, genera uno nuevo.
 * @return string El token CSRF actual.
 */
function get_csrf_token() {
    return generate_csrf_token();
}

/**
 * Imprime un campo oculto de formulario con el token CSRF.
 */
function csrf_field() {
    echo '<input type="hidden" name="' . htmlspecialchars(CSRF_TOKEN_NAME) . '" value="' . htmlspecialchars(get_csrf_token()) . '">';
}
