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
    codigo_recuperacion VARCHAR(10) NULL,
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

-- CREATE TABLE IF NOT EXISTS TwoFactorCodes (
--   id_code INT AUTO_INCREMENT PRIMARY KEY,
--   id_usuario INT NOT NULL,
--   code_hash VARCHAR(255) NOT NULL, -- guardamos hash del código por seguridad
--   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   expires_at DATETIME NOT NULL,
--   used BIT DEFAULT 0,
--   attempts INT DEFAULT 0,
--   ip_origen VARCHAR(100),
--   FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
-- );


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
