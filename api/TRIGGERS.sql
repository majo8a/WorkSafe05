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
    (1, 'Usuario', NEW.id_usuario, 'CREACIÓN',
     '', CONCAT('nombre_completo=', NEW.nombre_completo, '; correo=', NEW.correo, '; telefono=', IFNULL(NEW.telefono,''), '; id_rol=', IFNULL(NEW.id_rol,'')),
     NOW());
END //

CREATE TRIGGER tr_usuario_update
AFTER UPDATE ON Usuario
FOR EACH ROW
BEGIN
    IF OLD.nombre_completo <> NEW.nombre_completo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario', NEW.id_usuario, 'nombre_completo', OLD.nombre_completo, NEW.nombre_completo, NOW());
    END IF;
    IF OLD.correo <> NEW.correo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario', NEW.id_usuario, 'correo', IFNULL(OLD.correo,''), IFNULL(NEW.correo,''), NOW());
    END IF;
    IF OLD.telefono <> NEW.telefono THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario', NEW.id_usuario, 'telefono', IFNULL(OLD.telefono,''), IFNULL(NEW.telefono,''), NOW());
    END IF;
    IF OLD.password_hash <> NEW.password_hash THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario', NEW.id_usuario, 'password_hash', '***', '***', NOW());
    END IF;
    IF OLD.autenticacion_dos_factores <> NEW.autenticacion_dos_factores THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario', NEW.id_usuario, 'autenticacion_dos_factores', OLD.autenticacion_dos_factores, NEW.autenticacion_dos_factores, NOW());
    END IF;
    IF OLD.activo <> NEW.activo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario', NEW.id_usuario, 'activo', OLD.activo, NEW.activo, NOW());
    END IF;
    IF OLD.id_rol <> NEW.id_rol THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario', NEW.id_usuario, 'id_rol', IFNULL(OLD.id_rol,''), IFNULL(NEW.id_rol,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_usuario_delete
AFTER DELETE ON Usuario
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios
    (id_usuario_responsable, tipo_objeto, id_objeto, campo, valor_antiguo, valor_nuevo, fecha_cambio)
    VALUES
    (1, 'Usuario', OLD.id_usuario, 'ELIMINACIÓN',
     CONCAT('nombre_completo=', OLD.nombre_completo, '; correo=', OLD.correo, '; telefono=', IFNULL(OLD.telefono,'')),
     '', NOW());
END //

/* ------------------ Cuestionario ------------------ */
CREATE TRIGGER tr_cuestionario_insert
AFTER INSERT ON Cuestionario
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Cuestionario', NEW.id_cuestionario, 'CREACIÓN', '', CONCAT('nombre=', NEW.nombre, '; version=', NEW.version, '; estado=', IFNULL(NEW.estado,'')), NOW());
END //

CREATE TRIGGER tr_cuestionario_update
AFTER UPDATE ON Cuestionario
FOR EACH ROW
BEGIN
    IF OLD.nombre <> NEW.nombre THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Cuestionario', NEW.id_cuestionario, 'nombre', OLD.nombre, NEW.nombre, NOW());
    END IF;
    IF OLD.descripcion <> NEW.descripcion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Cuestionario', NEW.id_cuestionario, 'descripcion', IFNULL(OLD.descripcion,''), IFNULL(NEW.descripcion,''), NOW());
    END IF;
    IF OLD.version <> NEW.version THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Cuestionario', NEW.id_cuestionario, 'version', IFNULL(OLD.version,''), IFNULL(NEW.version,''), NOW());
    END IF;
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Cuestionario', NEW.id_cuestionario, 'estado', IFNULL(OLD.estado,''), IFNULL(NEW.estado,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_cuestionario_delete
AFTER DELETE ON Cuestionario
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Cuestionario', OLD.id_cuestionario, 'ELIMINACIÓN', CONCAT('nombre=', OLD.nombre, '; version=', OLD.version), '', NOW());
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
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Pregunta', NEW.id_pregunta, 'texto_pregunta', LEFT(OLD.texto_pregunta,255), LEFT(NEW.texto_pregunta,255), NOW());
    END IF;
    IF OLD.tipo_calificacion <> NEW.tipo_calificacion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Pregunta', NEW.id_pregunta, 'tipo_calificacion', IFNULL(OLD.tipo_calificacion,''), IFNULL(NEW.tipo_calificacion,''), NOW());
    END IF;
    IF OLD.orden <> NEW.orden THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Pregunta', NEW.id_pregunta, 'orden', IFNULL(OLD.orden,''), IFNULL(NEW.orden,''), NOW());
    END IF;
    IF OLD.puntaje_maximo <> NEW.puntaje_maximo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Pregunta', NEW.id_pregunta, 'puntaje_maximo', IFNULL(OLD.puntaje_maximo,''), IFNULL(NEW.puntaje_maximo,''), NOW());
    END IF;
    IF OLD.obligatoria <> NEW.obligatoria THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Pregunta', NEW.id_pregunta, 'obligatoria', OLD.obligatoria, NEW.obligatoria, NOW());
    END IF;
