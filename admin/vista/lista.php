<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        img { width: 80px; height: auto; }
        a.op { margin: 0 5px; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Productos Registrados</h1>
    <a href="index.php?action=registrar">Registrar Nuevo Producto</a><br><br>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Categoría</th>
                <th>Descripcion</th>
                <th>Operaciones</th> <!-- NUEVA COLUMNA -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $prod): ?>
                <tr>
                    <td><?= $prod['id'] ?></td>
                    <td>
                        <?php if ($prod['foto']): ?>
                            <img src="../imagenes/<?= htmlspecialchars($prod['foto']) ?>" alt="foto">
                        <?php else: ?>
                            <em>Sin imagen</em>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($prod['nombre']) ?></td>
                    <td>$<?= number_format($prod['precio'], 2) ?></td>
                    <td><?= htmlspecialchars($prod['categoria']) ?></td>
                    <td><?= htmlspecialchars($prod['descripcion']) ?></td>
                    <td>
                        <a class="op" href="index.php?action=editar&id=<?= $prod['id'] ?>">✏️ Editar</a>
                        <a class="op" href="index.php?action=eliminar&id=<?= $prod['id'] ?>"
                           onclick="return confirm('¿Estás seguro de eliminar este producto?');">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
