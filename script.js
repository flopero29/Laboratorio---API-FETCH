// URL base para las peticiones
const API_URL = 'registrar.php';

// Estado de la aplicacion
let editMode = false;

// Inicializacion
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema de productos inicializado');
    cargarProductos();
    inicializarEventListeners();
});

// Configurar event listeners
function inicializarEventListeners() {
    // Formulario submit
    document.getElementById('formProducto').addEventListener('submit', manejarSubmit);
    
    // Botones
    document.getElementById('btnBuscar').addEventListener('click', buscarProducto);
    document.getElementById('btnRefrescar').addEventListener('click', cargarProductos);
    document.getElementById('btnCancelar').addEventListener('click', cancelarEdicion);
    document.getElementById('btnActualizar').addEventListener('click', function() {
        document.getElementById('formProducto').dispatchEvent(new Event('submit'));
    });
    
    // Enter en busqueda
    document.getElementById('codigoBuscar').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarProducto();
        }
    });
}

// Manejar envio del formulario
async function manejarSubmit(e) {
    e.preventDefault();
    console.log('Enviando formulario...');
    
    const formData = new FormData(document.getElementById('formProducto'));
    
    if (editMode) {
        formData.append('Accion', 'Modificar');
        await enviarPeticion(formData, 'Modificar');
    } else {
        formData.append('Accion', 'Guardar');
        await enviarPeticion(formData, 'Guardar');
    }
}

// Funcion principal para enviar peticiones Fetch
async function enviarPeticion(formData, accion) {
    try {
        mostrarCargando(`Procesando ${accion.toLowerCase()}...`);

        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        // Verificar si la respuesta es JSON valido
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Respuesta no JSON:', text);
            throw new Error('Respuesta del servidor no valida');
        }

        console.log('Respuesta recibida:', data);

        if (data.success) {
            await mostrarExito(data.message);
            limpiarFormulario();
            cargarProductos();
            
            if (accion === 'Modificar') {
                cancelarEdicion();
            }
        } else {
            await mostrarError(data.message, data.errors);
        }

    } catch (error) {
        console.error('Error:', error);
        await mostrarError('Error de conexion: ' + error.message);
    }
}

// Buscar producto por codigo
async function buscarProducto() {
    const codigo = document.getElementById('codigoBuscar').value.trim();
    
    if (!codigo) {
        await mostrarError('Por favor ingrese un codigo para buscar');
        return;
    }

    try {
        mostrarCargando('Buscando producto...');

        const formData = new FormData();
        formData.append('Accion', 'Buscar');
        formData.append('codigo_buscar', codigo);

        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Respuesta no JSON:', text);
            throw new Error('Respuesta del servidor no valida');
        }

        if (data.success) {
            await mostrarExito(data.message);
            cargarProductoEnFormulario(data.producto);
        } else {
            await mostrarError(data.message, data.errors);
            document.getElementById('codigoBuscar').value = '';
        }

    } catch (error) {
        console.error('Error:', error);
        await mostrarError('Error al buscar producto: ' + error.message);
    }
}

// Cargar lista de productos
async function cargarProductos() {
    try {
        const formData = new FormData();
        formData.append('Accion', 'Listar');

        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Respuesta no JSON:', text);
            document.getElementById('tablaProductos').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error en formato de respuesta</td></tr>';
            return;
        }

        if (data.success) {
            mostrarProductosEnTabla(data.productos);
        } else {
            document.getElementById('tablaProductos').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar productos: ' + data.message + '</td></tr>';
        }

    } catch (error) {
        console.error('Error:', error);
        document.getElementById('tablaProductos').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error de conexion</td></tr>';
    }
}

// Mostrar productos en la tabla
function mostrarProductosEnTabla(productos) {
    if (!productos || productos.length === 0) {
        document.getElementById('tablaProductos').innerHTML = '<tr><td colspan="6" class="text-center">No hay productos registrados</td></tr>';
        return;
    }

    const html = productos.map(producto => `
        <tr>
            <td>${producto.id}</td>
            <td>${escapeHtml(producto.codigo)}</td>
            <td>${escapeHtml(producto.producto)}</td>
            <td>$${parseFloat(producto.precio).toFixed(2)}</td>
            <td>${producto.cantidad}</td>
            <td>
                <button class="btn btn-warning btn-sm btn-action" onclick="editarProducto(${producto.id})">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-danger btn-sm btn-action" onclick="eliminarProducto(${producto.id})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </td>
        </tr>
    `).join('');

    document.getElementById('tablaProductos').innerHTML = html;
}

