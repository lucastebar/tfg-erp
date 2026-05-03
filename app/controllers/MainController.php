<?php

require_once __DIR__ . '/../models/Producto.php';

class MainController {

    public function index() {
        $productoModel = new Producto();
        $productosBajoStock = $productoModel->obtenerBajoStock();
        $this->render('dashboard', ['productosBajoStock' => $productosBajoStock]);
    }

    private function render($vista, $data = []) {
        extract($data);
        ob_start();
        $viewFile = __DIR__ . '/../views/main/' . $vista . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "Vista no encontrada: $vista";
        }
        $content = ob_get_clean();
        
        $titulo = $titulo ?? 'Panel Principal';
        require __DIR__ . '/../views/layout.php';
    }
}