<?php

require_once __DIR__ . '/../../app/models/Producto.php';
require_once __DIR__ . '/../../app/models/Cliente.php';

class FacturaController {
    private $modelo;
    private $productoModel;
    private $clienteModel;
    private $viewPath;

    public function __construct() {
        $this->modelo = new Factura();
        $this->productoModel = new Producto();
        $this->clienteModel = new Cliente();
        $this->viewPath = __DIR__ . '/../views/facturas/';
    }

    public function index() {
        $busqueda = $_GET['busqueda'] ?? '';
        if ($busqueda) {
            $facturas = $this->modelo->buscar($busqueda);
        } else {
            $facturas = $this->modelo->obtenerTodos();
        }
        $this->render('listado', ['facturas' => $facturas, 'busqueda' => $busqueda]);
    }

    public function nuevo() {
        $clientes = $this->clienteModel->obtenerTodos();
        $productos = $this->productoModel->obtenerActivos();
        $this->render('crear', ['accion' => 'nuevo', 'clientes' => $clientes, 'productos' => $productos]);
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('nuevo');
            return;
        }

        $clienteId = $_POST['cliente_id'] ?? null;
        $descuento = floatval($_POST['descuento'] ?? 0);
        $lineas = $_POST['lineas'] ?? [];

        if (!$clienteId) {
            $_SESSION['mensaje'] = 'Debe seleccionar un cliente';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('nuevo');
            return;
        }

        if (empty($lineas)) {
            $_SESSION['mensaje'] = 'Debe añadir al menos un producto';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('nuevo');
            return;
        }

        $lineasValidadas = [];
        foreach ($lineas as $linea) {
            $productoId = intval($linea['producto_id']);
            $cantidad = intval($linea['cantidad']);
            
            if ($productoId <= 0 || $cantidad <= 0) {
                continue;
            }
            
            $producto = $this->productoModel->obtenerPorId($productoId);
            if (!$producto) {
                continue;
            }
            
            if ($producto['stock_actual'] < $cantidad) {
                $_SESSION['mensaje'] = 'Stock insuficiente para: ' . $producto['nombre'];
                $_SESSION['tipo_mensaje'] = 'error';
                $this->redirigir('nuevo');
                return;
            }
            
            $lineasValidadas[] = [
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto['pvp'],
                'tipo_iva' => $producto['tipo_iva']
            ];
        }

        if (empty($lineasValidadas)) {
            $_SESSION['mensaje'] = 'No hay líneas válidas para crear la factura';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('nuevo');
            return;
        }

        $data = [
            'cliente_id' => $clienteId,
            'descuento' => $descuento,
            'serie' => 'A'
        ];

        $facturaId = $this->modelo->crear($data, $lineasValidadas);

        if ($facturaId) {
            $_SESSION['mensaje'] = 'Factura creada correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            $this->redirigir('ver&id=' . $facturaId);
        } else {
            $_SESSION['mensaje'] = 'Error al crear la factura';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('nuevo');
        }
    }

    public function ver($id) {
        $factura = $this->modelo->obtenerPorId($id);
        if (!$factura) {
            $_SESSION['mensaje'] = 'Factura no encontrada';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('index');
            return;
        }
        $detalles = $this->modelo->obtenerDetalles($id);
        $this->render('ver', ['factura' => $factura, 'detalles' => $detalles]);
    }

    public function marcarPagada($id) {
        if ($this->modelo->cambiarEstado($id, 'pagada')) {
            $_SESSION['mensaje'] = 'Factura marcada como pagada';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar la factura';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
    }

    public function marcarPendiente($id) {
        if ($this->modelo->cambiarEstado($id, 'pendiente')) {
            $_SESSION['mensaje'] = 'Factura marcada como pendiente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar la factura';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
    }

    public function cancelar($id) {
        if ($this->modelo->cambiarEstado($id, 'cancelada')) {
            $_SESSION['mensaje'] = 'Factura cancelada (stock devuelto)';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al cancelar la factura';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        $this->redirigir('index');
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
        
        $titulo = $titulo ?? 'Gestión de Facturas';
        require __DIR__ . '/../views/layout.php';
    }

    private function redirigir($accion) {
        header('Location: index.php?controller=factura&action=' . $accion);
        exit;
    }
}