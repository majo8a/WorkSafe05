-- Crear base de datos
CREATE DATABASE IF NOT EXISTS NOM035DB;
USE NOM035DB;

-- Tabla: Rol
CREATE TABLE Rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255)
);

-- Tabla: Usuario
CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    autenticacion_dos_factores BIT DEFAULT 0,
    activo BIT DEFAULT 1,
    id_rol INT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES Rol(id_rol)
);

-- Tabla: Cuestionario
CREATE TABLE Cuestionario (
    id_cuestionario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    version VARCHAR(20) NOT NULL,
    estado VARCHAR(20) DEFAULT 'activo',
    fecha_creacion DATETIME NOT NULL,
    id_usuario_creador INT,
    FOREIGN KEY (id_usuario_creador) REFERENCES Usuario(id_usuario)
);

-- Tabla: Pregunta
CREATE TABLE Pregunta (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    id_cuestionario INT NOT NULL,
    texto_pregunta TEXT NOT NULL,
    tipo_calificacion VARCHAR(20) NOT NULL,
    orden INT NOT NULL,
    puntaje_maximo INT DEFAULT 4,
    obligatoria BOOLEAN DEFAULT TRUE,
    dimension VARCHAR(100) NOT NULL,
    dominio VARCHAR(100) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    grupo_aplicacion VARCHAR(50) NOT NULL,
    id_pregunta_dependeDe INT,
    condicion VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_cuestionario) REFERENCES Cuestionario(id_cuestionario),
    FOREIGN KEY (id_pregunta_dependeDe) REFERENCES Pregunta(id_pregunta)
);

-- Tabla: Opcion_Respuesta
CREATE TABLE Opcion_Respuesta (
    id_opcion INT AUTO_INCREMENT PRIMARY KEY,
    etiqueta VARCHAR(50) NOT NULL,
    valor INT NOT NULL
);

-- Tabla: Evaluacion
CREATE TABLE Evaluacion (
    id_evaluacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cuestionario INT NOT NULL,
    fecha_aplicacion DATETIME NOT NULL,
    estado VARCHAR(50) DEFAULT 'pendiente',
    id_usuario_aplicador INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_cuestionario) REFERENCES Cuestionario(id_cuestionario),
    FOREIGN KEY (id_usuario_aplicador) REFERENCES Usuario(id_usuario)
);

-- Tabla: Respuesta
CREATE TABLE Respuesta (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_pregunta INT NOT NULL,
    id_evaluacion INT NOT NULL,
    id_opcion_respuesta_select INT NOT NULL,
    fecha_respuesta DATETIME NOT NULL,
    FOREIGN KEY (id_pregunta) REFERENCES Pregunta(id_pregunta),
    FOREIGN KEY (id_evaluacion) REFERENCES Evaluacion(id_evaluacion),
    FOREIGN KEY (id_opcion_respuesta_select) REFERENCES Opcion_Respuesta(id_opcion)
);

-- Tabla: Rango_Interpretacion
CREATE TABLE Rango_Interpretacion (
    id_rango INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    objeto VARCHAR(100) NOT NULL,
    rango_inferior INT NOT NULL,
    rango_superior INT NOT NULL,
    nivel_riesgo VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255)
);

-- Tabla: Resultado
CREATE TABLE Resultado (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_evaluacion INT NOT NULL,
    categoria VARCHAR(100),
    dominio VARCHAR(100),
    dimension VARCHAR(100),
    puntaje_obtenido INT NOT NULL,
    nivel_riesgo VARCHAR(50) NOT NULL,
    interpretacion VARCHAR(50) NOT NULL,
    id_rango INT,
    FOREIGN KEY (id_evaluacion) REFERENCES Evaluacion(id_evaluacion),
    FOREIGN KEY (id_rango) REFERENCES Rango_Interpretacion(id_rango)
);

-- Tabla: Medida
CREATE TABLE Medida (
    id_medida INT AUTO_INCREMENT PRIMARY KEY,
    id_resultado INT NOT NULL,
    tipo_medida VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    id_usuario_responsable INT,
    fecha_limite DATE NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    FOREIGN KEY (id_resultado) REFERENCES Resultado(id_resultado),
    FOREIGN KEY (id_usuario_responsable) REFERENCES Usuario(id_usuario)
);
    
