<?php
// Inicia la sesión si aún no está iniciada. Esto es crucial para el carrito basado en sesiones
// y para mostrar mensajes temporales.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Recuperar y limpiar el mensaje de la sesión si existe.
$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Eliminar el mensaje después de mostrarlo para que no se repita.
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($producto['nombre']) ?> | Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir tus estilos personalizados para un mejor diseño -->
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Tienda Virtual</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=carrito">Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-md-2" href="admin/index.php?action=login">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <a href="index.php" class="btn btn-secondary mb-3">← Volver a la tienda</a>

        <?php
        // Mostrar mensaje de éxito o error si existe.
        if (isset($message)):
            $alertClass = ($message['type'] === 'success') ? 'alert-success' : 'alert-danger';
        ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message['text']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4 shadow">
            <div class="row g-0">
                <div class="col-md-5">
                    <?php if ($producto['foto']): ?>
                        <img src="imagenes/<?= htmlspecialchars($producto['foto']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                    <?php else: ?>
                        <div class="p-5 text-center">Sin imagen</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h3>
                        <p class="text-muted">Categoría: <?= htmlspecialchars($producto['categoria']) ?></p>
                        <h4 class="text-success">$<?= number_format($producto['precio'], 2) ?></h4>
                        
                        <?php if (!empty($producto['descripcion'])): ?>
                            <h5 class="mt-4">Descripción:</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
                        <?php else: ?>
                            <p class="card-text mt-3">No hay descripción disponible para este producto.</p>
                        <?php endif; ?>

                        <p class="card-text mt-3 text-muted">Este producto es parte del catálogo de nuestra tienda virtual. Puedes contactarnos para más detalles o realizar tu pedido.</p>

                        <!-- Formulario para añadir al carrito en la página de detalle -->
                        <form action="index.php?action=agregar_al_carrito&id=<?= htmlspecialchars($producto['id']) ?>" method="POST" class="d-flex align-items-center mt-3">
                            <input type="number" name="cantidad" value="1" min="1" class="form-control me-2" style="width: 100px;">
                            <button type="submit" class="btn btn-primary">Añadir al Carrito</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opcional: Incluir scripts JS si tienes alguno para esta página -->
    <script src="../assets/js/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>