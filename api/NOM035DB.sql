-- Crear base de datos
CREATE DATABASE IF NOT EXISTS NOM035DB;
USE NOM035DB;

-- Tabla: Rol
CREATE TABLE Rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255)
);

INSERT INTO Rol (nombre_rol, descripcion) 
VALUES ('Administrador', 'Usuario con control total del sistema, gestiona usuarios, configuraciones y datos.');

INSERT INTO Rol (nombre_rol, descripcion) 
VALUES ('Psicólogo', 'Usuario encargado de aplicar cuestionarios, evaluar resultados y dar seguimiento.');

INSERT INTO Rol (nombre_rol, descripcion) 
VALUES ('Usuario', 'Usuario común que responde cuestionarios y consulta resultados personales.');


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
    id_pregunta INT NOT NULL,
    etiqueta VARCHAR(50) NOT NULL,
    valor INT NOT NULL,
    FOREIGN KEY (id_pregunta) REFERENCES Pregunta(id_pregunta)
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
    valor INT NOT NULL DEFAULT 0,
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

-- -------------------------------------------------------
-- Triggers automáticos para registrar en Historial_Cambios
-- -------------------------------------------------------

/* IMPORTANTE: establecer en la sesión el usuario responsable
   desde la aplicación (ej. PHP):
   SET @id_usuario_responsable = <id_usuario_actual>;
*/

DELIMITER //

/* ------------------ Usuario ------------------ */
CREATE TRIGGER tr_usuario_insert
AFTER INSERT ON Usuario
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios
    (id_usuario_responsable, tipo_objeto, id_objeto, campo, valor_antiguo, valor_nuevo, fecha_cambio)
    VALUES
    (@id_usuario_responsable, 'Usuario', NEW.id_usuario, 'CREACIÓN',
     '', CONCAT('nombre_completo=', NEW.nombre_completo, '; correo=', NEW.correo, '; telefono=', IFNULL(NEW.telefono,''), '; id_rol=', IFNULL(NEW.id_rol,'')),
     NOW());
END //

CREATE TRIGGER tr_usuario_update
AFTER UPDATE ON Usuario
FOR EACH ROW
BEGIN
    IF OLD.nombre_completo <> NEW.nombre_completo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario', NEW.id_usuario, 'nombre_completo', OLD.nombre_completo, NEW.nombre_completo, NOW());
    END IF;
    IF OLD.correo <> NEW.correo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario', NEW.id_usuario, 'correo', IFNULL(OLD.correo,''), IFNULL(NEW.correo,''), NOW());
    END IF;
    IF OLD.telefono <> NEW.telefono THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario', NEW.id_usuario, 'telefono', IFNULL(OLD.telefono,''), IFNULL(NEW.telefono,''), NOW());
    END IF;
    IF OLD.password_hash <> NEW.password_hash THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario', NEW.id_usuario, 'password_hash', '***', '***', NOW());
    END IF;
    IF OLD.autenticacion_dos_factores <> NEW.autenticacion_dos_factores THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario', NEW.id_usuario, 'autenticacion_dos_factores', OLD.autenticacion_dos_factores, NEW.autenticacion_dos_factores, NOW());
    END IF;
    IF OLD.activo <> NEW.activo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario', NEW.id_usuario, 'activo', OLD.activo, NEW.activo, NOW());
    END IF;
    IF OLD.id_rol <> NEW.id_rol THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario', NEW.id_usuario, 'id_rol', IFNULL(OLD.id_rol,''), IFNULL(NEW.id_rol,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_usuario_delete
AFTER DELETE ON Usuario
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios
    (id_usuario_responsable, tipo_objeto, id_objeto, campo, valor_antiguo, valor_nuevo, fecha_cambio)
    VALUES
    (@id_usuario_responsable, 'Usuario', OLD.id_usuario, 'ELIMINACIÓN',
     CONCAT('nombre_completo=', OLD.nombre_completo, '; correo=', OLD.correo, '; telefono=', IFNULL(OLD.telefono,'')),
     '', NOW());
END //

