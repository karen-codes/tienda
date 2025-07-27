<?php
// Esta vista se incluirá dentro del layout de admin/index.php.
// Por lo tanto, no debe tener etiquetas <html>, <head>, <body>, etc.
// Solo el contenido de la sección principal.
?>
<div class="container-fluid">
    <h1 class="mb-4">Registrar Producto</h1>

    <!-- Mostrar mensajes de error si existen -->
    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Errores de validación:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=registrar" enctype="multipart/form-data">
        <!-- Campo oculto para el token CSRF -->
        <?php csrf_field(); ?>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required maxlength="100">
            <?php if (isset($errors['nombre'])): ?><span class="text-danger small mt-1"><?= htmlspecialchars($errors['nombre']) ?></span><?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" class="form-control" value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>" required min="0.01">
            <?php if (isset($errors['precio'])): ?><span class="text-danger small mt-1"><?= htmlspecialchars($errors['precio']) ?></span><?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto del producto:</label>
            <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
            <?php if (isset($errors['foto'])): ?><span class="text-danger small mt-1"><?= htmlspecialchars($errors['foto']) ?></span><?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría:</label>
            <select id="categoria_id" name="categoria_id" class="form-select" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id']) ?>"
                        <?= (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['categoria_id'])): ?><span class="text-danger small mt-1"><?= htmlspecialchars($errors['categoria_id']) ?></span><?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="5" class="form-control" maxlength="500"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
            <?php if (isset($errors['descripcion'])): ?><span class="text-danger small mt-1"><?= htmlspecialchars($errors['descripcion']) ?></span><?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <a href="index.php?action=listar" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