END //

CREATE TRIGGER tr_pregunta_delete
AFTER DELETE ON Pregunta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Pregunta', OLD.id_pregunta, 'ELIMINACIÓN', CONCAT('texto=', LEFT(OLD.texto_pregunta,200)), '', NOW());
END //

/* ------------------ Opcion_Respuesta ------------------ */
CREATE TRIGGER tr_opcion_insert
AFTER INSERT ON Opcion_Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Opcion_Respuesta', NEW.id_opcion, 'CREACIÓN', '', CONCAT('id_pregunta=', NEW.id_pregunta, '; etiqueta=', NEW.etiqueta, '; valor=', NEW.valor), NOW());
END //

CREATE TRIGGER tr_opcion_update
AFTER UPDATE ON Opcion_Respuesta
FOR EACH ROW
BEGIN
    IF OLD.etiqueta <> NEW.etiqueta THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Opcion_Respuesta', NEW.id_opcion, 'etiqueta', IFNULL(OLD.etiqueta,''), IFNULL(NEW.etiqueta,''), NOW());
    END IF;
    IF OLD.valor <> NEW.valor THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Opcion_Respuesta', NEW.id_opcion, 'valor', IFNULL(OLD.valor,''), IFNULL(NEW.valor,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_opcion_delete
AFTER DELETE ON Opcion_Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Opcion_Respuesta', OLD.id_opcion, 'ELIMINACIÓN', CONCAT('etiqueta=', OLD.etiqueta, '; valor=', OLD.valor), '', NOW());
END //

/* ------------------ Evaluacion ------------------ */
CREATE TRIGGER tr_evaluacion_insert
AFTER INSERT ON Evaluacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evaluacion', NEW.id_evaluacion, 'CREACIÓN', '', CONCAT('id_usuario=', NEW.id_usuario, '; id_cuestionario=', NEW.id_cuestionario, '; estado=', IFNULL(NEW.estado,'')), NOW());
END //

CREATE TRIGGER tr_evaluacion_update
AFTER UPDATE ON Evaluacion
FOR EACH ROW
BEGIN
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evaluacion', NEW.id_evaluacion, 'estado', IFNULL(OLD.estado,''), IFNULL(NEW.estado,''), NOW());
    END IF;
    IF OLD.id_usuario_aplicador <> NEW.id_usuario_aplicador THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evaluacion', NEW.id_evaluacion, 'id_usuario_aplicador', IFNULL(OLD.id_usuario_aplicador,''), IFNULL(NEW.id_usuario_aplicador,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_evaluacion_delete
AFTER DELETE ON Evaluacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evaluacion', OLD.id_evaluacion, 'ELIMINACIÓN', CONCAT('id_usuario=', OLD.id_usuario, '; id_cuestionario=', OLD.id_cuestionario), '', NOW());
END //

/* ------------------ Respuesta ------------------ */
CREATE TRIGGER tr_respuesta_insert
AFTER INSERT ON Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Respuesta', NEW.id_respuesta, 'CREACIÓN', '', CONCAT('id_pregunta=', NEW.id_pregunta, '; id_evaluacion=', NEW.id_evaluacion, '; id_opcion=', NEW.id_opcion_respuesta_select, '; valor=', NEW.valor), NOW());
END //

CREATE TRIGGER tr_respuesta_update
AFTER UPDATE ON Respuesta
FOR EACH ROW
BEGIN
    IF OLD.id_opcion_respuesta_select <> NEW.id_opcion_respuesta_select THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Respuesta', NEW.id_respuesta, 'id_opcion_respuesta_select', IFNULL(OLD.id_opcion_respuesta_select,''), IFNULL(NEW.id_opcion_respuesta_select,''), NOW());
    END IF;
    IF OLD.valor <> NEW.valor THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Respuesta', NEW.id_respuesta, 'valor', IFNULL(OLD.valor,''), IFNULL(NEW.valor,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_respuesta_delete
