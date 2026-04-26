<?php

class MainController {

    public function index() {
        $this->render('dashboard');
    }

    private function render($vista, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/main/' . $vista . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "Vista no encontrada: $vista";
        }
    }
}