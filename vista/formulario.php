<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Producto</title>
</head>

<body>
    <h1>Registrar Producto</h1>

    <form method="POST" action="index.php?action=registrar" enctype="multipart/form-data">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Precio:</label><br>
        <input type="number" name="precio" step="0.01" required><br><br>

        <label>Foto del producto:</label><br>
        <input type="file" name="foto" accept="image/*"><br><br>

        <label>Categoría:</label><br>
        <select name="categoria_id" required>
            <option value="">Seleccione una categoría</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" value="Guardar Producto">
    </form>
</body>

</html>