AFTER DELETE ON Respuesta
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Respuesta', OLD.id_respuesta, 'ELIMINACIÓN', CONCAT('id_pregunta=', OLD.id_pregunta, '; id_evaluacion=', OLD.id_evaluacion, '; valor=', OLD.valor), '', NOW());
END //

/* ------------------ Rango_Interpretacion ------------------ */
CREATE TRIGGER tr_rango_insert
AFTER INSERT ON Rango_Interpretacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Rango_Interpretacion', NEW.id_rango, 'CREACIÓN', '', CONCAT('tipo=', NEW.tipo, '; objeto=', NEW.objeto, '; rango=[', NEW.rango_inferior, '-', NEW.rango_superior, ']'), NOW());
END //

CREATE TRIGGER tr_rango_update
AFTER UPDATE ON Rango_Interpretacion
FOR EACH ROW
BEGIN
    IF OLD.rango_inferior <> NEW.rango_inferior OR OLD.rango_superior <> NEW.rango_superior THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Rango_Interpretacion', NEW.id_rango, 'rango', CONCAT(OLD.rango_inferior,'-',OLD.rango_superior), CONCAT(NEW.rango_inferior,'-',NEW.rango_superior), NOW());
    END IF;
    IF OLD.nivel_riesgo <> NEW.nivel_riesgo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Rango_Interpretacion', NEW.id_rango, 'nivel_riesgo', IFNULL(OLD.nivel_riesgo,''), IFNULL(NEW.nivel_riesgo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_rango_delete
AFTER DELETE ON Rango_Interpretacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Rango_Interpretacion', OLD.id_rango, 'ELIMINACIÓN', CONCAT('tipo=', OLD.tipo, '; rango=[', OLD.rango_inferior, '-', OLD.rango_superior, ']'), '', NOW());
END //

/* ------------------ Resultado ------------------ */
CREATE TRIGGER tr_resultado_insert
AFTER INSERT ON Resultado
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Resultado', NEW.id_resultado, 'CREACIÓN', '', CONCAT('id_evaluacion=', NEW.id_evaluacion, '; puntaje=', NEW.puntaje_obtenido, '; nivel_riesgo=', NEW.nivel_riesgo), NOW());
END //

CREATE TRIGGER tr_resultado_update
AFTER UPDATE ON Resultado
FOR EACH ROW
BEGIN
    IF OLD.puntaje_obtenido <> NEW.puntaje_obtenido THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Resultado', NEW.id_resultado, 'puntaje_obtenido', IFNULL(OLD.puntaje_obtenido,''), IFNULL(NEW.puntaje_obtenido,''), NOW());
    END IF;
    IF OLD.nivel_riesgo <> NEW.nivel_riesgo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Resultado', NEW.id_resultado, 'nivel_riesgo', IFNULL(OLD.nivel_riesgo,''), IFNULL(NEW.nivel_riesgo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_resultado_delete
AFTER DELETE ON Resultado
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Resultado', OLD.id_resultado, 'ELIMINACIÓN', CONCAT('id_evaluacion=', OLD.id_evaluacion, '; puntaje=', OLD.puntaje_obtenido), '', NOW());
END //

/* ------------------ Medida ------------------ */
CREATE TRIGGER tr_medida_insert
AFTER INSERT ON Medida
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Medida', NEW.id_medida, 'CREACIÓN', '', CONCAT('id_resultado=', NEW.id_resultado, '; tipo_medida=', NEW.tipo_medida, '; estado=', IFNULL(NEW.estado,'')), NOW());
END //

CREATE TRIGGER tr_medida_update
AFTER UPDATE ON Medida
FOR EACH ROW
BEGIN
    IF OLD.tipo_medida <> NEW.tipo_medida THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Medida', NEW.id_medida, 'tipo_medida', IFNULL(OLD.tipo_medida,''), IFNULL(NEW.tipo_medida,''), NOW());
    END IF;
    IF OLD.descripcion <> NEW.descripcion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Medida', NEW.id_medida, 'descripcion', IFNULL(OLD.descripcion,''), IFNULL(NEW.descripcion,''), NOW());
    END IF;
    IF OLD.id_usuario_responsable <> NEW.id_usuario_responsable THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Medida', NEW.id_medida, 'id_usuario_responsable', IFNULL(OLD.id_usuario_responsable,''), IFNULL(NEW.id_usuario_responsable,''), NOW());
    END IF;
    IF OLD.fecha_limite <> NEW.fecha_limite THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Medida', NEW.id_medida, 'fecha_limite', IFNULL(OLD.fecha_limite,''), IFNULL(NEW.fecha_limite,''), NOW());
    END IF;
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Medida', NEW.id_medida, 'estado', IFNULL(OLD.estado,''), IFNULL(NEW.estado,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_medida_delete
AFTER DELETE ON Medida
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Medida', OLD.id_medida, 'ELIMINACIÓN', CONCAT('id_resultado=', OLD.id_resultado, '; tipo_medida=', OLD.tipo_medida), '', NOW());
END //

