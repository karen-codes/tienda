// assets/js/funciones.js

document.addEventListener("DOMContentLoaded", function () {
    console.log("JS personalizado cargado correctamente.");

    // Función para mostrar una alerta temporal (ej. para mensajes de éxito/error)
    // NOTA: Esta función requiere que tengas estilos CSS para las clases .alert, .alert-success, .alert-danger, etc.
    // Puedes usar los estilos que te proporcioné en estilos.css o integrar Bootstrap CSS.
    function showTemporaryAlert(message, type = 'success', duration = 3000) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} fixed-top text-center py-2`; // Requiere estilos CSS para .alert
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            /* Colores básicos, puedes ajustarlos con tu paleta de colores o clases de Bootstrap */
            background-color: ${type === 'success' ? '#d4edda' : '#f8d7da'};
            color: ${type === 'success' ? '#155724' : '#721c24'};
            border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
        `;
        alertDiv.textContent = message;
        document.body.appendChild(alertDiv);

        // Fade in
        setTimeout(() => alertDiv.style.opacity = '1', 10);

        // Fade out and remove
        setTimeout(() => {
            alertDiv.style.opacity = '0';
            alertDiv.addEventListener('transitionend', () => alertDiv.remove());
        }, duration);
    }

    // Ejemplo de uso de la alerta (puedes llamarlo desde tu PHP después de una operación,
    // pasando el mensaje en una variable de sesión o parámetro de URL, por ejemplo):
    // showTemporaryAlert('Producto guardado con éxito!', 'success');
    // showTemporaryAlert('Error al guardar el producto.', 'danger');

    // Puedes añadir más funciones JS personalizadas aquí.
    // No se incluye el código para manejar el evento de clic en los enlaces de eliminar,
    // ya que has indicado que prefieres mantener el 'onclick' directamente en el HTML.
});
