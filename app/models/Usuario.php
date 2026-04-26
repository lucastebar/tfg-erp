<?php

class Usuario {
    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = getDBConnection();
    }

    public function verificarCredenciales($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT id, nombre, email, rol, ultimo_acceso FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function actualizarUltimoAcceso($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function cambiarPassword($id, $nuevaPassword) {
        $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }
}