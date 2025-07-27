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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda Virtual</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS personalizado -->
    <link rel="stylesheet" href="/tienda/assets/css/estilos.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/tienda/">Tienda Virtual</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/tienda/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/tienda/contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/tienda/carrito">Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-md-2" href="admin/index.php?action=login">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
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

        <?php foreach ($agrupados as $categoria => $items): ?>
            <h2 class="text-primary mt-4 card-title"><?= htmlspecialchars($categoria) ?></h2>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($item['foto']): ?>
                                <img src="/tienda/imagenes/<?= htmlspecialchars($item['foto']) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($item['nombre']) ?>">
                            <?php else: ?>
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-muted" style="height: 200px;">
                                    Sin imagen
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['nombre']) ?></h5>
                                <p class="card-text">$<?= number_format($item['precio'], 2) ?></p>
                                
                                <a href="/tienda/detalle/<?= htmlspecialchars($item['id']) ?>" class="btn btn-sm btn-outline-primary mb-2">Ver más</a>
                                
                                <!-- CAMBIO CRÍTICO: La acción del formulario debe ser una ruta absoluta -->
                                <form action="/tienda/agregar_al_carrito" method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>"> <!-- Añadimos el ID como campo oculto -->
                                    <input type="number" name="cantidad" value="1" min="1" class="form-control form-control-sm me-2" style="width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-success">Añadir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
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