<?php
include_once 'conexion.php';

class Producto {
    
    
    public $id;
    public $codigo;
    public $producto;
    public $precio;
    public $cantidad;
    public $errors = [];
    
    public function __construct() {
        
    }
    
    // Validación de datos
    public function validate() {
        $this->errors = [];
        
        if (empty($this->codigo)) {
            $this->errors['codigo'] = "El código es obligatorio";
        }
        
        if (empty($this->producto)) {
            $this->errors['producto'] = "El nombre del producto es obligatorio";
        }
        
        if (empty($this->precio) || !is_numeric($this->precio) || $this->precio <= 0) {
            $this->errors['precio'] = "El precio debe ser un número mayor a 0";
        }
        
        if (empty($this->cantidad) || !is_numeric($this->cantidad) || $this->cantidad < 0) {
            $this->errors['cantidad'] = "La cantidad debe ser un número válido";
        }
        
        return empty($this->errors);
    }
    
    // Guardar producto
    public function guardar() {
        if (!$this->validate()) {
            return false;
        }
        
        $data = [
            "codigo" => $this->codigo,
            "producto" => $this->producto,
            "precio" => $this->precio,
            "cantidad" => $this->cantidad
        ];
        
        $db = DB::getInstance();
        return $db->insertSeguro("productos", $data);
    }
    
    // Actualizar producto
    public function actualizar() {
        if (!$this->validate()) {
            return false;
        }
        
        $data = [
            "codigo" => $this->codigo,
            "producto" => $this->producto,
            "precio" => $this->precio,
            "cantidad" => $this->cantidad
        ];
        
        $db = DB::getInstance();
        return $db->updateSeguro("productos", $data, "id = " . $this->id);
    }
    
    // Buscar producto por ID
    public function buscarPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $db = DB::getInstance();
        return $db->fila($sql, ['id' => $id]);
    }
    
    // Buscar producto por código
    public function buscarPorCodigo($codigo) {
        $sql = "SELECT * FROM productos WHERE codigo = :codigo";
        $db = DB::getInstance();
        return $db->fila($sql, ['codigo' => $codigo]);
    }
    
    // Listar todos los productos
    public function listar() {
        $sql = "SELECT * FROM productos ORDER BY id DESC";
        $db = DB::getInstance();
        $resultados = $db->Arreglos($sql);
        
        // Asegurar que los tipos de datos sean correctos
        foreach ($resultados as &$producto) {
            $producto['precio'] = floatval($producto['precio']);
            $producto['cantidad'] = intval($producto['cantidad']);
            $producto['id'] = intval($producto['id']);
        }
        
        return $resultados;
    }
    
    // Cargar datos desde array (para $_POST)
    public function cargarDatos($datos) {
        $this->id = isset($datos['id']) ? $datos['id'] : null;
        $this->codigo = isset($datos['codigo']) ? trim($datos['codigo']) : '';
        $this->producto = isset($datos['producto']) ? trim($datos['producto']) : '';
        $this->precio = isset($datos['precio']) ? floatval($datos['precio']) : 0;
        $this->cantidad = isset($datos['cantidad']) ? intval($datos['cantidad']) : 0;
    }
    
    // Getter para errores
    public function getErrors() {
        return $this->errors;
    }
}
?>