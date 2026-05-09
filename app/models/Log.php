<?php

class Log {
    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = getDBConnection();
    }

    public function obtenerTodos($filtros = []) {
        $sql = "SELECT l.*, u.nombre as usuario_nombre 
                FROM logs l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id 
                ORDER BY l.created_at DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function obtenerPorUsuario($usuarioId) {
        $stmt = $this->pdo->prepare("SELECT * FROM logs WHERE usuario_id = ? ORDER BY created_at DESC");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function obtenerPorFecha($fechaInicio, $fechaFin) {
        $stmt = $this->pdo->prepare("SELECT l.*, u.nombre as usuario_nombre 
                FROM logs l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id 
                WHERE l.created_at BETWEEN ? AND ?
                ORDER BY l.created_at DESC");
        $stmt->execute([$fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }

    public function registrar($usuarioId, $accion, $tabla = null, $registroId = null, $detalles = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $sql = "INSERT INTO logs (usuario_id, accion, tabla_afectada, registro_id, detalles, ip, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$usuarioId, $accion, $tabla, $registroId, $detalles, $ip]);
    }

    public static function guardar($usuarioId, $accion, $tabla = null, $registroId = null, $detalles = null) {
        $log = new self();
        return $log->registrar($usuarioId, $accion, $tabla, $registroId, $detalles);
    }
}