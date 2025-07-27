<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda Virtual</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS personalizado -->
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
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
                        <a class="nav-link" href="index.php?action=contacto">Contacto</a> <!-- NUEVO: Enlace a Contacto -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=carrito">Carrito</a> <!-- NUEVO: Enlace a Carrito -->
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-md-2" href="admin/index.php?action=login">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php foreach ($agrupados as $categoria => $items): ?>
            <h2 class="text-primary mt-4 card-title"><?= htmlspecialchars($categoria) ?></h2> <!-- Añadido card-title para estilo -->
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($item['foto']): ?>
                                <img src="imagenes/<?= htmlspecialchars($item['foto']) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($item['nombre']) ?>">
                            <?php else: ?>
                                <!-- Placeholder para cuando no hay imagen, con estilos Bootstrap -->
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-muted" style="height: 200px;">
                                    Sin imagen
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['nombre']) ?></h5>
                                <p class="card-text">$<?= number_format($item['precio'], 2) ?></p>
                                <a href="index.php?action=detalle&id=<?= htmlspecialchars($item['id']) ?>" class="btn btn-sm btn-outline-primary">Ver más</a>
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
    <script src="assets/js/funciones.js"></script>
</body>

</html>
