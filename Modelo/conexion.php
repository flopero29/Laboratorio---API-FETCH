<?php
class DB {
    private $pdo;
    private static $instance = null;
    
    public function __construct() {
        try {
            $host = 'localhost';
            $dbname = 'productosdb';
            $username = 'root';  // Cambia si es necesario
            $password = '';      // Cambia si es necesario
            
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    // Patrón Singleton
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }
    
    // Método para inserción segura
    public function insertSeguro($tabla, $datos) {
        $campos = implode(', ', array_keys($datos));
        $placeholders = ':' . implode(', :', array_keys($datos));
        
        $sql = "INSERT INTO $tabla ($campos) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($datos);
    }
    
    // Método para actualización segura
    public function updateSeguro($tabla, $datos, $where) {
        $set = '';
        foreach ($datos as $campo => $valor) {
            $set .= "$campo = :$campo, ";
        }
        $set = rtrim($set, ', ');
        
        $sql = "UPDATE $tabla SET $set WHERE $where";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($datos);
    }
    
    // Método para consultas
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    // Obtener múltiples registros
    public function Arreglos($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Obtener un solo registro
    public function fila($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    // Obtener conexión PDO directamente
    public function getPdo() {
        return $this->pdo;
    }
}
?>