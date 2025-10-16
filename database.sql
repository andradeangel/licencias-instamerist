
-- Tabla de usuarios (administradores, docentes, padres)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'docente', 'padre') NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de estudiantes
CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    ci VARCHAR(20) UNIQUE NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de relaciones estudiante-padre
CREATE TABLE estudiante_padre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    padre_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
    FOREIGN KEY (padre_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE(estudiante_id, padre_id)
);

-- Tabla de licencias
CREATE TABLE licencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    fecha_licencia DATE NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('pendiente', 'aceptado', 'rechazado') DEFAULT 'pendiente' NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creada_por INT NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
    FOREIGN KEY (creada_por) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insertar datos de prueba

-- Administrador
INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES
('Admin', 'Principal', 'admin@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); -- password: password

-- Docentes
INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES
('Docente', 'Uno', 'docente1@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente'),
('Docente', 'Dos', 'docente2@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente');

-- Padres
INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES
('Padre', 'Uno', 'padre1@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'padre'),
('Padre', 'Dos', 'padre2@amerinst.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'padre');

-- Estudiantes
INSERT INTO estudiantes (nombre, apellido, fecha_nacimiento, ci) VALUES
('Estudiante', 'Uno', '2010-05-15', '12345678'),
('Estudiante', 'Dos', '2011-03-20', '87654321'),
('Estudiante', 'Tres', '2009-11-10', '11223344'),
('Estudiante', 'Cuatro', '2012-07-05', '44332211');

-- Asignar estudiantes a padres
INSERT INTO estudiante_padre (estudiante_id, padre_id) VALUES
(1, 4), -- Estudiante 1 a Padre 1
(2, 4), -- Estudiante 2 a Padre 1
(3, 5), -- Estudiante 3 a Padre 2
(4, 5); -- Estudiante 4 a Padre 2

-- Licencias de prueba
INSERT INTO licencias (estudiante_id, fecha_licencia, motivo, creada_por) VALUES
(1, '2023-10-15', 'Enfermedad', 4),
(2, '2023-10-16', 'Cita médica', 4),
(3, '2023-10-17', 'Problemas familiares', 5),
(4, '2023-10-18', 'Viaje', 5);