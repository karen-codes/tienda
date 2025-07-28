<?php
// Inicia la sesión si aún no está iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Recuperar y limpiar el mensaje de la sesión si existe
$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Eliminar el mensaje después de mostrarlo
}

// Las variables $nombre, $correo, $asunto, $mensaje y $errors
// se pasan desde el controlador (contacto)
// Si no están definidas, se inicializan a vacío para evitar errores de "undefined variable".
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['correo'] ?? '';
$asunto = $_POST['asunto'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto | Tienda Virtual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/tienda/assets/css/estilos.css"> <!-- CAMBIO: Ruta con prefijo /tienda/ -->
    
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/tienda/">Tienda Virtual</a> <!-- CAMBIO: Enlace a la raíz de la aplicación -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/tienda/">Inicio</a> <!-- CAMBIO: Enlace a la raíz de la aplicación -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/tienda/contacto">Contacto</a> <!-- CAMBIO: Enlace amigable con prefijo -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/tienda/carrito">Carrito</a> <!-- CAMBIO: Enlace amigable con prefijo -->
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-md-2" href="admin/index.php?action=login">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <a href="/tienda/" class="btn btn-secondary mb-3">← Volver a la tienda</a> <!-- CAMBIO: Enlace a la raíz de la aplicación -->

        <div class="contact-form-container">
            <h1>Contáctanos</h1>
            <p class="text-center text-muted mb-4">Envíanos un mensaje y te responderemos a la brevedad posible.</p>

            <?php
            // Mostrar mensaje de éxito o error si existen
            if (isset($message)) {
                $alertClass = ($message['type'] === 'success') ? 'alert-success' : 'alert-danger';
                echo '<div class="alert ' . $alertClass . '">' . htmlspecialchars($message['text']) . '</div>';
            }
            ?>

            <form action="/tienda/contacto" method="POST"> <!-- CAMBIO: Acción amigable con prefijo -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Tu Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required
                           value="<?= htmlspecialchars($nombre) ?>">
                    <?php if (isset($errors['nombre'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['nombre']) ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Tu Correo Electrónico:</label>
                    <input type="email" class="form-control" id="correo" name="correo" required
                           value="<?= htmlspecialchars($correo) ?>">
                    <?php if (isset($errors['correo'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['correo']) ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="asunto" class="form-label">Asunto:</label>
                    <input type="text" class="form-control" id="asunto" name="asunto"
                           value="<?= htmlspecialchars($asunto) ?>">
                    <?php if (isset($errors['asunto'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['asunto']) ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="mensaje" class="form-label">Tu Mensaje:</label>
                    <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required><?= htmlspecialchars($mensaje) ?></textarea>
                    <?php if (isset($errors['mensaje'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['mensaje']) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
            </form>
        </div>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <p class="mb-0">© <?= date('Y') ?> Tienda Virtual</p>
    </footer>

    <!-- JS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu archivo JS personalizado -->
    <script src="/tienda/assets/js/funciones.js"></script> <!-- CAMBIO: Ruta con prefijo /tienda/ -->
</body>
</html>
