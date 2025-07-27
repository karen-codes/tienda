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

    <div class="container py-4">
        <a href="index.php" class="btn btn-secondary mb-3">← Volver a la tienda</a>

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
                        
                        <!-- NUEVO: Mostrar la descripción del producto -->
                        <?php if (!empty($producto['descripcion'])): ?>
                            <h5 class="mt-4">Descripción:</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
                        <?php else: ?>
                            <p class="card-text mt-3">No hay descripción disponible para este producto.</p>
                        <?php endif; ?>

                        <p class="card-text mt-3 text-muted">Este producto es parte del catálogo de nuestra tienda virtual. Puedes contactarnos para más detalles o realizar tu pedido.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opcional: Incluir scripts JS si tienes alguno para esta página -->
    <script src="../assets/js/funciones.js"></script>
</body>
</html>