-- Tabla: Evidencia
CREATE TABLE Evidencia (
    id_evidencia INT AUTO_INCREMENT PRIMARY KEY,
    id_medida INT NOT NULL,
    tipo_archivo VARCHAR(50) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    fecha_carga DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario_subidoPor INT,
    FOREIGN KEY (id_medida) REFERENCES Medida(id_medida),
    FOREIGN KEY (id_usuario_subidoPor) REFERENCES Usuario(id_usuario)
);

-- Tabla: Capacitacion
CREATE TABLE Capacitacion (
    id_capacitacion INT AUTO_INCREMENT PRIMARY KEY,
    tema VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    tipo_modalidad VARCHAR(50) NOT NULL,
    id_usuario_asignador INT,
    FOREIGN KEY (id_usuario_asignador) REFERENCES Usuario(id_usuario)
);

-- Tabla: Confirmacion
CREATE TABLE Confirmacion (
    id_confirmacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_capacitacion INT NOT NULL,
    tipo_confirmacion VARCHAR(50) NOT NULL,
    fecha_confirmacion DATETIME NOT NULL,
    ip_registro VARCHAR(50) NOT NULL,
    asistio BIT DEFAULT 1,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_capacitacion) REFERENCES Capacitacion(id_capacitacion)
);

-- Tabla: Documento
CREATE TABLE Documento (
    id_documento INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    fecha_publicacion DATETIME NOT NULL,
    id_usuario_publicador INT,
    acceso_roles VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_usuario_publicador) REFERENCES Usuario(id_usuario)
);

-- Tabla: Usuario_Documento
CREATE TABLE Usuario_Documento (
    id_usuario_doc INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_documento INT NOT NULL,
    fecha_asignacion DATETIME NOT NULL,
    tipo_acceso VARCHAR(50) NOT NULL,
    firmado BIT DEFAULT 0,
    fecha_firma DATETIME,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_documento) REFERENCES Documento(id_documento)
);

-- Tabla: Bitacora
CREATE TABLE Bitacora (
    id_bitacora INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    fecha_evento DATETIME NOT NULL,
    objeto VARCHAR(100) NOT NULL,
    id_objeto INT NOT NULL,
    ip_origen VARCHAR(100),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

-- Tabla: Notificacion
CREATE TABLE Notificacion (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(30) NOT NULL,
    contenido VARCHAR(255) NOT NULL,
    fecha_envio DATETIME NOT NULL,
    estado_general VARCHAR(20) DEFAULT 'pendiente',
    modulo_origen VARCHAR(50)
);

-- Tabla: Usuario_Notificacion
CREATE TABLE Usuario_Notificacion (
    id_usuario_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_notificacion INT NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    fecha_visualizacion DATETIME NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_notificacion) REFERENCES Notificacion(id_notificacion)
);

-- Tabla: Historial_Cambios
CREATE TABLE Historial_Cambios (
    id_cambio INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario_responsable INT NOT NULL,
    tipo_objeto VARCHAR(100) NOT NULL,
    id_objeto INT NOT NULL,
    campo VARCHAR(100) NOT NULL,
    valor_antiguo VARCHAR(255) NOT NULL,
    valor_nuevo VARCHAR(255) NOT NULL,
    fecha_cambio DATETIME NOT NULL,
    FOREIGN KEY (id_usuario_responsable) REFERENCES Usuario(id_usuario)
);

-- Tabla: Regla_Calificacion
CREATE TABLE Regla_Calificacion (
    id_regla INT AUTO_INCREMENT PRIMARY KEY,
    id_cuestionario INT NOT NULL,
    dimension VARCHAR(100) NOT NULL,
    rango_inferior INT NOT NULL,
    rango_superior INT NOT NULL,
    nivel_riesgo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_cuestionario) REFERENCES Cuestionario(id_cuestionario)
);
