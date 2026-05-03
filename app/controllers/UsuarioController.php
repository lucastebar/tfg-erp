<?php

class UsuarioController {
    private $modelo;
    private $viewPath;

    public function __construct() {
        $this->modelo = new Usuario();
        $this->viewPath = __DIR__ . '/../views/usuarios/';
    }

    public function index() {
        $usuarios = $this->modelo->obtenerTodos();
        $this->render('listado', ['usuarios' => $usuarios]);
    }

    public function nuevo() {
        $this->render('formulario', ['accion' => 'nuevo', 'usuario' => null]);
    }

    public function editar($id) {
        $usuario = $this->modelo->obtenerPorId($id);
        if (!$usuario) {
            $_SESSION['mensaje'] = 'Usuario no encontrado';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('index');
            return;
        }
        $this->render('formulario', ['accion' => 'editar', 'usuario' => $usuario]);
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index');
            return;
        }

        $data = $this->modelo->sanitize($_POST);
        $errores = $this->validar($data, 'nuevo');

        if (!empty($errores)) {
            $_SESSION['mensaje'] = implode('<br>', $errores);
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'usuario' => $data, 'errores' => $errores]);
            return;
        }

        if ($this->modelo->existeEmail($data['email'])) {
            $_SESSION['mensaje'] = 'Ya existe un usuario con ese email';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'usuario' => $data]);
            return;
        }

        if ($this->modelo->guardar($data)) {
            $_SESSION['mensaje'] = 'Usuario creado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            $this->redirigir('index');
        } else {
            $_SESSION['mensaje'] = 'Error al guardar el usuario';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'usuario' => $data]);
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index');
            return;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $this->redirigir('index');
            return;
        }

        $data = $this->modelo->sanitize($_POST);
        $errores = $this->validar($data, 'editar');

        if (!empty($errores)) {
            $_SESSION['mensaje'] = implode('<br>', $errores);
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'usuario' => array_merge($data, ['id' => $id])]);
            return;
        }

        if ($this->modelo->existeEmail($data['email'], $id)) {
            $_SESSION['mensaje'] = 'Ya existe otro usuario con ese email';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'usuario' => array_merge($data, ['id' => $id])]);
            return;
        }

        if ($this->modelo->actualizar($id, $data)) {
            $_SESSION['mensaje'] = 'Usuario actualizado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            $this->redirigir('index');
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar el usuario';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'usuario' => array_merge($data, ['id' => $id])]);
        }
    }

    public function eliminar($id) {
        if ($this->modelo->eliminar($id)) {
            $_SESSION['mensaje'] = 'Usuario eliminado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el usuario';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
    }

    public function activar($id) {
        if ($this->modelo->activar($id)) {
            $_SESSION['mensaje'] = 'Usuario activado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al activar el usuario';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
    }

    private function validar($data, $tipo) {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email es obligatorio y debe ser válido';
        }
        if ($tipo === 'nuevo' && empty($data['password'])) {
            $errores[] = 'La contraseña es obligatoria';
        }
        if (!in_array($data['rol'], ['admin', 'operario'])) {
            $errores[] = 'El rol debe ser admin u operario';
        }

        return $errores;
    }

    private function render($vista, $data = []) {
        extract($data);
        ob_start();
        $viewFile = $this->viewPath . $vista . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "Vista no encontrada: $vista";
        }
        $content = ob_get_clean();
        
        $titulo = $titulo ?? 'Gestión de Usuarios';
        require __DIR__ . '/../views/layout.php';
    }

    private function redirigir($accion) {
        header('Location: index.php?controller=usuario&action=' . $accion);
        exit;
    }
}