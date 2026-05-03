<?php

class Cliente {
    private $pdo;
    private $id;
    private $razonSocial;
    private $nif;
    private $direccion;
    private $cp;
    private $ciudad;
    private $telefono;
    private $email;
    private $createdAt;
    private $updatedAt;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            require_once __DIR__ . '/../../config/db.php';
            $this->pdo = getDBConnection();
        }
    }

    public function obtenerTodos() {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes ORDER BY razon_social ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function buscar($termino) {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE razon_social LIKE ? OR nif LIKE ? OR email LIKE ? ORDER BY razon_social ASC");
        $busqueda = "%{$termino}%";
        $stmt->execute([$busqueda, $busqueda, $busqueda]);
        return $stmt->fetchAll();
    }

    public function guardar($data) {
        $sql = "INSERT INTO clientes (razon_social, nif, direccion, cp, ciudad, telefono, email, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['razon_social'],
            $data['nif'],
            $data['direccion'],
            $data['cp'],
            $data['ciudad'],
            $data['telefono'],
            $data['email']
        ]);
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE clientes SET razon_social = ?, nif = ?, direccion = ?, cp = ?, ciudad = ?, telefono = ?, email = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['razon_social'],
            $data['nif'],
            $data['direccion'],
            $data['cp'],
            $data['ciudad'],
            $data['telefono'],
            $data['email'],
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeNif($nif, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT id FROM clientes WHERE nif = ? AND id != ?");
            $stmt->execute([$nif, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT id FROM clientes WHERE nif = ?");
            $stmt->execute([$nif]);
        }
        return $stmt->fetch() !== false;
    }

    public function validarNif($nif) {
        $nif = strtoupper($nif);
        if (!preg_match('/^[0-9]{8}[A-Z]$/', $nif) && !preg_match('/^[A-Z]{1}[0-9]{8}$/', $nif)) {
            return false;
        }
        return true;
    }

    public function sanitize($data) {
        return [
            'razon_social' => htmlspecialchars(trim($data['razon_social']), ENT_QUOTES, 'UTF-8'),
            'nif' => strtoupper(trim($data['nif'])),
            'direccion' => htmlspecialchars(trim($data['direccion']), ENT_QUOTES, 'UTF-8'),
            'cp' => trim($data['cp']),
            'ciudad' => htmlspecialchars(trim($data['ciudad']), ENT_QUOTES, 'UTF-8'),
            'telefono' => preg_replace('/[^0-9]/', '', trim($data['telefono'])),
            'email' => strtolower(trim($data['email']))
        ];
    }
}