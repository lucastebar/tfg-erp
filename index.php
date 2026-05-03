<?php

session_start();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/models/Usuario.php';
require_once __DIR__ . '/app/models/Cliente.php';
require_once __DIR__ . '/app/models/Producto.php';
require_once __DIR__ . '/app/models/Factura.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'index';

$allowedControllers = ['auth', 'cliente', 'main', 'producto', 'factura', 'usuario'];
$allowedActions = [
    'auth' => ['index', 'login', 'logout', 'perfil'], 
    'cliente' => ['index', 'nuevo', 'editar', 'guardar', 'actualizar', 'eliminar'],
    'main' => ['index'],
    'producto' => ['index', 'nuevo', 'editar', 'guardar', 'actualizar', 'eliminar', 'ajustar'],
    'factura' => ['index', 'nuevo', 'crear', 'ver', 'marcarPagada', 'marcarPendiente', 'cancelar'],
    'usuario' => ['index', 'nuevo', 'editar', 'guardar', 'actualizar', 'eliminar', 'activar']
];

if (!in_array($controller, $allowedControllers)) {
    die('Controlador no válido');
}

if (!isset($allowedActions[$controller]) || !in_array($action, $allowedActions[$controller])) {
    $action = 'index';
}

if ($controller !== 'auth' && !isset($_SESSION['usuario_id'])) {
    header('Location: index.php?controller=auth&action=index');
    exit;
}

if ($controller === 'usuario' && ($_SESSION['rol'] ?? '') !== 'admin') {
    header('Location: index.php?controller=main&action=index');
    exit;
}

$controllerFile = __DIR__ . '/app/controllers/' . ucfirst($controller) . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $className = ucfirst($controller) . 'Controller';
    $instance = new $className();
    
    if (method_exists($instance, $action)) {
        $id = $_GET['id'] ?? null;
        $instance->$action($id);
    }
} else {
    echo "Controlador no encontrado";
}