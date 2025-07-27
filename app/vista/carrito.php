<?php
// Inicia la sesión si aún no está iniciada. Esto es crucial para el carrito basado en sesiones.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se asume que el carrito se almacenará en $_SESSION['carrito']
// Si no existe, se inicializa como un array vacío.
$carrito = $_SESSION['carrito'] ?? [];

$total_carrito = 0;
foreach ($carrito as $item) {
    $total_carrito += $item['precio'] * $item['cantidad'];
}

// Recuperar y limpiar el mensaje de la sesión si existe (para mensajes de éxito/error)
$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Eliminar el mensaje después de mostrarlo
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito de Compras | Tienda Virtual</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS personalizado -->
    <link rel="stylesheet" href="/tienda/assets/css/estilos.css">
    <style>
        /* Estilos específicos para la página del carrito */
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .table-cart th, .table-cart td {
            vertical-align: middle;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
        }
    </style>
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
        <h1>Mi Carrito de Compras</h1>
        <a href="/tienda/" class="btn btn-secondary mb-3">← Seguir comprando</a> <!-- CAMBIO: Enlace a la raíz de la aplicación -->

        <?php
        // Mostrar mensaje de éxito o error si existe
        if (isset($message)):
            $alertClass = ($message['type'] === 'success') ? 'alert-success' : 'alert-danger';
        ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message['text']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($carrito)): ?>
            <div class="alert alert-info text-center" role="alert">
                Tu carrito está vacío. ¡Añade algunos productos!
            </div>
        <?php else: ?>
            <form action="/tienda/actualizar_carrito" method="POST"> <!-- CAMBIO: Acción amigable con prefijo -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-cart">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carrito as $id_producto => $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['foto']): ?>
                                            <img src="/tienda/imagenes/<?= htmlspecialchars($item['foto']) ?>" class="cart-item-img" alt="<?= htmlspecialchars($item['nombre']) ?>">
                                        <?php else: ?>
                                            <div class="cart-item-img d-flex align-items-center justify-content-center bg-light text-muted small">Sin imagen</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td>$<?= number_format($item['precio'], 2) ?></td>
                                    <td>
                                        <input type="number" name="cantidad[<?= $id_producto ?>]" value="<?= htmlspecialchars($item['cantidad']) ?>" min="1" class="form-control quantity-input">
                                    </td>
                                    <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                                    <td>
                                        <a href="/tienda/eliminar_del_carrito?id=<?= htmlspecialchars($id_producto) ?>" class="btn btn-danger btn-sm">Eliminar</a> <!-- CAMBIO: Enlace amigable con prefijo -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>$<?= number_format($total_carrito, 2) ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-info">Actualizar Carrito</button>
                    <a href="/tienda/finalizar_compra" class="btn btn-success">Finalizar Compra</a> <!-- CAMBIO: Enlace amigable con prefijo -->
                </div>
            </form>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                <a href="/tienda/vaciar_carrito" class="btn btn-warning">Vaciar Carrito</a> <!-- CAMBIO: Enlace amigable con prefijo -->
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <p class="mb-0">© <?= date('Y') ?> Tienda Virtual</p>
    </footer>

    <!-- JS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu archivo JS personalizado -->
    <script src="/tienda/assets/js/funciones.js"></script>
</body>
</html>
