<?php
// Esta vista se incluirá dentro del layout de admin/index.php.
// Por lo tanto, no debe tener etiquetas <html>, <head>, <body>, etc.
// Solo el contenido de la sección principal.
?>
<div class="container-fluid">
    <h1 class="mb-4">Detalles del Pedido #<?= htmlspecialchars($pedidoInfo['id']) ?></h1>

    <a href="index.php?action=pedidos" class="btn btn-secondary mb-3">← Volver a Listado de Pedidos</a>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Información del Pedido</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($pedidoInfo['nombre_cliente']) ?></p>
                    <p><strong>Correo:</strong> <?= htmlspecialchars($pedidoInfo['correo_cliente']) ?></p>
                    <p><strong>Dirección de Envío:</strong> <?= nl2br(htmlspecialchars($pedidoInfo['direccion_envio'])) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha del Pedido:</strong> <?= htmlspecialchars($pedidoInfo['fecha_pedido']) ?></p>
                    <p><strong>Total del Pedido:</strong> <strong class="text-success">$<?= number_format($pedidoInfo['total'], 2) ?></strong></p>
                    <p>
                        <strong>Estado:</strong> 
                        <span class="badge 
                            <?php 
                                if ($pedidoInfo['estado'] == 'Pendiente') echo 'bg-warning text-dark';
                                elseif ($pedidoInfo['estado'] == 'Procesando') echo 'bg-info';
                                elseif ($pedidoInfo['estado'] == 'Enviado') echo 'bg-primary';
                                elseif ($pedidoInfo['estado'] == 'Completado') echo 'bg-success';
                                elseif ($pedidoInfo['estado'] == 'Cancelado') echo 'bg-danger';
                                else echo 'bg-secondary';
                            ?>">
                            <?= htmlspecialchars($pedidoInfo['estado']) ?>
                        </span>
                    </p>
                    <!-- Formulario para actualizar el estado del pedido -->
                    <form action="index.php?action=actualizar_estado_pedido" method="POST" class="mt-3 d-flex align-items-center">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($pedidoInfo['id']) ?>">
                        <?php csrf_field(); // Incluye el token CSRF ?>
                        <label for="estado" class="form-label me-2 mb-0">Cambiar Estado:</label>
                        <select name="estado" id="estado" class="form-select form-select-sm me-2" style="max-width: 150px;">
                            <option value="Pendiente" <?= ($pedidoInfo['estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                            <option value="Procesando" <?= ($pedidoInfo['estado'] == 'Procesando') ? 'selected' : '' ?>>Procesando</option>
                            <option value="Enviado" <?= ($pedidoInfo['estado'] == 'Enviado') ? 'selected' : '' ?>>Enviado</option>
                            <option value="Completado" <?= ($pedidoInfo['estado'] == 'Completado') ? 'selected' : '' ?>>Completado</option>
                            <option value="Cancelado" <?= ($pedidoInfo['estado'] == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Productos en el Pedido</h5>
        </div>
        <div class="card-body">
            <?php if (empty($pedidoDetalles)): ?>
                <div class="alert alert-warning" role="alert">
                    No hay productos detallados para este pedido.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidoDetalles as $detalle): ?>
                                <tr>
                                    <td><?= htmlspecialchars($detalle['nombre_producto']) ?></td>
                                    <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                                    <td><?= htmlspecialchars($detalle['cantidad']) ?></td>
                                    <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
