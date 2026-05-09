<?php

class Usuario {
    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = getDBConnection();
    }

    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT id, nombre, email, rol, activo, ultimo_acceso, created_at FROM usuarios ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function obtenerActivos() {
        $stmt = $this->pdo->query("SELECT id, nombre, email, rol, activo, ultimo_acceso FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT id, nombre, email, rol, activo, ultimo_acceso, created_at FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
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

    public function guardar($data) {
        $hash = password_hash($data['password'] ?? 'change123', PASSWORD_BCRYPT);
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['email'],
            $hash,
            $data['rol'] ?? 'operario'
        ]);
    }

    public function actualizar($id, $data) {
        if (!empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, password = ?, activo = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['nombre'],
                $data['email'],
                $data['rol'],
                $hash,
                $data['activo'] ?? 1,
                $id
            ]);
        } else {
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, activo = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['nombre'],
                $data['email'],
                $data['rol'],
                $data['activo'] ?? 1,
                $id
            ]);
        }
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = 0, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function activar($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = 1, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeEmail($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
        }
        return $stmt->fetch() !== false;
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

    public static function validarPassword($password) {
        $errores = [];
        
        if (strlen($password) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
        
        $count = 0;
        if ($hasUpper) $count++;
        if ($hasLower) $count++;
        if ($hasNumber) $count++;
        if ($hasSpecial) $count++;
        
        if ($count < 3) {
            $errores[] = 'La contraseña debe cumplir al menos 3 de estas 4 reglas: mayúscula, minúscula, número y carácter especial';
        }
        
        return $errores;
    }

    public function sanitize($data) {
        return [
            'nombre' => htmlspecialchars(trim($data['nombre'])), 
            'email' => filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL) ? trim($data['email']) : '',
            'rol' => $data['rol'] ?? 'operario',
            'password' => $data['password'] ?? '',
            'activo' => isset($data['activo']) ? 1 : 0
        ];
    }
}