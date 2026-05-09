<?php

set_exception_handler(function($e) {
    $codigo = 500;
    $mensaje = 'Error interno del servidor';
    
    if ($e instanceof ValidationError) {
        $codigo = 400;
        $mensaje = $e->getMessage();
    } elseif ($e instanceof AppException) {
        $codigo = $e->getCodigoError();
        $mensaje = $e->getMessage();
    } elseif ($e instanceof PDOException) {
        error_log("PDO Error: " . $e->getMessage());
        $mensaje = 'Error de base de datos';
    }
    
    ApiResponse::send($codigo, ['error' => $mensaje]);
});

set_error_handler(function($nivel, $mensaje, $archivo, $linea) {
    throw new ErrorException($mensaje, 0, $nivel, $archivo, $linea);
});