/* ------------------ Evidencia ------------------ */
CREATE TRIGGER tr_evidencia_insert
AFTER INSERT ON Evidencia
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evidencia', NEW.id_evidencia, 'CREACIÓN', '', CONCAT('id_medida=', NEW.id_medida, '; tipo_archivo=', NEW.tipo_archivo, '; ruta=', NEW.ruta_archivo), NOW());
END //

CREATE TRIGGER tr_evidencia_update
AFTER UPDATE ON Evidencia
FOR EACH ROW
BEGIN
    IF OLD.ruta_archivo <> NEW.ruta_archivo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evidencia', NEW.id_evidencia, 'ruta_archivo', IFNULL(OLD.ruta_archivo,''), IFNULL(NEW.ruta_archivo,''), NOW());
    END IF;
    IF OLD.tipo_archivo <> NEW.tipo_archivo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evidencia', NEW.id_evidencia, 'tipo_archivo', IFNULL(OLD.tipo_archivo,''), IFNULL(NEW.tipo_archivo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_evidencia_delete
AFTER DELETE ON Evidencia
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Evidencia', OLD.id_evidencia, 'ELIMINACIÓN', CONCAT('id_medida=', OLD.id_medida, '; ruta=', OLD.ruta_archivo), '', NOW());
END //

/* ------------------ Capacitacion ------------------ */
CREATE TRIGGER tr_capacitacion_insert
AFTER INSERT ON Capacitacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Capacitacion', NEW.id_capacitacion, 'CREACIÓN', '', CONCAT('tema=', NEW.tema, '; fecha_inicio=', NEW.fecha_inicio, '; fecha_fin=', NEW.fecha_fin), NOW());
END //

CREATE TRIGGER tr_capacitacion_update
AFTER UPDATE ON Capacitacion
FOR EACH ROW
BEGIN
    IF OLD.tema <> NEW.tema THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Capacitacion', NEW.id_capacitacion, 'tema', IFNULL(OLD.tema,''), IFNULL(NEW.tema,''), NOW());
    END IF;
    IF OLD.fecha_inicio <> NEW.fecha_inicio OR OLD.fecha_fin <> NEW.fecha_fin THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Capacitacion', NEW.id_capacitacion, 'fechas', CONCAT(OLD.fecha_inicio,' - ',OLD.fecha_fin), CONCAT(NEW.fecha_inicio,' - ',NEW.fecha_fin), NOW());
    END IF;
END //

CREATE TRIGGER tr_capacitacion_delete
AFTER DELETE ON Capacitacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Capacitacion', OLD.id_capacitacion, 'ELIMINACIÓN', CONCAT('tema=', OLD.tema, '; fechas=', OLD.fecha_inicio,' - ',OLD.fecha_fin), '', NOW());
END //

/* ------------------ Confirmacion ------------------ */
CREATE TRIGGER tr_confirmacion_insert
AFTER INSERT ON Confirmacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Confirmacion', NEW.id_confirmacion, 'CREACIÓN', '', CONCAT('id_usuario=', NEW.id_usuario, '; id_capacitacion=', NEW.id_capacitacion, '; tipo_confirmacion=', NEW.tipo_confirmacion), NOW());
END //

CREATE TRIGGER tr_confirmacion_update
AFTER UPDATE ON Confirmacion
FOR EACH ROW
BEGIN
    IF OLD.tipo_confirmacion <> NEW.tipo_confirmacion THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Confirmacion', NEW.id_confirmacion, 'tipo_confirmacion', IFNULL(OLD.tipo_confirmacion,''), IFNULL(NEW.tipo_confirmacion,''), NOW());
    END IF;
    IF OLD.asistio <> NEW.asistio THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Confirmacion', NEW.id_confirmacion, 'asistio', IFNULL(OLD.asistio,''), IFNULL(NEW.asistio,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_confirmacion_delete