// Cargar producto en formulario para editar
function cargarProductoEnFormulario(producto) {
    document.getElementById('productoId').value = producto.id;
    document.getElementById('codigo').value = producto.codigo;
    document.getElementById('producto').value = producto.producto;
    document.getElementById('precio').value = producto.precio;
    document.getElementById('cantidad').value = producto.cantidad;
    
    activarModoEdicion();
}

// Activar modo edicion
function activarModoEdicion() {
    editMode = true;
    document.getElementById('btnGuardar').style.display = 'none';
    document.getElementById('btnActualizar').style.display = 'inline-block';
    document.getElementById('btnCancelar').style.display = 'inline-block';
    
    document.getElementById('codigo').focus();
}

// Cancelar edicion
function cancelarEdicion() {
    editMode = false;
    limpiarFormulario();
    document.getElementById('btnGuardar').style.display = 'inline-block';
    document.getElementById('btnActualizar').style.display = 'none';
    document.getElementById('btnCancelar').style.display = 'none';
}

// Limpiar formulario
function limpiarFormulario() {
    document.getElementById('formProducto').reset();
    document.getElementById('productoId').value = '';
}

// Editar producto (llamado desde la tabla)
async function editarProducto(id) {
    try {
        // Buscar el producto por ID
        const formData = new FormData();
        formData.append('Accion', 'Buscar');
        formData.append('codigo_buscar', await obtenerCodigoPorId(id));

        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Respuesta del servidor no valida');
        }

        if (data.success) {
            cargarProductoEnFormulario(data.producto);
            await mostrarInfo('Producto cargado para edicion');
        } else {
            await mostrarError('Error al cargar producto para edicion');
        }

    } catch (error) {
        console.error('Error:', error);
        await mostrarError('Error al cargar producto: ' + error.message);
    }
}

// Obtener codigo por ID (helper para edicion)
async function obtenerCodigoPorId(id) {
    try {
        const formData = new FormData();
        formData.append('Accion', 'Listar');

        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            return '';
        }

        if (data.success) {
            const producto = data.productos.find(p => p.id == id);
            return producto ? producto.codigo : '';
        }
        return '';
    } catch (error) {
        console.error('Error:', error);
        return '';
    }
}

// Eliminar producto
async function eliminarProducto(id) {
    const result = await Swal.fire({
        title: 'Estas seguro?',
        text: "No podras revertir esta accion!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        await Swal.fire({
            title: 'Funcion en desarrollo',
            text: 'La eliminacion estara disponible en la siguiente version',
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    }
}

// Utilidades para SweetAlert2
function mostrarCargando(mensaje) {
    Swal.fire({
        title: mensaje,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function mostrarExito(mensaje) {
    return Swal.fire({
        title: 'Exito!',
        text: mensaje,
        icon: 'success',
        confirmButtonText: 'Aceptar',
        timer: 2000
    });
}

function mostrarError(mensaje, errores = null) {
    let html = mensaje;
    
    if (errores && Object.keys(errores).length > 0) {
        html += '<ul class="text-start mt-2">';
        for (const [campo, error] of Object.entries(errores)) {
            html += `<li><strong>${campo}:</strong> ${error}</li>`;
        }
        html += '</ul>';
    }
    
    return Swal.fire({
        title: 'Error',
        html: html,
        icon: 'error',
        confirmButtonText: 'Entendido'
    });
}

function mostrarInfo(mensaje) {
    return Swal.fire({
        title: 'Informacion',
        text: mensaje,
        icon: 'info',
        confirmButtonText: 'Aceptar',
        timer: 1500
    });
}

// Utilidad para escapar HTML (seguridad)
function escapeHtml(unsafe) {
    if (typeof unsafe !== 'string') return unsafe;
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Cargar productos cada 30 segundos (opcional)
setInterval(cargarProductos, 30000);