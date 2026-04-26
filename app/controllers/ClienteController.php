<?php

class ClienteController {
    private $modelo;
    private $viewPath;

    public function __construct() {
        $this->modelo = new Cliente();
        $this->viewPath = __DIR__ . '/../views/clientes/';
    }

    public function index() {
        $busqueda = $_GET['busqueda'] ?? '';
        if ($busqueda) {
            $clientes = $this->modelo->buscar($busqueda);
        } else {
            $clientes = $this->modelo->obtenerTodos();
        }
        $this->render('listado', ['clientes' => $clientes, 'busqueda' => $busqueda]);
    }

    public function nuevo() {
        $this->render('formulario', ['accion' => 'nuevo', 'cliente' => null]);
    }

    public function editar($id) {
        $cliente = $this->modelo->obtenerPorId($id);
        if (!$cliente) {
            $_SESSION['mensaje'] = 'Cliente no encontrado';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('index');
            return;
        }
        $this->render('formulario', ['accion' => 'editar', 'cliente' => $cliente]);
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index');
            return;
        }

        $data = $this->modelo->sanitize($_POST);
        $errores = $this->validar($data);

        if (!empty($errores)) {
            $_SESSION['mensaje'] = implode('<br>', $errores);
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'cliente' => $data, 'errores' => $errores]);
            return;
        }

        if ($this->modelo->existeNif($data['nif'])) {
            $_SESSION['mensaje'] = 'Ya existe un cliente con ese NIF';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'cliente' => $data]);
            return;
        }

        if ($this->modelo->guardar($data)) {
            $_SESSION['mensaje'] = 'Cliente creado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            $this->redirigir('index');
        } else {
            $_SESSION['mensaje'] = 'Error al guardar el cliente';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'cliente' => $data]);
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
        $errores = $this->validar($data);

        if (!empty($errores)) {
            $_SESSION['mensaje'] = implode('<br>', $errores);
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'cliente' => array_merge($data, ['id' => $id])]);
            return;
        }

        if ($this->modelo->existeNif($data['nif'], $id)) {
            $_SESSION['mensaje'] = 'Ya existe otro cliente con ese NIF';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'cliente' => array_merge($data, ['id' => $id])]);
            return;
        }

        if ($this->modelo->actualizar($id, $data)) {
            $_SESSION['mensaje'] = 'Cliente actualizado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            $this->redirigir('index');
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar el cliente';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'cliente' => array_merge($data, ['id' => $id])]);
        }
    }

    public function eliminar($id) {
        if ($this->modelo->eliminar($id)) {
            $_SESSION['mensaje'] = 'Cliente eliminado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el cliente';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
    }

    private function validar($data) {
        $errores = [];

        if (empty($data['razon_social'])) {
            $errores[] = 'La razón social es obligatoria';
        }
        if (empty($data['nif'])) {
            $errores[] = 'El NIF es obligatoria';
        } elseif (!$this->modelo->validarNif($data['nif'])) {
            $errores[] = 'El formato del NIF es inválido';
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
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
            echo "Vista noenciada: $vista";
        }
        $content = ob_get_clean();
        
        $titulo = $titulo ?? 'Gestión de Clientes';
        require __DIR__ . '/../views/layout.php';
    }

    private function redirigir($accion) {
        header('Location: index.php?controller=cliente&action=' . $accion);
        exit;
    }
}