/* ------------------ Cuestionario ------------------ */
CREATE TRIGGER tr_cuestionario_insert
AFTER INSERT ON Cuestionario
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Cuestionario', NEW.id_cuestionario, 'CREACIÓN', '', CONCAT('nombre=', NEW.nombre, '; version=', NEW.version, '; estado=', IFNULL(NEW.estado,'')), NOW());
END //

CREATE TRIGGER tr_cuestionario_update
AFTER UPDATE ON Cuestionario
FOR EACH ROW
BEGIN
    IF OLD.nombre <> NEW.nombre THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Cuestionario', NEW.id_cuestionario, 'nombre', OLD.nombre, NEW.nombre, NOW());
    END IF;
    IF OLD.descripcion <> NEW.descripcion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Cuestionario', NEW.id_cuestionario, 'descripcion', IFNULL(OLD.descripcion,''), IFNULL(NEW.descripcion,''), NOW());
    END IF;
    IF OLD.version <> NEW.version THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Cuestionario', NEW.id_cuestionario, 'version', IFNULL(OLD.version,''), IFNULL(NEW.version,''), NOW());
    END IF;
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Cuestionario', NEW.id_cuestionario, 'estado', IFNULL(OLD.estado,''), IFNULL(NEW.estado,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_cuestionario_delete
AFTER DELETE ON Cuestionario
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Cuestionario', OLD.id_cuestionario, 'ELIMINACIÓN', CONCAT('nombre=', OLD.nombre, '; version=', OLD.version), '', NOW());
END //

/* ------------------ Pregunta ------------------ */
CREATE TRIGGER tr_pregunta_insert
AFTER INSERT ON Pregunta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Pregunta', NEW.id_pregunta, 'CREACIÓN', '', CONCAT('texto=', LEFT(NEW.texto_pregunta,200), '; id_cuestionario=', NEW.id_cuestionario), NOW());
END //

CREATE TRIGGER tr_pregunta_update
AFTER UPDATE ON Pregunta
FOR EACH ROW
BEGIN
    IF OLD.texto_pregunta <> NEW.texto_pregunta THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Pregunta', NEW.id_pregunta, 'texto_pregunta', LEFT(OLD.texto_pregunta,255), LEFT(NEW.texto_pregunta,255), NOW());
    END IF;
    IF OLD.tipo_calificacion <> NEW.tipo_calificacion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Pregunta', NEW.id_pregunta, 'tipo_calificacion', IFNULL(OLD.tipo_calificacion,''), IFNULL(NEW.tipo_calificacion,''), NOW());
    END IF;
    IF OLD.orden <> NEW.orden THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Pregunta', NEW.id_pregunta, 'orden', IFNULL(OLD.orden,''), IFNULL(NEW.orden,''), NOW());
    END IF;
    IF OLD.puntaje_maximo <> NEW.puntaje_maximo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Pregunta', NEW.id_pregunta, 'puntaje_maximo', IFNULL(OLD.puntaje_maximo,''), IFNULL(NEW.puntaje_maximo,''), NOW());
    END IF;
    IF OLD.obligatoria <> NEW.obligatoria THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Pregunta', NEW.id_pregunta, 'obligatoria', OLD.obligatoria, NEW.obligatoria, NOW());
    END IF;
END //

CREATE TRIGGER tr_pregunta_delete
AFTER DELETE ON Pregunta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Pregunta', OLD.id_pregunta, 'ELIMINACIÓN', CONCAT('texto=', LEFT(OLD.texto_pregunta,200)), '', NOW());
END //

/* ------------------ Opcion_Respuesta ------------------ */
CREATE TRIGGER tr_opcion_insert
AFTER INSERT ON Opcion_Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Opcion_Respuesta', NEW.id_opcion, 'CREACIÓN', '', CONCAT('id_pregunta=', NEW.id_pregunta, '; etiqueta=', NEW.etiqueta, '; valor=', NEW.valor), NOW());
END //

CREATE TRIGGER tr_opcion_update
AFTER UPDATE ON Opcion_Respuesta
FOR EACH ROW
BEGIN
    IF OLD.etiqueta <> NEW.etiqueta THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Opcion_Respuesta', NEW.id_opcion, 'etiqueta', IFNULL(OLD.etiqueta,''), IFNULL(NEW.etiqueta,''), NOW());
    END IF;
    IF OLD.valor <> NEW.valor THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Opcion_Respuesta', NEW.id_opcion, 'valor', IFNULL(OLD.valor,''), IFNULL(NEW.valor,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_opcion_delete
