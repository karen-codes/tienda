<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <!-- Incluir CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS personalizado para el admin -->
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        /* Estilos adicionales para el formulario */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea, /* Estilo para el textarea */
        select {
            width: calc(100% - 22px); /* Ajuste para padding y borde */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"],
        .btn-cancel { /* Estilo para el botón de cancelar */
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            display: inline-block; /* Para que los botones estén en la misma línea */
            width: auto;
            text-decoration: none; /* Para el enlace */
            text-align: center; /* Para el enlace */
        }
        input[type="submit"]:hover,
        .btn-cancel:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
            display: block;
        }
        .current-photo {
            margin-bottom: 15px;
        }
        .current-photo img {
            max-width: 150px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Producto</h1>

        <!-- Mostrar mensajes de error si existen -->
        <?php if (isset($errors) && !empty($errors)): ?>
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px; max-width: 600px; margin: 20px auto;">
                <strong>Errores de validación:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=editar" enctype="multipart/form-data">
            <!-- Campo oculto para el token CSRF -->
            <?php csrf_field(); ?>

            <input type="hidden" name="id" value="<?= htmlspecialchars($producto['id']) ?>">

            <label for="nombre">Nombre:</label><br>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? $producto['nombre']) ?>" required maxlength="100"><br>
            <?php if (isset($errors['nombre'])): ?><span class="error"><?= htmlspecialchars($errors['nombre']) ?></span><br><?php endif; ?>
            <br>

            <label for="precio">Precio:</label><br>
            <input type="number" id="precio" name="precio" step="0.01" value="<?= htmlspecialchars($_POST['precio'] ?? $producto['precio']) ?>" required min="0.01"><br>
            <?php if (isset($errors['precio'])): ?><span class="error"><?= htmlspecialchars($errors['precio']) ?></span><br><?php endif; ?>
            <br>

            <div class="form-group current-photo">
                <label>Foto actual:</label><br>
                <?php if ($producto['foto']): ?>
                    <img src="../imagenes/<?= htmlspecialchars($producto['foto']) ?>" alt="Foto actual del producto"><br>
                <?php else: ?>
                    <em>Sin imagen</em><br>
                <?php endif; ?>
            </div>
            <label for="foto">Nueva foto (opcional):</label><br>
            <input type="file" id="foto" name="foto" accept="image/*"><br>
            <?php if (isset($errors['foto'])): ?><span class="error"><?= htmlspecialchars($errors['foto']) ?></span><br><?php endif; ?>
            <br>

            <label for="categoria_id">Categoría:</label><br>
            <select id="categoria_id" name="categoria_id" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id']) ?>"
                        <?= (isset($_POST['categoria_id']) ? $_POST['categoria_id'] : $producto['categoria_id']) == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
            <?php if (isset($errors['categoria_id'])): ?><span class="error"><?= htmlspecialchars($errors['categoria_id']) ?></span><br><?php endif; ?>
            <br>

            <label for="descripcion">Descripción:</label><br>
            <textarea id="descripcion" name="descripcion" rows="5" maxlength="500"><?= htmlspecialchars($_POST['descripcion'] ?? $producto['descripcion']) ?></textarea><br>
            <?php if (isset($errors['descripcion'])): ?><span class="error"><?= htmlspecialchars($errors['descripcion']) ?></span><br><?php endif; ?>
            <br>

            <input type="submit" value="Actualizar Producto">
            <a href="index.php?action=listar" class="btn btn-secondary btn-cancel">Cancelar</a>
        </form>
    </div>
    <!-- JS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu archivo JS personalizado -->
    <script src="../assets/js/funciones.js"></script>
</body>
</html>

