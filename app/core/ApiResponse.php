<?php

class ApiResponse {
    
    public static function error400($mensaje) {
        self::send(400, ['error' => $mensaje]);
    }
    
    public static function error401($mensaje = 'No autenticado') {
        self::send(401, ['error' => $mensaje]);
    }
    
    public static function error404($mensaje = 'Recurso no encontrado') {
        self::send(404, ['error' => $mensaje]);
    }
    
    public static function error500($mensaje = 'Error interno del servidor') {
        self::send(500, ['error' => $mensaje]);
    }
    
    public static function success($data = [], $mensaje = '') {
        $response = $data;
        if ($mensaje) {
            $response['mensaje'] = $mensaje;
        }
        self::send(200, $response);
    }
    
    public static function send($codigo, $data) {
        http_response_code($codigo);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

class ValidationError extends Exception {
    private $campo;
    
    public function __construct($campo, $mensaje) {
        parent::__construct($mensaje);
        $this->campo = $campo;
    }
    
    public function getCampo() {
        return $this->campo;
    }
}

class AppException extends Exception {
    private $codigoError;
    
    public function __construct($codigoError, $mensaje) {
        parent::__construct($mensaje);
        $this->codigoError = $codigoError;
    }
    
    public function getCodigoError() {
        return $this->codigoError;
    }
}