AFTER DELETE ON Opcion_Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Opcion_Respuesta', OLD.id_opcion, 'ELIMINACIÓN', CONCAT('etiqueta=', OLD.etiqueta, '; valor=', OLD.valor), '', NOW());
END //

/* ------------------ Evaluacion ------------------ */
CREATE TRIGGER tr_evaluacion_insert
AFTER INSERT ON Evaluacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evaluacion', NEW.id_evaluacion, 'CREACIÓN', '', CONCAT('id_usuario=', NEW.id_usuario, '; id_cuestionario=', NEW.id_cuestionario, '; estado=', IFNULL(NEW.estado,'')), NOW());
END //

CREATE TRIGGER tr_evaluacion_update
AFTER UPDATE ON Evaluacion
FOR EACH ROW
BEGIN
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evaluacion', NEW.id_evaluacion, 'estado', IFNULL(OLD.estado,''), IFNULL(NEW.estado,''), NOW());
    END IF;
    IF OLD.id_usuario_aplicador <> NEW.id_usuario_aplicador THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evaluacion', NEW.id_evaluacion, 'id_usuario_aplicador', IFNULL(OLD.id_usuario_aplicador,''), IFNULL(NEW.id_usuario_aplicador,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_evaluacion_delete
AFTER DELETE ON Evaluacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evaluacion', OLD.id_evaluacion, 'ELIMINACIÓN', CONCAT('id_usuario=', OLD.id_usuario, '; id_cuestionario=', OLD.id_cuestionario), '', NOW());
END //

/* ------------------ Respuesta ------------------ */
CREATE TRIGGER tr_respuesta_insert
AFTER INSERT ON Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Respuesta', NEW.id_respuesta, 'CREACIÓN', '', CONCAT('id_pregunta=', NEW.id_pregunta, '; id_evaluacion=', NEW.id_evaluacion, '; id_opcion=', NEW.id_opcion_respuesta_select, '; valor=', NEW.valor), NOW());
END //

CREATE TRIGGER tr_respuesta_update
AFTER UPDATE ON Respuesta
FOR EACH ROW
BEGIN
    IF OLD.id_opcion_respuesta_select <> NEW.id_opcion_respuesta_select THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Respuesta', NEW.id_respuesta, 'id_opcion_respuesta_select', IFNULL(OLD.id_opcion_respuesta_select,''), IFNULL(NEW.id_opcion_respuesta_select,''), NOW());
    END IF;
    IF OLD.valor <> NEW.valor THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Respuesta', NEW.id_respuesta, 'valor', IFNULL(OLD.valor,''), IFNULL(NEW.valor,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_respuesta_delete
AFTER DELETE ON Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Respuesta', OLD.id_respuesta, 'ELIMINACIÓN', CONCAT('id_pregunta=', OLD.id_pregunta, '; id_evaluacion=', OLD.id_evaluacion, '; valor=', OLD.valor), '', NOW());
END //

/* ------------------ Rango_Interpretacion ------------------ */
CREATE TRIGGER tr_rango_insert
AFTER INSERT ON Rango_Interpretacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Rango_Interpretacion', NEW.id_rango, 'CREACIÓN', '', CONCAT('tipo=', NEW.tipo, '; objeto=', NEW.objeto, '; rango=[', NEW.rango_inferior, '-', NEW.rango_superior, ']'), NOW());
END //

CREATE TRIGGER tr_rango_update
AFTER UPDATE ON Rango_Interpretacion
FOR EACH ROW
BEGIN
    IF OLD.rango_inferior <> NEW.rango_inferior OR OLD.rango_superior <> NEW.rango_superior THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Rango_Interpretacion', NEW.id_rango, 'rango', CONCAT(OLD.rango_inferior,'-',OLD.rango_superior), CONCAT(NEW.rango_inferior,'-',NEW.rango_superior), NOW());
    END IF;
    IF OLD.nivel_riesgo <> NEW.nivel_riesgo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Rango_Interpretacion', NEW.id_rango, 'nivel_riesgo', IFNULL(OLD.nivel_riesgo,''), IFNULL(NEW.nivel_riesgo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_rango_delete
