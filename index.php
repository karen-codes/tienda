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
</head>
<body>
    <h1>Bienvenido a la Tienda</h1>

    <?php foreach ($agrupados as $categoria => $items): ?>
        <h2><?= htmlspecialchars($categoria) ?></h2>
        <ul>
            <?php foreach ($items as $item): ?>
                <li>
                    <?= htmlspecialchars($item['nombre']) ?> - 
                    $<?= number_format($item['precio'], 2) ?>
                    <?php if ($item['foto']): ?>
                        <br><img src="imagenes/<?= $item['foto'] ?>" width="100">
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

    <hr>
    <a href="admin/index.php?action=login">Acceder como Administrador</a>
</body>
</html>
