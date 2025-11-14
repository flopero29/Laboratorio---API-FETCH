<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Respuesta por defecto
$response = [
    "success" => false,
    "message" => "Accion no valida",
    "accion" => "",
    "errors" => []
];

try {
    // Verificar que los archivos existen
    if (!file_exists('Modelo/conexion.php') || !file_exists('Modelo/Productos.php')) {
        throw new Exception("Archivos de modelo no encontrados");
    }

    include_once 'Modelo/conexion.php';
    include_once 'Modelo/Productos.php';

    // Obtener JSON input si viene como raw data
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $jsonData = json_decode($input, true);
        if ($jsonData) {
            $_POST = array_merge($_POST, $jsonData);
        }
    }

    // Obtener la accion
    $accion = isset($_POST['Accion']) ? $_POST['Accion'] : (isset($_GET['Accion']) ? $_GET['Accion'] : '');

    // Log para debugging (remover en produccion)
    error_log("Accion recibida: " . $accion);

    if (empty($accion)) {
        $response["message"] = "No se recibio accion";
        echo json_encode($response);
        exit;
    }

    // Crear instancia del producto
    $producto = new Producto();

    switch ($accion) {
        case "Guardar":
            $producto->cargarDatos($_POST);
            
            if ($producto->validate()) {
                if ($producto->guardar()) {
                    $response = [
                        "success" => true,
                        "message" => "Producto creado exitosamente",
                        "accion" => "Guardar",
                        "errors" => []
                    ];
                } else {
                    $response = [
                        "success" => false,
                        "message" => "Error al guardar el producto en la base de datos",
                        "accion" => "Guardar",
                        "errors" => $producto->getErrors()
                    ];
                }
            } else {
                $response = [
                    "success" => false,
                    "message" => "Errores de validacion",
                    "accion" => "Guardar",
                    "errors" => $producto->getErrors()
                ];
            }
            break;

        case "Modificar":
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                $response = [
                    "success" => false,
                    "message" => "ID del producto no proporcionado",
                    "accion" => "Modificar",
                    "errors" => ["id" => "ID requerido para modificar"]
                ];
                break;
            }

            $producto->cargarDatos($_POST);
            
            if ($producto->validate()) {
                if ($producto->actualizar()) {
                    $response = [
                        "success" => true,
                        "message" => "Producto actualizado exitosamente",
                        "accion" => "Modificar",
                        "errors" => []
                    ];
                } else {
                    $response = [
                        "success" => false,
                        "message" => "Error al actualizar el producto",
                        "accion" => "Modificar",
                        "errors" => $producto->getErrors()
                    ];
                }
            } else {
                $response = [
                    "success" => false,
                    "message" => "Errores de validacion",
                    "accion" => "Modificar",
                    "errors" => $producto->getErrors()
                ];
            }
            break;

        case "Buscar":
            $codigo = isset($_POST['codigo_buscar']) ? trim($_POST['codigo_buscar']) : '';
            
            if (empty($codigo)) {
                $response = [
                    "success" => false,
                    "message" => "Ingrese un codigo para buscar",
                    "accion" => "Buscar",
                    "errors" => ["codigo_buscar" => "Codigo requerido"]
                ];
                break;
            }

            $productoEncontrado = $producto->buscarPorCodigo($codigo);
            
            if ($productoEncontrado) {
                $response = [
                    "success" => true,
                    "message" => "Producto encontrado",
                    "accion" => "Buscar",
                    "producto" => $productoEncontrado,
                    "errors" => []
                ];
            } else {
                $response = [
                    "success" => false,
                    "message" => "Producto no encontrado",
                    "accion" => "Buscar",
                    "errors" => ["codigo_buscar" => "No se encontro producto con ese codigo"]
                ];
            }
            break;

        case "Listar":
            $productos = $producto->listar();
            $response = [
                "success" => true,
                "message" => "Lista de productos obtenida",
                "accion" => "Listar",
                "productos" => $productos,
                "errors" => []
            ];
            break;

        default:
            $response = [
                "success" => false,
                "message" => "Accion no reconocida: " . $accion,
                "accion" => $accion,
                "errors" => ["accion" => "Accion no valida"]
            ];
            break;
    }

} catch (Exception $e) {
    error_log("Error en registrar.php: " . $e->getMessage());
    $response = [
        "success" => false,
        "message" => "Error interno del servidor: " . $e->getMessage(),
        "accion" => isset($accion) ? $accion : "",
        "errors" => ["exception" => $e->getMessage()]
    ];
}

// Enviar respuesta JSON
echo json_encode($response);
exit;
?>