AFTER DELETE ON Rango_Interpretacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Rango_Interpretacion', OLD.id_rango, 'ELIMINACIÓN', CONCAT('tipo=', OLD.tipo, '; rango=[', OLD.rango_inferior, '-', OLD.rango_superior, ']'), '', NOW());
END //

/* ------------------ Resultado ------------------ */
CREATE TRIGGER tr_resultado_insert
AFTER INSERT ON Resultado
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Resultado', NEW.id_resultado, 'CREACIÓN', '', CONCAT('id_evaluacion=', NEW.id_evaluacion, '; puntaje=', NEW.puntaje_obtenido, '; nivel_riesgo=', NEW.nivel_riesgo), NOW());
END //

CREATE TRIGGER tr_resultado_update
AFTER UPDATE ON Resultado
FOR EACH ROW
BEGIN
    IF OLD.puntaje_obtenido <> NEW.puntaje_obtenido THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Resultado', NEW.id_resultado, 'puntaje_obtenido', IFNULL(OLD.puntaje_obtenido,''), IFNULL(NEW.puntaje_obtenido,''), NOW());
    END IF;
    IF OLD.nivel_riesgo <> NEW.nivel_riesgo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Resultado', NEW.id_resultado, 'nivel_riesgo', IFNULL(OLD.nivel_riesgo,''), IFNULL(NEW.nivel_riesgo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_resultado_delete
AFTER DELETE ON Resultado
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Resultado', OLD.id_resultado, 'ELIMINACIÓN', CONCAT('id_evaluacion=', OLD.id_evaluacion, '; puntaje=', OLD.puntaje_obtenido), '', NOW());
END //

/* ------------------ Medida ------------------ */
CREATE TRIGGER tr_medida_insert
AFTER INSERT ON Medida
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Medida', NEW.id_medida, 'CREACIÓN', '', CONCAT('id_resultado=', NEW.id_resultado, '; tipo_medida=', NEW.tipo_medida, '; estado=', IFNULL(NEW.estado,'')), NOW());
END //

CREATE TRIGGER tr_medida_update
AFTER UPDATE ON Medida
FOR EACH ROW
BEGIN
    IF OLD.tipo_medida <> NEW.tipo_medida THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Medida', NEW.id_medida, 'tipo_medida', IFNULL(OLD.tipo_medida,''), IFNULL(NEW.tipo_medida,''), NOW());
    END IF;
    IF OLD.descripcion <> NEW.descripcion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Medida', NEW.id_medida, 'descripcion', IFNULL(OLD.descripcion,''), IFNULL(NEW.descripcion,''), NOW());
    END IF;
    IF OLD.id_usuario_responsable <> NEW.id_usuario_responsable THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Medida', NEW.id_medida, 'id_usuario_responsable', IFNULL(OLD.id_usuario_responsable,''), IFNULL(NEW.id_usuario_responsable,''), NOW());
    END IF;
    IF OLD.fecha_limite <> NEW.fecha_limite THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Medida', NEW.id_medida, 'fecha_limite', IFNULL(OLD.fecha_limite,''), IFNULL(NEW.fecha_limite,''), NOW());
    END IF;
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Medida', NEW.id_medida, 'estado', IFNULL(OLD.estado,''), IFNULL(NEW.estado,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_medida_delete
AFTER DELETE ON Medida
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Medida', OLD.id_medida, 'ELIMINACIÓN', CONCAT('id_resultado=', OLD.id_resultado, '; tipo_medida=', OLD.tipo_medida), '', NOW());
END //

/* ------------------ Evidencia ------------------ */
CREATE TRIGGER tr_evidencia_insert
AFTER INSERT ON Evidencia
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evidencia', NEW.id_evidencia, 'CREACIÓN', '', CONCAT('id_medida=', NEW.id_medida, '; tipo_archivo=', NEW.tipo_archivo, '; ruta=', NEW.ruta_archivo), NOW());
END //

