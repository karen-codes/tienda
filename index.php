<?php
require_once "config/conexion.php";
$pdo = Conexion::conectar();

// Obtener productos agrupados por categoría
$sql = "SELECT p.*, c.nombre AS categoria 
        FROM productos p 
        JOIN categoria c ON p.categoria_id = c.id 
        ORDER BY c.nombre, p.nombre";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar productos por categoría
$agrupados = [];
foreach ($productos as $prod) {
    $agrupados[$prod['categoria']][] = $prod;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda Virtual</title>
    <!-- Bootstrap 5 desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Tienda Virtual</a>
            <a class="btn btn-outline-light" href="admin/index.php?action=login">Acceso Admin</a>
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
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <footer class="bg-light text-center py-3 mt-4">
        <p class="mb-0">© <?= date('Y') ?> Mi Tienda Virtual</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>