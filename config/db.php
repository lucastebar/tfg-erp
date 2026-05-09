<?php

// Detectar si está en Railway (PORT está definida por Railway)
$isRailway = getenv('PORT') !== false && getenv('PORT') !== '';

// Configuración de base de datos
// En Railway usa variables MYSQL_*, en local usa valores por defecto
if ($isRailway) {
    // Railway proporciona estas variables automáticamente
    define('DB_HOST', getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('MYSQL_PORT') ?: '3306');
    define('DB_NAME', getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'railway');
    define('DB_USER', getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') ?: '');
} else {
    // Valores locales para desarrollo
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'tfg_erp');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión a BD: " . $e->getMessage());
        return null;
    }
}