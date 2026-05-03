<?php

class Factura {
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
        $stmt = $this->pdo->query("SELECT f.*, c.razon_social, c.nif FROM facturas f LEFT JOIN clientes c ON f.cliente_id = c.id ORDER BY f.numero_factura DESC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT f.*, c.razon_social, c.nif, c.direccion, c.cp, c.ciudad FROM facturas f LEFT JOIN clientes c ON f.cliente_id = c.id WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function obtenerDetalles($facturaId) {
        $stmt = $this->pdo->prepare("SELECT df.*, p.nombre, p.codigo FROM detalles_factura df LEFT JOIN productos p ON df.producto_id = p.id WHERE df.factura_id = ?");
        $stmt->execute([$facturaId]);
        return $stmt->fetchAll();
    }

    public function buscar($termino) {
        $stmt = $this->pdo->prepare("SELECT f.*, c.razon_social, c.nif FROM facturas f LEFT JOIN clientes c ON f.cliente_id = c.id WHERE f.numero_factura LIKE ? OR c.razon_social LIKE ? ORDER BY f.numero_factura DESC");
        $busqueda = "%{$termino}%";
        $stmt->execute([$busqueda, $busqueda]);
        return $stmt->fetchAll();
    }

    public function generarNumeroFactura($serie = 'A') {
        $anno = date('Y');
        $stmt = $this->pdo->prepare("SELECT MAX(CAST(SUBSTRING(numero_factura, 5) AS UNSIGNED)) as max_num FROM facturas WHERE serie = ? AND anno = ?");
        $stmt->execute([$serie, $anno]);
        $result = $stmt->fetch();
        $sigiente = ($result['max_num'] ?? 0) + 1;
        return $serie . str_pad($sigiente, 5, '0', STR_PAD_LEFT) . '/' . $anno;
    }

    public function crear($data, $lineas) {
        $this->pdo->beginTransaction();
        
        try {
            $numeroFactura = $this->generarNumeroFactura($data['serie'] ?? 'A');
            $descuento = floatval($data['descuento'] ?? 0);
            
            $baseImponible = 0;
            $ivaTotal = 0;
            
            foreach ($lineas as $linea) {
                $baseImponible += $linea['cantidad'] * $linea['precio_unitario'];
                $ivaTotal += ($linea['cantidad'] * $linea['precio_unitario']) * (floatval($linea['tipo_iva']) / 100);
            }
            
            if ($descuento > 0) {
                $descuentoAmount = $baseImponible * ($descuento / 100);
                $baseImponible -= $descuentoAmount;
                $ivaTotal = $baseImponible * 0.21;
            }
            
            $total = $baseImponible + $ivaTotal;
            
            $sql = "INSERT INTO facturas (numero_factura, serie, anno, cliente_id, base_imponible, iva, total, descuento, estado, fecha, created_at, updated_at) 
                    VALUES (?, ?, YEAR(NOW()), ?, ?, ?, ?, ?, 'pendiente', NOW(), NOW(), NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $numeroFactura,
                $data['serie'] ?? 'A',
                $data['cliente_id'],
                round($baseImponible, 2),
                round($ivaTotal, 2),
                round($total, 2),
                $descuento
            ]);
            
            $facturaId = $this->pdo->lastInsertId();
            
            foreach ($lineas as $linea) {
                $sqlDetalle = "INSERT INTO detalles_factura (factura_id, producto_id, cantidad, precio_unitario, created_at) VALUES (?, ?, ?, ?, NOW())";
                $stmtDetalle = $this->pdo->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    $facturaId,
                    $linea['producto_id'],
                    $linea['cantidad'],
                    $linea['precio_unitario']
                ]);
                
                $this->restarStock($linea['producto_id'], $linea['cantidad']);
            }
            
            $this->pdo->commit();
            return $facturaId;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al crear factura: " . $e->getMessage());
            return false;
        }
    }

    public function cambiarEstado($id, $estado) {
        $estadosValidos = ['pendiente', 'pagada', 'cancelada'];
        if (!in_array($estado, $estadosValidos)) {
            return false;
        }
        
        if ($estado === 'cancelada') {
            $this->devolverStock($id);
        }
        
        $stmt = $this->pdo->prepare("UPDATE facturas SET estado = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    private function restarStock($productoId, $cantidad) {
        $stmt = $this->pdo->prepare("UPDATE productos SET stock_actual = stock_actual - ?, updated_at = NOW() WHERE id = ? AND stock_actual >= ?");
        return $stmt->execute([$cantidad, $productoId, $cantidad]);
    }

    private function devolverStock($facturaId) {
        $detalles = $this->obtenerDetalles($facturaId);
        foreach ($detalles as $detalle) {
            $stmt = $this->pdo->prepare("UPDATE productos SET stock_actual = stock_actual + ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$detalle['cantidad'], $detalle['producto_id']]);
        }
    }

    public function eliminar($id) {
        return $this->cambiarEstado($id, 'cancelada');
    }

    public function obtenerEstadisticas($fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT 
                    COUNT(*) as total_facturas,
                    SUM(total) as total_ventas,
                    SUM(iva) as total_iva
                FROM facturas WHERE estado != 'cancelada'";
        
        $params = [];
        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        } elseif ($fechaInicio) {
            $sql .= " AND fecha >= ?";
            $params = [$fechaInicio];
        } elseif ($fechaFin) {
            $sql .= " AND fecha <= ?";
            $params = [$fechaFin];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}