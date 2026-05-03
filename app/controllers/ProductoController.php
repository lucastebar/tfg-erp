<?php

class ProductoController {
    private $modelo;
    private $viewPath;

    public function __construct() {
        $this->modelo = new Producto();
        $this->viewPath = __DIR__ . '/../views/productos/';
    }

    public function index() {
        $busqueda = $_GET['busqueda'] ?? '';
        if ($busqueda) {
            $productos = $this->modelo->buscar($busqueda);
        } else {
            $productos = $this->modelo->obtenerTodos();
        }
        $this->render('listado', ['productos' => $productos, 'busqueda' => $busqueda]);
    }

    public function nuevo() {
        $this->render('formulario', ['accion' => 'nuevo', 'producto' => null]);
    }

    public function editar($id) {
        $producto = $this->modelo->obtenerPorId($id);
        if (!$producto) {
            $_SESSION['mensaje'] = 'Producto no encontrado';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('index');
            return;
        }
        $this->render('formulario', ['accion' => 'editar', 'producto' => $producto]);
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
            $this->render('formulario', ['accion' => 'nuevo', 'producto' => $data, 'errores' => $errores]);
            return;
        }

        if ($this->modelo->existeCodigo($data['codigo'])) {
            $_SESSION['mensaje'] = 'Ya existe un producto con ese código';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'producto' => $data]);
            return;
        }

        if ($this->modelo->guardar($data)) {
            $_SESSION['mensaje'] = 'Producto creado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            $this->redirigir('index');
        } else {
            $_SESSION['mensaje'] = 'Error al guardar el producto';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'nuevo', 'producto' => $data]);
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
            $this->render('formulario', ['accion' => 'editar', 'producto' => array_merge($data, ['id' => $id])]);
            return;
        }

        if ($this->modelo->existeCodigo($data['codigo'], $id)) {
            $_SESSION['mensaje'] = 'Ya existe otro producto con ese código';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'producto' => array_merge($data, ['id' => $id])]);
            return;
        }

        if ($this->modelo->actualizar($id, $data)) {
            $_SESSION['mensaje'] = 'Producto actualizado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            $this->redirigir('index');
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar el producto';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->render('formulario', ['accion' => 'editar', 'producto' => array_merge($data, ['id' => $id])]);
        }
    }

    public function eliminar($id) {
        if ($this->modelo->eliminar($id)) {
            $_SESSION['mensaje'] = 'Producto eliminado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el producto';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
    }

    public function ajustar() {
        $id = $_POST['id'] ?? null;
        $cantidad = intval($_POST['cantidad'] ?? 0);

        if (!$id || $cantidad === 0) {
            $_SESSION['mensaje'] = 'Datos inválidos para el ajuste';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('index');
            return;
        }

        if ($this->modelo->ajustarStock($id, $cantidad)) {
            $_SESSION['mensaje'] = 'Stock ajustado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al ajustar el stock';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
    }

    private function validar($data) {
        $errores = [];

        if (empty($data['codigo'])) {
            $errores[] = 'El código es obligatorio';
        }
        if (empty($data['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }
        if (empty($data['pvp']) || $data['pvp'] <= 0) {
            $errores[] = 'El PVP es obligatorio y debe ser mayor que 0';
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
        
        $titulo = $titulo ?? 'Gestión de Productos';
        require __DIR__ . '/../views/layout.php';
    }

    private function redirigir($accion) {
        header('Location: index.php?controller=producto&action=' . $accion);
        exit;
    }
}