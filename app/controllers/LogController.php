<?php

class LogController {
    private $modelo;
    private $viewPath;

    public function __construct() {
        $this->modelo = new Log();
        $this->viewPath = __DIR__ . '/../views/logs/';
    }

    public function index() {
        $fechaInicio = $_GET['fecha_inicio'] ?? null;
        $fechaFin = $_GET['fecha_fin'] ?? null;
        $usuarioId = $_GET['usuario_id'] ?? null;

        if ($fechaInicio && $fechaFin) {
            $logs = $this->modelo->obtenerPorFecha($fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59');
        } else {
            $logs = $this->modelo->obtenerTodos();
        }

        $this->render('listado', ['logs' => $logs, 'fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin]);
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
        
        $titulo = $titulo ?? 'Logs de Auditoría';
        require __DIR__ . '/../views/layout.php';
    }

    private function redirigir($accion) {
        header('Location: index.php?controller=log&action=' . $accion);
        exit;
    }
}