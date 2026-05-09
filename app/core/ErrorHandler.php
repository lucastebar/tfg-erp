<?php

$GLOBALS['app_error'] = null;

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
    } else {
        error_log("Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    }
    
    // Verificar si es una solicitud API
    $isApi = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    
    if ($isApi) {
        ApiResponse::send($codigo, ['error' => $mensaje]);
    } else {
        // Para páginas web, guardar el error en una variable global
        // y dejar que el código continúe (el error se muestra en la página si se desea)
        $GLOBALS['app_error'] = ['codigo' => $codigo, 'mensaje' => $mensaje];
        http_response_code($codigo);
    }
});

set_error_handler(function($nivel, $mensaje, $archivo, $linea) {
    if (!(error_reporting() & $nivel)) {
        return false;
    }
    throw new ErrorException($mensaje, 0, $nivel, $archivo, $linea);
});