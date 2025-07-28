<?php
// Este archivo se incluye dentro de admin/index.php, por lo que no necesita
// iniciar sesión ni definir APP_PATH nuevamente.

// Recuperar y limpiar el mensaje de la sesión si existe
$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Eliminar el mensaje después de mostrarlo
}
?>

<h1 class="mb-4">Productos Registrados</h1>

<?php
// Mostrar mensaje de éxito o error si existe
if (isset($message)):
    $alertClass = ($message['type'] === 'success') ? 'alert-success' : 'alert-danger';
?>
    <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message['text']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

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
                    <th>Descripción</th>
                    <th>Operaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['id']) ?></td>
                        <td>
                            <?php if ($producto['foto']): ?>
                                <!-- CAMBIO CRÍTICO: Usar ruta relativa para la imagen desde admin/ -->
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
                            <!-- CAMBIO: Añadimos un contenedor flex para los botones y clases de margen/ancho -->
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="index.php?action=editar&id=<?= htmlspecialchars($producto['id']) ?>" class="btn btn-sm btn-info flex-fill"> ✏️ Editar</a>
                                <a href="index.php?action=eliminar&id=<?= htmlspecialchars($producto['id']) ?>" class="btn btn-sm btn-danger flex-fill" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');"> 🗑️ Eliminar</a>
                            </div>
                           <!-- <a href="index.php?action=editar&id=<?= htmlspecialchars($producto['id']) ?>" class="btn btn-sm btn-info op"> ✏️ Editar</a> -->
                           <!--<a href="index.php?action=eliminar&id=<?= htmlspecialchars($producto['id']) ?>" class="btn btn-sm btn-danger op" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');"> 🗑️ Eliminar</a> -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

