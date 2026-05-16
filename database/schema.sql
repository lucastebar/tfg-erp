-- Base de datos TFG ERP
-- MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS tfg_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tfg_erp;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'operario') DEFAULT 'operario',
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(150) NOT NULL,
    nif VARCHAR(9) NOT NULL UNIQUE,
    direccion VARCHAR(255),
    cp VARCHAR(5),
    ciudad VARCHAR(100),
    telefono VARCHAR(15),
    email VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio_coste DECIMAL(10, 2),
    pvp DECIMAL(10, 2) NOT NULL,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    tipo_iva VARCHAR(10) DEFAULT '21',
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de facturas
CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(20) NOT NULL UNIQUE,
    serie VARCHAR(10) DEFAULT 'A',
    anno YEAR DEFAULT (YEAR(CURRENT_DATE)),
    cliente_id INT NOT NULL,
    base_imponible DECIMAL(10, 2) DEFAULT 0,
    iva DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) DEFAULT 0,
    descuento DECIMAL(5, 2) DEFAULT 0,
    estado ENUM('pendiente', 'pagada', 'cancelada') DEFAULT 'pendiente',
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB;

-- Tabla de detalles de factura
CREATE TABLE IF NOT EXISTS detalles_factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

-- Tabla de logs
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    detalles TEXT,
    ip VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Tabla de configuración de empresa
CREATE TABLE IF NOT EXISTS configuracion_empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL DEFAULT 'Mi Empresa',
    cif VARCHAR(20) NOT NULL DEFAULT 'A12345678',
    direccion VARCHAR(255),
    cp VARCHAR(10),
    ciudad VARCHAR(100),
    provincia VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    web VARCHAR(150),
    iva_por_defecto DECIMAL(5, 2) DEFAULT 21.00,
    serie_facturas VARCHAR(5) DEFAULT 'A',
    logo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertar configuración por defecto
INSERT INTO configuracion_empresa (nombre, cif, direccion, ciudad) 
VALUES ('TFG ERP', 'X12345678', 'Dirección de la empresa', 'Ciudad');

-- Insertar usuario administrador por defecto (password: P@ssw0rd123..)
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador', 'admin@tfg.local', '$2y$10$8K1pR2vL3mN4oP5qQ6rR7sS8tT9uU0vV1wW2xX3yY4zZ5aA6bB7c', 'admin');

-- Insertar usuario operario por defecto (password: P@ssw0rd123..)
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Operario', 'operario@tfg.local', '$2y$10$8K1pR2vL3mN4oP5qQ6rR7sS8tT9uU0vV1wW2xX3yY4zZ5aA6bB7c', 'operario');