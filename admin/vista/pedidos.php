<?php
// Esta vista se incluirá dentro del layout de admin/index.php.
// Por lo tanto, no debe tener etiquetas <html>, <head>, <body>, etc.
// Solo el contenido de la sección principal.
?>
<div class="container-fluid">
    <h1 class="mb-4">Listado de Pedidos</h1>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info" role="alert">
            No hay pedidos registrados aún.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Correo</th>
                        <th>Total</th>
                        <th>Fecha Pedido</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?= htmlspecialchars($pedido['id']) ?></td>
                            <td><?= htmlspecialchars($pedido['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($pedido['correo_cliente']) ?></td>
                            <td>$<?= number_format($pedido['total'], 2) ?></td>
                            <td><?= htmlspecialchars($pedido['fecha_pedido']) ?></td>
                            <td>
                                <span class="badge 
                                    <?php 
                                        if ($pedido['estado'] == 'Pendiente') echo 'bg-warning text-dark';
                                        elseif ($pedido['estado'] == 'Procesando') echo 'bg-info';
                                        elseif ($pedido['estado'] == 'Enviado') echo 'bg-primary';
                                        elseif ($pedido['estado'] == 'Completado') echo 'bg-success';
                                        elseif ($pedido['estado'] == 'Cancelado') echo 'bg-danger';
                                        else echo 'bg-secondary';
                                    ?>">
                                    <?= htmlspecialchars($pedido['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?action=ver_detalle_pedido&id=<?= htmlspecialchars($pedido['id']) ?>" class="btn btn-sm btn-outline-primary">Ver Detalles</a>
                                <!-- Opcional: Botón para editar estado directamente aquí o un modal -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
