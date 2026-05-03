<?php

class Producto {
    private $pdo;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            require_once __DIR__ . '/../../config/db.php';
            $this->pdo = getDBConnection();
        }
    }

    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT * FROM productos ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function obtenerActivos() {
        $stmt = $this->pdo->query("SELECT * FROM productos WHERE activo = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function buscar($termino) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE nombre LIKE ? OR codigo LIKE ? ORDER BY nombre ASC");
        $busqueda = "%{$termino}%";
        $stmt->execute([$busqueda, $busqueda]);
        return $stmt->fetchAll();
    }

    public function guardar($data) {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, precio_coste, pvp, stock_actual, stock_minimo, tipo_iva, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'] ?? '',
            $data['precio_coste'] ?? 0,
            $data['pvp'],
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 5,
            $data['tipo_iva'] ?? '21'
        ]);
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE productos SET codigo = ?, nombre = ?, descripcion = ?, precio_coste = ?, pvp = ?, stock_actual = ?, stock_minimo = ?, tipo_iva = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'] ?? '',
            $data['precio_coste'] ?? 0,
            $data['pvp'],
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 5,
            $data['tipo_iva'] ?? '21',
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function eliminarDefinitivo($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeCodigo($codigo, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT id FROM productos WHERE codigo = ? AND id != ?");
            $stmt->execute([$codigo, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT id FROM productos WHERE codigo = ?");
            $stmt->execute([$codigo]);
        }
        return $stmt->fetch() !== false;
    }

    public function ajustarStock($id, $cantidad) {
        $sql = "UPDATE productos SET stock_actual = stock_actual + ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$cantidad, $id]);
    }

    public function restarStock($id, $cantidad) {
        $producto = $this->obtenerPorId($id);
        if ($producto && $producto['stock_actual'] >= $cantidad) {
            $sql = "UPDATE productos SET stock_actual = stock_actual - ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cantidad, $id]);
        }
        return false;
    }

    public function obtenerBajoStock() {
        $stmt = $this->pdo->query("SELECT * FROM productos WHERE activo = 1 AND stock_actual <= stock_minimo ORDER BY stock_actual ASC");
        return $stmt->fetchAll();
    }

    public function sanitize($data) {
        return [
            'codigo' => strtoupper(trim($data['codigo'])),
            'nombre' => htmlspecialchars(trim($data['nombre']), ENT_QUOTES, 'UTF-8'),
            'descripcion' => htmlspecialchars(trim($data['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'precio_coste' => floatval($data['precio_coste'] ?? 0),
            'pvp' => floatval($data['pvp']),
            'stock_actual' => intval($data['stock_actual'] ?? 0),
            'stock_minimo' => intval($data['stock_minimo'] ?? 5),
            'tipo_iva' => $data['tipo_iva'] ?? '21'
        ];
    }
}