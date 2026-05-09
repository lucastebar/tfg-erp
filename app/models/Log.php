<?php

class Log {
    private $pdo;

    public function __construct() {
        $dbPath = __DIR__ . '/../../config/db.php';
        if (file_exists($dbPath)) {
            require_once $dbPath;
            $this->pdo = getDBConnection();
        } else {
            error_log("Log: No se encontró db.php en $dbPath");
            $this->pdo = null;
        }
    }

    public function obtenerTodos($filtros = []) {
        if (!$this->pdo) {
            error_log("Log::obtenerTodos - Sin conexión a BD");
            return [];
        }
        
        try {
            $sql = "SELECT l.*, u.nombre as usuario_nombre 
                    FROM logs l 
                    LEFT JOIN usuarios u ON l.usuario_id = u.id 
                    ORDER BY l.created_at DESC";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Log::obtenerTodos - PDO Error: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorUsuario($usuarioId) {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM logs WHERE usuario_id = ? ORDER BY created_at DESC");
            $stmt->execute([$usuarioId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Log::obtenerPorUsuario - PDO Error: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorFecha($fechaInicio, $fechaFin) {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT l.*, u.nombre as usuario_nombre 
                    FROM logs l 
                    LEFT JOIN usuarios u ON l.usuario_id = u.id 
                    WHERE l.created_at BETWEEN ? AND ?
                    ORDER BY l.created_at DESC");
            $stmt->execute([$fechaInicio, $fechaFin]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Log::obtenerPorFecha - PDO Error: " . $e->getMessage());
            return [];
        }
    }

    public function registrar($usuarioId, $accion, $tabla = null, $registroId = null, $detalles = null) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            $sql = "INSERT INTO logs (usuario_id, accion, tabla_afectada, registro_id, detalles, ip, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$usuarioId, $accion, $tabla, $registroId, $detalles, $ip]);
        } catch (PDOException $e) {
            error_log("Log::registrar - PDO Error: " . $e->getMessage());
            return false;
        }
    }

    public static function guardar($usuarioId, $accion, $tabla = null, $registroId = null, $detalles = null) {
        try {
            $log = new self();
            return $log->registrar($usuarioId, $accion, $tabla, $registroId, $detalles);
        } catch (Exception $e) {
            error_log("Log::guardar - Error: " . $e->getMessage());
            return false;
        }
    }
}