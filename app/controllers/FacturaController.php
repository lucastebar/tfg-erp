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

    public function exportPdf($id) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $factura = $this->modelo->obtenerPorId($id);
        if (!$factura) {
            $_SESSION['mensaje'] = 'Factura no encontrada';
            $_SESSION['tipo_mensaje'] = 'error';
            $this->redirigir('index');
            return;
        }
        
        $detalles = $this->modelo->obtenerDetalles($id);
        
        $fpdfPath = __DIR__ . '/../../lib/FPDF/fpdf.php';
        if (!file_exists($fpdfPath)) {
            die('FPDF not found at: ' . $fpdfPath);
        }
        
        require_once $fpdfPath;
        
        if (!class_exists('FPDF')) {
            die('FPDF class not loaded');
        }
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 20);
        
        function toPdf($text) {
            return iconv('UTF-8', 'Windows-1252', $text);
        }
        
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 10, toPdf('Datos del Cliente'), 0, 1, 'L');
        
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(71, 85, 105);
        $pdf->Cell(0, 5, toPdf(htmlspecialchars($factura['razon_social'])), 0, 1, 'L');
        $pdf->Cell(0, 5, toPdf('NIF: ' . htmlspecialchars($factura['nif'])), 0, 1, 'L');
        $pdf->Cell(0, 5, toPdf(htmlspecialchars($factura['direccion'] ?? '')), 0, 1, 'L');
        $pdf->Cell(0, 5, toPdf(htmlspecialchars(($factura['cp'] ?? '') . ' ' . ($factura['ciudad'] ?? ''))), 0, 1, 'L');
        
        $pdf->Ln(10);
        $pdf->SetDrawColor(30, 41, 59);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 8, toPdf('FACTURA'), 0, 1, 'R');
        
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 5, toPdf('Nº: ' . $factura['numero_factura']), 0, 1, 'R');
        $pdf->Cell(0, 5, toPdf('Fecha: ' . date('d/m/Y', strtotime($factura['fecha']))), 0, 1, 'R');
        $pdf->Cell(0, 5, toPdf('Estado: ' . ucfirst($factura['estado'])), 0, 1, 'R');
        
        $pdf->Ln(10);
        
        $pdf->SetFillColor(241, 245, 249);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->SetDrawColor(203, 213, 225);
        
        $colCodigo = 30;
        $colProducto = 80;
        $colCantidad = 25;
        $colPrecio = 30;
        $colImporte = 25;
        
        $pdf->Cell($colCodigo, 8, toPdf('Codigo'), 1, 0, 'C', true);
        $pdf->Cell($colProducto, 8, toPdf('Producto'), 1, 0, 'C', true);
        $pdf->Cell($colCantidad, 8, toPdf('Cantidad'), 1, 0, 'C', true);
        $pdf->Cell($colPrecio, 8, toPdf('Precio'), 1, 0, 'C', true);
        $pdf->Cell($colImporte, 8, toPdf('Importe'), 1, 1, 'C', true);
        
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(71, 85, 105);
        
        foreach ($detalles as $detalle) {
            $importe = $detalle['cantidad'] * $detalle['precio_unitario'];
            $pdf->Cell($colCodigo, 7, toPdf($detalle['codigo']), 1, 0, 'L');
            $pdf->Cell($colProducto, 7, toPdf(substr($detalle['nombre'], 0, 35)), 1, 0, 'L');
            $pdf->Cell($colCantidad, 7, $detalle['cantidad'], 1, 0, 'C');
            $pdf->Cell($colPrecio, 7, iconv('UTF-8', 'Windows-1252', number_format($detalle['precio_unitario'], 2, ',', '.') . ' €'), 1, 0, 'R');
            $pdf->Cell($colImporte, 7, iconv('UTF-8', 'Windows-1252', number_format($importe, 2, ',', '.') . ' €'), 1, 1, 'R');
        }
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(30, 41, 59);
        
        $descuentoImporte = $factura['descuento'] > 0 ? $factura['base_imponible'] * ($factura['descuento'] / 100) : 0;
        $xTotales = 130;
        
        $pdf->Cell($xTotales, 6, toPdf('Base Imponible:'), 0, 0, 'R');
        $pdf->Cell(0, 6, toPdf(number_format($factura['base_imponible'], 2, ',', '.') . ' €'), 0, 1, 'R');
        
        $pdf->Cell($xTotales, 6, toPdf('IVA (21%):'), 0, 0, 'R');
        $pdf->Cell(0, 6, toPdf(number_format($factura['iva'], 2, ',', '.') . ' €'), 0, 1, 'R');
        
        if ($factura['descuento'] > 0) {
            $pdf->Cell($xTotales, 6, toPdf('Descuento (' . $factura['descuento'] . '%):'), 0, 0, 'R');
            $pdf->Cell(0, 6, toPdf('-' . number_format($descuentoImporte, 2, ',', '.') . ' €'), 0, 1, 'R');
        }
        
        $pdf->SetDrawColor(30, 41, 59);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(130, $pdf->GetY(), 200, $pdf->GetY());
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell($xTotales, 8, toPdf('TOTAL:'), 0, 0, 'R');
        $pdf->Cell(0, 8, toPdf(number_format($factura['total'], 2, ',', '.') . ' €'), 0, 1, 'R');
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="Factura_' . $factura['numero_factura'] . '.pdf"');
        $pdf->Output('I');
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
        $basePath = (getenv('PORT') !== false && getenv('PORT') !== '') ? '' : '/tfg-erp';
        header('Location: ' . $basePath . '/index.php?controller=factura&action=' . $accion);
        exit;
    }
}