AFTER DELETE ON Confirmacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Confirmacion', OLD.id_confirmacion, 'ELIMINACIÓN', CONCAT('id_usuario=', OLD.id_usuario, '; id_capacitacion=', OLD.id_capacitacion), '', NOW());
END //

/* ------------------ Documento ------------------ */
CREATE TRIGGER tr_documento_insert
AFTER INSERT ON Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Documento', NEW.id_documento, 'CREACIÓN', '', CONCAT('titulo=', NEW.titulo, '; ruta=', NEW.ruta_archivo, '; acceso_roles=', NEW.acceso_roles), NOW());
END //

CREATE TRIGGER tr_documento_update
AFTER UPDATE ON Documento
FOR EACH ROW
BEGIN
    IF OLD.titulo <> NEW.titulo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Documento', NEW.id_documento, 'titulo', IFNULL(OLD.titulo,''), IFNULL(NEW.titulo,''), NOW());
    END IF;
    IF OLD.ruta_archivo <> NEW.ruta_archivo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Documento', NEW.id_documento, 'ruta_archivo', IFNULL(OLD.ruta_archivo,''), IFNULL(NEW.ruta_archivo,''), NOW());
    END IF;
    IF OLD.acceso_roles <> NEW.acceso_roles THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Documento', NEW.id_documento, 'acceso_roles', IFNULL(OLD.acceso_roles,''), IFNULL(NEW.acceso_roles,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_documento_delete
AFTER DELETE ON Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Documento', OLD.id_documento, 'ELIMINACIÓN', CONCAT('titulo=', OLD.titulo, '; ruta=', OLD.ruta_archivo), '', NOW());
END //

/* ------------------ Usuario_Documento ------------------ */
CREATE TRIGGER tr_usuario_documento_insert
AFTER INSERT ON Usuario_Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario_Documento', NEW.id_usuario_doc, 'CREACIÓN', '', CONCAT('id_usuario=', NEW.id_usuario, '; id_documento=', NEW.id_documento, '; tipo_acceso=', NEW.tipo_acceso), NOW());
END //

CREATE TRIGGER tr_usuario_documento_update
AFTER UPDATE ON Usuario_Documento
FOR EACH ROW
BEGIN
    IF OLD.tipo_acceso <> NEW.tipo_acceso THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario_Documento', NEW.id_usuario_doc, 'tipo_acceso', IFNULL(OLD.tipo_acceso,''), IFNULL(NEW.tipo_acceso,''), NOW());
    END IF;
    IF OLD.firmado <> NEW.firmado THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario_Documento', NEW.id_usuario_doc, 'firmado', IFNULL(OLD.firmado,''), IFNULL(NEW.firmado,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_usuario_documento_delete
AFTER DELETE ON Usuario_Documento
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Usuario_Documento', OLD.id_usuario_doc, 'ELIMINACIÓN', CONCAT('id_usuario=', OLD.id_usuario, '; id_documento=', OLD.id_documento), '', NOW());
END //

/* ------------------ Regla_Calificacion ------------------ */
CREATE TRIGGER tr_regla_insert
AFTER INSERT ON Regla_Calificacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Regla_Calificacion', NEW.id_regla, 'CREACIÓN', '', CONCAT('id_cuestionario=', NEW.id_cuestionario, '; dimension=', NEW.dimension, '; nivel_riesgo=', NEW.nivel_riesgo), NOW());
END //

CREATE TRIGGER tr_regla_update
AFTER UPDATE ON Regla_Calificacion
FOR EACH ROW
BEGIN
    IF OLD.rango_inferior <> NEW.rango_inferior OR OLD.rango_superior <> NEW.rango_superior THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Regla_Calificacion', NEW.id_regla, 'rango', CONCAT(OLD.rango_inferior,'-',OLD.rango_superior), CONCAT(NEW.rango_inferior,'-',NEW.rango_superior), NOW());
    END IF;
    IF OLD.nivel_riesgo <> NEW.nivel_riesgo THEN
        INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Regla_Calificacion', NEW.id_regla, 'nivel_riesgo', IFNULL(OLD.nivel_riesgo,''), IFNULL(NEW.nivel_riesgo,''), NOW());
    END IF;
END //

CREATE TRIGGER tr_regla_delete
AFTER DELETE ON Regla_Calificacion
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Cambios VALUES (NULL, 1, 'Regla_Calificacion', OLD.id_regla, 'ELIMINACIÓN', CONCAT('id_cuestionario=', OLD.id_cuestionario, '; dimension=', OLD.dimension), '', NOW());
END //

/* ------------------ FIN de triggers ------------------ */

DELIMITER ;