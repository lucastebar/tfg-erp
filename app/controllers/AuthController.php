<?php

class AuthController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Usuario();
    }

    public function index() {
        $this->modelo->seedSiVacio();
        if ($_SESSION['usuario_id'] ?? '') {
            header('Location: index.php?controller=main&action=index');
            exit;
        }
        $this->render('login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['mensaje'] = 'Email y contraseña son obligatoria';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->index();
            return;
        }

        $usuario = $this->modelo->verificarCredenciales($email, $password);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];
            
            $this->modelo->actualizarUltimoAcceso($usuario['id']);
            
            header('Location: index.php?controller=main&action=index');
            exit;
        } else {
            $_SESSION['mensaje'] = 'Credenciales incorrectas';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->index();
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?controller=auth&action=index');
        exit;
    }

    public function perfil() {
        if (!$_SESSION['usuario_id'] ?? '') {
            $this->redirigir('index');
            return;
        }
        
        $usuario = $this->modelo->obtenerPorId($_SESSION['usuario_id']);
        $this->render('perfil', ['usuario' => $usuario]);
    }

    private function render($vista, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/auth/' . $vista . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
    }

    private function redirigir($action) {
        header('Location: index.php?controller=auth&action=' . $action);
        exit;
    }
}