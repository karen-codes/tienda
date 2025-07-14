<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda Virtual</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS -->
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Tienda Virtual</span>
            <a class="btn btn-outline-light" href="admin/index.php?action=login">Admin</a>
        </div>
    </nav>

    <div class="container">
        <?php foreach ($agrupados as $categoria => $items): ?>
            <h2 class="text-primary mt-4"><?= htmlspecialchars($categoria) ?></h2>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($item['foto']): ?>
                                <img src="imagenes/<?= $item['foto'] ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($item['nombre']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['nombre']) ?></h5>
                                <p class="card-text">$<?= number_format($item['precio'], 2) ?></p>
                                <a href="index.php?action=detalle&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">Ver más</a>
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

    <!-- Tu archivo JS -->
    <script src="assets/js/funciones.js"></script>
</body>

</html>