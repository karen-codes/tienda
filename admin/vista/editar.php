<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
</head>
<body>
    <h1>Editar Producto</h1>

    <form method="POST" action="index.php?action=editar" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">

        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required><br><br>

        <label>Precio:</label><br>
        <input type="number" name="precio" step="0.01" value="<?= $producto['precio'] ?>" required><br><br>

        <label>Foto actual:</label><br>
        <?php if ($producto['foto']): ?>
            <img src="../imagenes/<?= htmlspecialchars($producto['foto']) ?>" width="100"><br>
        <?php else: ?>
            <em>Sin imagen</em><br>
        <?php endif; ?>
        <label>Nueva foto (opcional):</label><br>
        <input type="file" name="foto" accept="image/*"><br><br>

        <label>Categoría:</label><br>
        <select name="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= $producto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" value="Actualizar Producto">
        <a href="index.php?action=listar">Cancelar</a>
    </form>
</body>
</html>
