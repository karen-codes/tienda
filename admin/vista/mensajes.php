<?php
// Esta vista se incluirá dentro del layout de admin/index.php,
// por lo que no necesita las etiquetas <html>, <head>, <body>, etc.
// Solo el contenido de la sección principal.
?>
<div class="container-fluid">
    <h1 class="mb-4">Mensajes de Contacto</h1>

    <?php if (empty($mensajes)): ?>
        <div class="alert alert-info" role="alert">
            No hay mensajes de contacto registrados.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Asunto</th>
                        <th>Mensaje</th>
                        <th>Fecha de Envío</th>
                        <!-- Puedes añadir una columna para acciones futuras (ej. ver detalle, eliminar) -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mensajes as $mensaje): ?>
                        <tr>
                            <td><?= htmlspecialchars($mensaje['id']) ?></td>
                            <td><?= htmlspecialchars($mensaje['nombre']) ?></td>
                            <td><?= htmlspecialchars($mensaje['correo']) ?></td>
                            <td><?= htmlspecialchars($mensaje['asunto']) ?></td>
                            <td><?= nl2br(htmlspecialchars(substr($mensaje['mensaje'], 0, 100))) ?><?= (strlen($mensaje['mensaje']) > 100) ? '...' : '' ?></td>
                            <td><?= htmlspecialchars($mensaje['fecha_envio']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>