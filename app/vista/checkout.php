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

// Calcular el total del carrito para mostrarlo en el checkout
$total_carrito = 0;
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_carrito += $item['precio'] * $item['cantidad'];
    }
}

// Las variables $nombreCliente, $correoCliente, $direccionEnvio, $metodoPago y $errors
// se pasan desde el controlador (finalizar_compra o procesar_compra)
// Si no están definidas, se inicializan a vacío para evitar errores de "undefined variable".
$nombreCliente = $nombreCliente ?? '';
$correoCliente = $correoCliente ?? '';
$direccionEnvio = $direccionEnvio ?? '';
$metodoPago = $metodoPago ?? '';
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra | Tienda Virtual</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS personalizado -->
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
                        <a class="nav-link" href="/tienda/contacto">Contacto</a> <!-- CAMBIO: Enlace amigable con prefijo -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/tienda/carrito">Carrito</a> <!-- CAMBIO: Enlace amigable con prefijo -->
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-md-2" href="admin/index.php?action=login">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <a href="/tienda/carrito" class="btn btn-secondary mb-3">← Volver al Carrito</a> <!-- CAMBIO: Enlace amigable con prefijo -->

        <div class="checkout-form-container">
            <h1>Confirmar Compra</h1>
            <p class="text-center text-muted mb-4">Por favor, complete sus datos para finalizar el pedido.</p>

            <?php
            // Mostrar mensaje de éxito o error si existen
            if (isset($message)) {
                $alertClass = ($message['type'] === 'success') ? 'alert-success' : 'alert-danger';
                echo '<div class="alert ' . $alertClass . '">' . htmlspecialchars($message['text']) . '</div>';
            }
            ?>

            <div class="order-summary">
                <h5>Resumen del Pedido</h5>
                <p>Total del Carrito: <strong class="text-primary">$<?= number_format($total_carrito, 2) ?></strong></p>
                <!-- Aquí podrías listar los productos del carrito si lo deseas, pero para simplicidad, solo el total. -->
            </div>

            <form action="/tienda/procesar_compra" method="POST"> <!-- CAMBIO: Acción amigable con prefijo -->
                <div class="mb-3">
                    <label for="nombre_cliente" class="form-label">Nombre Completo:</label>
                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required
                           value="<?= htmlspecialchars($nombreCliente) ?>">
                    <?php if (isset($errors['nombre_cliente'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['nombre_cliente']) ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="correo_cliente" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="correo_cliente" name="correo_cliente" required
                           value="<?= htmlspecialchars($correoCliente) ?>">
                    <?php if (isset($errors['correo_cliente'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['correo_cliente']) ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="direccion_envio" class="form-label">Dirección de Envío:</label>
                    <textarea class="form-control" id="direccion_envio" name="direccion_envio" rows="3" required><?= htmlspecialchars($direccionEnvio) ?></textarea>
                    <?php if (isset($errors['direccion_envio'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['direccion_envio']) ?></div><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago:</label>
                    <div class="payment-methods">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metodo_pago" id="pagoTarjeta" value="tarjeta"
                                <?= ($metodoPago == 'tarjeta') ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="pagoTarjeta">
                                Tarjeta de Crédito/Débito (Simulado)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metodo_pago" id="pagoPaypal" value="paypal"
                                <?= ($metodoPago == 'paypal') ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="pagoPaypal">
                                PayPal (Simulado)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metodo_pago" id="pagoTransferencia" value="transferencia"
                                <?= ($metodoPago == 'transferencia') ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="pagoTransferencia">
                                Transferencia Bancaria (Simulado)
                            </label>
                        </div>
                    </div>
                    <?php if (isset($errors['metodo_pago'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['metodo_pago']) ?></div><?php endif; ?>
                </div>

                <button type="submit" class="btn btn-success">Confirmar y Pagar</button>
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