<?php
// Esta vista se incluirá dentro del layout de admin/index.php.
// Por lo tanto, no debe tener etiquetas <html>, <head>, <body>, etc.
// Solo el contenido de la sección principal.
?>
<div class="container-fluid">
    <h1 class="mb-4">Productos Registrados</h1>

    <!-- Mostrar mensaje de éxito o error si existe -->
    <?php
    // El mensaje de sesión ya se maneja en admin/index.php, pero si tuvieras un mensaje específico aquí, podrías mostrarlo.
    // if (isset($message)):
    //     $alertClass = ($message['type'] === 'success') ? 'alert-success' : 'alert-danger';
    ?>
        <!-- <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> -->
    <?php // endif; ?>

    <a href="index.php?action=registrar" class="btn btn-primary mb-3">Registrar Nuevo Producto</a>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info" role="alert">
            No hay productos registrados.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Descripción</th> <!-- Asegúrate de que esta columna exista en tu consulta SQL -->
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['id']) ?></td>
                            <td>
                                <?php if ($producto['foto']): ?>
                                    <img src="../imagenes/<?= htmlspecialchars($producto['foto']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" width="80" height="80" style="object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <span class="text-muted small">Sin foto</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>$<?= number_format($producto['precio'], 2) ?></td>
                            <td><?= htmlspecialchars($producto['categoria']) ?></td>
                            <td><?= nl2br(htmlspecialchars(substr($producto['descripcion'] ?? '', 0, 100))) ?><?= (strlen($producto['descripcion'] ?? '') > 100) ? '...' : '' ?></td>
                            <td>
                                <a href="index.php?action=editar&id=<?= htmlspecialchars($producto['id']) ?>" class="btn btn-sm btn-info op">Editar</a>
                                <a href="index.php?action=eliminar&id=<?= htmlspecialchars($producto['id']) ?>" class="btn btn-sm btn-danger op" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