CREATE TRIGGER tr_evidencia_update
AFTER UPDATE ON Evidencia
FOR EACH ROW
BEGIN
    IF OLD.ruta_archivo <> NEW.ruta_archivo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evidencia', NEW.id_evidencia, 'ruta_archivo', IFNULL(OLD.ruta_archivo,''), IFNULL(NEW.ruta_archivo,''), NOW());
    END IF;
    IF OLD.tipo_archivo <> NEW.tipo_archivo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evidencia', NEW.id_evidencia, 'tipo_archivo', IFNULL(OLD.tipo_archivo,''), IFNULL(NEW.tipo_archivo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_evidencia_delete
AFTER DELETE ON Evidencia
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Evidencia', OLD.id_evidencia, 'ELIMINACIÓN', CONCAT('id_medida=', OLD.id_medida, '; ruta=', OLD.ruta_archivo), '', NOW());
END //

/* ------------------ Capacitacion ------------------ */
CREATE TRIGGER tr_capacitacion_insert
AFTER INSERT ON Capacitacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Capacitacion', NEW.id_capacitacion, 'CREACIÓN', '', CONCAT('tema=', NEW.tema, '; fecha_inicio=', NEW.fecha_inicio, '; fecha_fin=', NEW.fecha_fin), NOW());
END //

CREATE TRIGGER tr_capacitacion_update
AFTER UPDATE ON Capacitacion
FOR EACH ROW
BEGIN
    IF OLD.tema <> NEW.tema THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Capacitacion', NEW.id_capacitacion, 'tema', IFNULL(OLD.tema,''), IFNULL(NEW.tema,''), NOW());
    END IF;
    IF OLD.fecha_inicio <> NEW.fecha_inicio OR OLD.fecha_fin <> NEW.fecha_fin THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Capacitacion', NEW.id_capacitacion, 'fechas', CONCAT(OLD.fecha_inicio,' - ',OLD.fecha_fin), CONCAT(NEW.fecha_inicio,' - ',NEW.fecha_fin), NOW());
    END IF;
END //

CREATE TRIGGER tr_capacitacion_delete
AFTER DELETE ON Capacitacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Capacitacion', OLD.id_capacitacion, 'ELIMINACIÓN', CONCAT('tema=', OLD.tema, '; fechas=', OLD.fecha_inicio,' - ',OLD.fecha_fin), '', NOW());
END //

/* ------------------ Confirmacion ------------------ */
CREATE TRIGGER tr_confirmacion_insert
AFTER INSERT ON Confirmacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Confirmacion', NEW.id_confirmacion, 'CREACIÓN', '', CONCAT('id_usuario=', NEW.id_usuario, '; id_capacitacion=', NEW.id_capacitacion, '; tipo_confirmacion=', NEW.tipo_confirmacion), NOW());
END //

CREATE TRIGGER tr_confirmacion_update
AFTER UPDATE ON Confirmacion
FOR EACH ROW
BEGIN
    IF OLD.tipo_confirmacion <> NEW.tipo_confirmacion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Confirmacion', NEW.id_confirmacion, 'tipo_confirmacion', IFNULL(OLD.tipo_confirmacion,''), IFNULL(NEW.tipo_confirmacion,''), NOW());
    END IF;
    IF OLD.asistio <> NEW.asistio THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Confirmacion', NEW.id_confirmacion, 'asistio', IFNULL(OLD.asistio,''), IFNULL(NEW.asistio,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_confirmacion_delete
AFTER DELETE ON Confirmacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Confirmacion', OLD.id_confirmacion, 'ELIMINACIÓN', CONCAT('id_usuario=', OLD.id_usuario, '; id_capacitacion=', OLD.id_capacitacion), '', NOW());
END //

/* ------------------ Documento ------------------ */
CREATE TRIGGER tr_documento_insert
AFTER INSERT ON Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Documento', NEW.id_documento, 'CREACIÓN', '', CONCAT('titulo=', NEW.titulo, '; ruta=', NEW.ruta_archivo, '; acceso_roles=', NEW.acceso_roles), NOW());
END //

CREATE TRIGGER tr_documento_update
AFTER UPDATE ON Documento
FOR EACH ROW
BEGIN
    IF OLD.titulo <> NEW.titulo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Documento', NEW.id_documento, 'titulo', IFNULL(OLD.titulo,''), IFNULL(NEW.titulo,''), NOW());
    END IF;
    IF OLD.ruta_archivo <> NEW.ruta_archivo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Documento', NEW.id_documento, 'ruta_archivo', IFNULL(OLD.ruta_archivo,''), IFNULL(NEW.ruta_archivo,''), NOW());
    END IF;
    IF OLD.acceso_roles <> NEW.acceso_roles THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Documento', NEW.id_documento, 'acceso_roles', IFNULL(OLD.acceso_roles,''), IFNULL(NEW.acceso_roles,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_documento_delete
AFTER DELETE ON Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Documento', OLD.id_documento, 'ELIMINACIÓN', CONCAT('titulo=', OLD.titulo, '; ruta=', OLD.ruta_archivo), '', NOW());
END //

/* ------------------ Usuario_Documento ------------------ */
CREATE TRIGGER tr_usuario_documento_insert
AFTER INSERT ON Usuario_Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario_Documento', NEW.id_usuario_doc, 'CREACIÓN', '', CONCAT('id_usuario=', NEW.id_usuario, '; id_documento=', NEW.id_documento, '; tipo_acceso=', NEW.tipo_acceso), NOW());
END //

CREATE TRIGGER tr_usuario_documento_update
AFTER UPDATE ON Usuario_Documento
FOR EACH ROW
BEGIN
    IF OLD.tipo_acceso <> NEW.tipo_acceso THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario_Documento', NEW.id_usuario_doc, 'tipo_acceso', IFNULL(OLD.tipo_acceso,''), IFNULL(NEW.tipo_acceso,''), NOW());
    END IF;
    IF OLD.firmado <> NEW.firmado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario_Documento', NEW.id_usuario_doc, 'firmado', IFNULL(OLD.firmado,''), IFNULL(NEW.firmado,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_usuario_documento_delete
AFTER DELETE ON Usuario_Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Usuario_Documento', OLD.id_usuario_doc, 'ELIMINACIÓN', CONCAT('id_usuario=', OLD.id_usuario, '; id_documento=', OLD.id_documento), '', NOW());
END //

/* ------------------ Regla_Calificacion ------------------ */
CREATE TRIGGER tr_regla_insert
AFTER INSERT ON Regla_Calificacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Regla_Calificacion', NEW.id_regla, 'CREACIÓN', '', CONCAT('id_cuestionario=', NEW.id_cuestionario, '; dimension=', NEW.dimension, '; nivel_riesgo=', NEW.nivel_riesgo), NOW());
END //

CREATE TRIGGER tr_regla_update
AFTER UPDATE ON Regla_Calificacion
FOR EACH ROW
BEGIN
    IF OLD.rango_inferior <> NEW.rango_inferior OR OLD.rango_superior <> NEW.rango_superior THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Regla_Calificacion', NEW.id_regla, 'rango', CONCAT(OLD.rango_inferior,'-',OLD.rango_superior), CONCAT(NEW.rango_inferior,'-',NEW.rango_superior), NOW());
    END IF;
    IF OLD.nivel_riesgo <> NEW.nivel_riesgo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Regla_Calificacion', NEW.id_regla, 'nivel_riesgo', IFNULL(OLD.nivel_riesgo,''), IFNULL(NEW.nivel_riesgo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_regla_delete
AFTER DELETE ON Regla_Calificacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, @id_usuario_responsable, 'Regla_Calificacion', OLD.id_regla, 'ELIMINACIÓN', CONCAT('id_cuestionario=', OLD.id_cuestionario, '; dimension=', OLD.dimension), '', NOW());
END //

/* ------------------ FIN de triggers ------------------ */

DELIMITER ;

SET @id_usuario_responsable = 1;

INSERT INTO Usuario (nombre_completo, correo, telefono, password_hash, autenticacion_dos_factores, activo, id_rol, fecha_registro)
VALUES ('Administrador', 'admin@correo.com', '123456789', '12345', '0', '1', '1', NOW());

INSERT INTO Usuario (nombre_completo, correo, telefono, password_hash, autenticacion_dos_factores, activo, id_rol, fecha_registro)
VALUES ('Psicólogo', 'psico@correo.com', '123456789', '12345', '0', '1', '2', NOW());

-- ========================
--  INSERTAR CUESTIONARIO
-- ========================
INSERT INTO Cuestionario (nombre, descripcion, version, estado, fecha_creacion)
VALUES (
    'Cuestionario para identificar los factores de riesgo psicosocial y evaluar el entorno organizacional',
    'Instrumento oficial basado en la NOM-035-STPS-2018 para identificar factores de riesgo psicosocial y evaluar el entorno organizacional.',
    'activo',
    NOW(),
    1
);

-- ========================
--  INSERTAR PREGUNTAS
-- ========================
SET @id_cuestionario := (SELECT id_cuestionario FROM Cuestionario ORDER BY id_cuestionario DESC LIMIT 1);

INSERT INTO Pregunta (
    id_cuestionario, texto_pregunta, tipo_calificacion, orden, puntaje_maximo,
    obligatoria, dimension, dominio, categoria, grupo_aplicacion, id_pregunta_dependeDe, condicion
) VALUES
(@id_cuestionario, 'El espacio donde trabajo me permite realizar mis actividades de manera segura e higiénica', 'Likert', 1, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo me exige hacer mucho esfuerzo físico', 'Likert', 2, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me preocupa sufrir un accidente en mi trabajo', 'Likert', 3, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Considero que en mi trabajo se aplican las normas de seguridad y salud en el trabajo', 'Likert', 4, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Considero que las actividades que realizo son peligrosas', 'Likert', 5, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Por la cantidad de trabajo que tengo debo quedarme tiempo adicional a mi turno', 'Likert', 6, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Por la cantidad de trabajo que tengo debo trabajar sin parar', 'Likert', 7, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Considero que es necesario mantener un ritmo de trabajo acelerado', 'Likert', 8, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo exige que esté muy concentrado', 'Likert', 9, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo requiere que memorice mucha información', 'Likert', 10, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En mi trabajo tengo que tomar decisiones difíciles muy rápido', 'Likert', 11, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo exige que atienda varios asuntos al mismo tiempo', 'Likert', 12, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En mi trabajo soy responsable de cosas de mucho valor', 'Likert', 13, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Respondo ante mi jefe por los resultados de toda mi área de trabajo', 'Likert', 14, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En el trabajo me dan órdenes contradictorias', 'Likert', 15, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Considero que en mi trabajo me piden hacer cosas innecesarias', 'Likert', 16, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Trabajo horas extras más de tres veces a la semana', 'Likert', 17, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo me exige laborar en días de descanso, festivos o fines de semana', 'Likert', 18, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Considero que el tiempo en el trabajo es mucho y perjudica mis actividades familiares o personales', 'Likert', 19, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Debo atender asuntos de trabajo cuando estoy en casa', 'Likert', 20, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Pienso en las actividades familiares o personales cuando estoy en mi trabajo', 'Likert', 21, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Pienso que mis responsabilidades familiares afectan mi trabajo', 'Likert', 22, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo permite que desarrolle nuevas habilidades', 'Likert', 23, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En mi trabajo puedo aspirar a un mejor puesto', 'Likert', 24, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Durante mi jornada de trabajo puedo tomar pausas cuando las necesito', 'Likert', 25, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Puedo decidir cuánto trabajo realizo durante la jornada laboral', 'Likert', 26, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Puedo decidir la velocidad a la que realizo mis actividades en mi trabajo', 'Likert', 27, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Puedo cambiar el orden de las actividades que realizo en mi trabajo', 'Likert', 28, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Los cambios que se presentan en mi trabajo dificultan mi labor', 'Likert', 29, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Cuando se presentan cambios en mi trabajo se tienen en cuenta mis ideas o aportaciones', 'Likert', 30, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me informan con claridad cuáles son mis funciones', 'Likert', 31, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me explican claramente los resultados que debo obtener en mi trabajo', 'Likert', 32, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me explican claramente los objetivos de mi trabajo', 'Likert', 33, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me informan con quién puedo resolver problemas o asuntos de trabajo', 'Likert', 34, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me permiten asistir a capacitaciones relacionadas con mi trabajo', 'Likert', 35, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Recibo capacitación útil para hacer mi trabajo', 'Likert', 36, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi jefe ayuda a organizar mejor el trabajo', 'Likert', 37, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi jefe tiene en cuenta mis puntos de vista y opiniones', 'Likert', 38, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi jefe me comunica a tiempo la información relacionada con el trabajo', 'Likert', 39, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'La orientación que me da mi jefe me ayuda a realizar mejor mi trabajo', 'Likert', 40, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi jefe ayuda a solucionar los problemas que se presentan en el trabajo', 'Likert', 41, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Puedo confiar en mis compañeros de trabajo', 'Likert', 42, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Entre compañeros solucionamos los problemas de trabajo de forma respetuosa', 'Likert', 43, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En mi trabajo me hacen sentir parte del grupo', 'Likert', 44, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Cuando tenemos que realizar trabajo de equipo los compañeros colaboran', 'Likert', 45, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mis compañeros de trabajo me ayudan cuando tengo dificultades', 'Likert', 46, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me informan sobre lo que hago bien en mi trabajo', 'Likert', 47, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'La forma como evalúan mi trabajo en mi centro de trabajo me ayuda a mejorar mi desempeño', 'Likert', 48, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En mi centro de trabajo me pagan a tiempo mi salario', 'Likert', 49, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'El pago que recibo es el que merezco por el trabajo que realizo', 'Likert', 50, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Si obtengo los resultados esperados en mi trabajo me recompensan o reconocen', 'Likert', 51, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Las personas que hacen bien el trabajo pueden crecer laboralmente', 'Likert', 52, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Considero que mi trabajo es estable', 'Likert', 53, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En mi trabajo existe continua rotación de personal', 'Likert', 54, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Siento orgullo de laborar en este centro de trabajo', 'Likert', 55, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me siento comprometido con mi trabajo', 'Likert', 56, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'En mi trabajo puedo expresarme libremente sin interrupciones', 'Likert', 57, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Recibo críticas constantes a mi persona y/o trabajo', 'Likert', 58, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Recibo burlas, calumnias, difamaciones, humillaciones o ridiculizaciones', 'Likert', 59, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Se ignora mi presencia o se me excluye de las reuniones de trabajo y en la toma de decisiones', 'Likert', 60, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Se manipulan las situaciones de trabajo para hacerme parecer un mal trabajador', 'Likert', 61, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Se ignoran mis éxitos laborales y se atribuyen a otros trabajadores', 'Likert', 62, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Me bloquean o impiden las oportunidades que tengo para obtener ascenso o mejora en mi trabajo', 'Likert', 63, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'He presenciado actos de violencia en mi centro de trabajo', 'Likert', 64, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Atiendo clientes o usuarios muy enojados', 'Likert', 65, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo me exige atender personas muy necesitadas de ayuda o enfermas', 'Likert', 66, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Para hacer mi trabajo debo demostrar sentimientos distintos a los míos', 'Likert', 67, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Mi trabajo me exige atender situaciones de violencia', 'Likert', 68, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Comunican tarde los asuntos de trabajo', 'Likert', 69, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Dificultan el logro de los resultados del trabajo', 'Likert', 70, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Cooperan poco cuando se necesita', 'Likert', 71, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna'),
(@id_cuestionario, 'Ignoran las sugerencias para mejorar su trabajo', 'Likert', 72, 4, TRUE, 'General', 'N/A', 'N/A', 'Trabajador', NULL, 'Ninguna');

-- ========================
--  ASIGNAR OPCIONES A TODAS LAS PREGUNTAS
-- ========================
INSERT INTO Opcion_Respuesta (id_pregunta, etiqueta, valor)
SELECT 
    p.id_pregunta, 
    o.etiqueta, 
    o.valor
FROM Pregunta p
CROSS JOIN (
    SELECT 4 AS valor, 'Siempre' AS etiqueta
    UNION ALL SELECT 3, 'Casi siempre'
    UNION ALL SELECT 2, 'Algunas veces'
    UNION ALL SELECT 1, 'Casi nunca'
    UNION ALL SELECT 0, 'Nunca'
) o
WHERE p.id_pregunta NOT IN (SELECT DISTINCT id_pregunta FROM Opcion_Respuesta)
ORDER BY p.id_pregunta, o.valor DESC;
