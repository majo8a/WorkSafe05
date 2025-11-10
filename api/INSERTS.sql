
-- USUARIOS
INSERT INTO Rol (nombre_rol, descripcion) 
VALUES ('Administrador', 'Usuario con control total del sistema, gestiona usuarios, configuraciones y datos.');

INSERT INTO Rol (nombre_rol, descripcion) 
VALUES ('Psicólogo', 'Usuario encargado de aplicar cuestionarios, evaluar resultados y dar seguimiento.');

INSERT INTO Rol (nombre_rol, descripcion) 
VALUES ('Usuario', 'Usuario común que responde cuestionarios y consulta resultados personales.');

--  INSERTAR CUESTIONARIO

INSERT INTO Cuestionario (nombre, descripcion, version, estado, fecha_creacion)
VALUES (
    'Cuestionario para identificar los factores de riesgo psicosocial y evaluar el entorno organizacional',
    'Instrumento oficial basado en la NOM-035-STPS-2018 para identificar factores de riesgo psicosocial y evaluar el entorno organizacional.',
    'activo',
    NOW(),
    1
);

--  INSERTAR PREGUNTAS
SET @id_cuestionario := (SELECT id_cuestionario FROM Cuestionario ORDER BY id_cuestionario DESC LIMIT 1);

INSERT INTO Pregunta (
  id_cuestionario, texto_pregunta, tipo_calificacion, orden, puntaje_maximo,
  obligatoria, dimension, dominio, categoria, grupo_aplicacion,
  id_pregunta_dependeDe, condicion
) VALUES

(@id_cuestionario, 'El espacio donde trabajo me permite realizar mis actividades de manera segura e higiénica',
'Likert', 1, 4, TRUE, 'Condiciones del ambiente de trabajo', 'Condiciones peligrosas e inseguras',
'Condiciones en el ambiente de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo me exige hacer mucho esfuerzo físico',
'Likert', 2, 4, TRUE, 'Carga de trabajo', 'Exigencias físicas del trabajo',
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me preocupa sufrir un accidente en mi trabajo',
'Likert', 3, 4, TRUE, 'Condiciones del ambiente de trabajo', 'Condiciones peligrosas e inseguras',
'Condiciones en el ambiente de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Considero que en mi trabajo se aplican las normas de seguridad y salud en el trabajo',
'Likert', 4, 4, TRUE, 'Condiciones del ambiente de trabajo', 'Condiciones peligrosas e inseguras',
'Condiciones en el ambiente de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Considero que las actividades que realizo son peligrosas',
'Likert', 5, 4, TRUE, 'Condiciones del ambiente de trabajo', 'Condiciones peligrosas e inseguras',
'Condiciones en el ambiente de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Por la cantidad de trabajo que tengo debo quedarme tiempo adicional a mi turno',
'Likert', 6, 4, TRUE, 'Carga de trabajo', 'Cargas cuantitativas', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Por la cantidad de trabajo que tengo debo trabajar sin parar',
'Likert', 7, 4, TRUE, 'Carga de trabajo', 'Cargas cuantitativas', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Considero que es necesario mantener un ritmo de trabajo acelerado',
'Likert', 8, 4, TRUE, 'Carga de trabajo', 'Cargas cuantitativas', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo exige que esté muy concentrado',
'Likert', 9, 4, TRUE, 'Carga de trabajo', 'Cargas mentales', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo requiere que memorice mucha información',
'Likert', 10, 4, TRUE, 'Carga de trabajo', 'Cargas mentales', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En mi trabajo tengo que tomar decisiones difíciles muy rápido',
'Likert', 11, 4, TRUE, 'Carga de trabajo', 'Cargas mentales', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo exige que atienda varios asuntos al mismo tiempo',
'Likert', 12, 4, TRUE, 'Carga de trabajo', 'Cargas mentales', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En mi trabajo soy responsable de cosas de mucho valor',
'Likert', 13, 4, TRUE, 'Carga de trabajo', 'Cargas de responsabilidad', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Respondo ante mi jefe por los resultados de toda mi área de trabajo',
'Likert', 14, 4, TRUE, 'Carga de trabajo', 'Cargas de responsabilidad', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En el trabajo me dan órdenes contradictorias',
'Likert', 15, 4, TRUE, 'Falta de control sobre el trabajo', 'Interferencia en la relación trabajo–familia', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Considero que en mi trabajo me piden hacer cosas innecesarias',
'Likert', 16, 4, TRUE, 'Falta de control sobre el trabajo', 'Interferencia en la relación trabajo–familia', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Trabajo horas extras más de tres veces a la semana',
'Likert', 17, 4, TRUE, 'Carga de trabajo', 'Cargas cuantitativas', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo me exige laborar en días de descanso, festivos o fines de semana',
'Likert', 18, 4, TRUE, 'Carga de trabajo', 'Cargas cuantitativas', 
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Considero que el tiempo en el trabajo es mucho y perjudica mis actividades familiares o personales',
'Likert', 19, 4, TRUE, 'Falta de control sobre el trabajo', 'Interferencia en la relación trabajo–familia',
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Debo atender asuntos de trabajo cuando estoy en casa',
'Likert', 20, 4, TRUE, 'Falta de control sobre el trabajo', 'Interferencia en la relación trabajo–familia',
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Pienso en las actividades familiares o personales cuando estoy en mi trabajo',
'Likert', 21, 4, TRUE, 'Falta de control sobre el trabajo', 'Interferencia en la relación trabajo–familia',
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Pienso que mis responsabilidades familiares afectan mi trabajo',
'Likert', 22, 4, TRUE, 'Falta de control sobre el trabajo', 'Interferencia en la relación trabajo–familia',
'Factores propios de la actividad', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo permite que desarrolle nuevas habilidades',
'Likert', 23, 4, TRUE, 'Falta de control sobre el trabajo', 'Posibilidades de desarrollo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En mi trabajo puedo aspirar a un mejor puesto',
'Likert', 24, 4, TRUE, 'Falta de control sobre el trabajo', 'Posibilidades de desarrollo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Durante mi jornada de trabajo puedo tomar pausas cuando las necesito',
'Likert', 25, 4, TRUE, 'Falta de control sobre el trabajo', 'Falta de control sobre el trabajo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Puedo decidir cuánto trabajo realizo durante la jornada laboral',
'Likert', 26, 4, TRUE, 'Falta de control sobre el trabajo', 'Falta de control sobre el trabajo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Puedo decidir la velocidad a la que realizo mis actividades en mi trabajo',
'Likert', 27, 4, TRUE, 'Falta de control sobre el trabajo', 'Falta de control sobre el trabajo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Puedo cambiar el orden de las actividades que realizo en mi trabajo',
'Likert', 28, 4, TRUE, 'Falta de control sobre el trabajo', 'Falta de control sobre el trabajo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Los cambios que se presentan en mi trabajo dificultan mi labor',
'Likert', 29, 4, TRUE, 'Falta de control sobre el trabajo', 'Cambios no previstos en el trabajo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Cuando se presentan cambios en mi trabajo se tienen en cuenta mis ideas o aportaciones',
'Likert', 30, 4, TRUE, 'Falta de control sobre el trabajo', 'Cambios no previstos en el trabajo',
'Organización del tiempo de trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me informan con claridad cuáles son mis funciones',
'Likert', 31, 4, TRUE, 'Falta de control sobre el trabajo', 'Definición de responsabilidades',
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me explican claramente los resultados que debo obtener en mi trabajo',
'Likert', 32, 4, TRUE, 'Falta de control sobre el trabajo', 'Definición de responsabilidades',
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me explican claramente los objetivos de mi trabajo',
'Likert', 33, 4, TRUE, 'Falta de control sobre el trabajo', 'Definición de responsabilidades',
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me informan con quién puedo resolver problemas o asuntos de trabajo',
'Likert', 34, 4, TRUE, 'Falta de control sobre el trabajo', 'Definición de responsabilidades',
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me permiten asistir a capacitaciones relacionadas con mi trabajo',
'Likert', 35, 4, TRUE, 'Falta de control sobre el trabajo', 'Capacitación',
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Recibo capacitación útil para hacer mi trabajo',
'Likert', 36, 4, TRUE, 'Falta de control sobre el trabajo', 'Capacitación',
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi jefe ayuda a organizar mejor el trabajo',
'Likert', 37, 4, TRUE, 'Liderazgo', 'Escasa claridad de funciones', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi jefe tiene en cuenta mis puntos de vista y opiniones',
'Likert', 38, 4, TRUE, 'Liderazgo', 'Escasa claridad de funciones', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi jefe me comunica a tiempo la información relacionada con el trabajo',
'Likert', 39, 4, TRUE, 'Liderazgo', 'Escasa claridad de funciones', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'La orientación que me da mi jefe me ayuda a realizar mejor mi trabajo',
'Likert', 40, 4, TRUE, 'Liderazgo', 'Escasa claridad de funciones', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi jefe ayuda a solucionar los problemas que se presentan en el trabajo',
'Likert', 41, 4, TRUE, 'Liderazgo', 'Escasa claridad de funciones', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Puedo confiar en mis compañeros de trabajo',
'Likert', 42, 4, TRUE, 'Relaciones en el trabajo', 'Relaciones sociales en el trabajo', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Entre compañeros solucionamos los problemas de trabajo de forma respetuosa',
'Likert', 43, 4, TRUE, 'Relaciones en el trabajo', 'Relaciones sociales en el trabajo', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En mi trabajo me hacen sentir parte del grupo',
'Likert', 44, 4, TRUE, 'Relaciones en el trabajo', 'Relaciones sociales en el trabajo', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Cuando tenemos que realizar trabajo de equipo los compañeros colaboran',
'Likert', 45, 4, TRUE, 'Relaciones en el trabajo', 'Relaciones sociales en el trabajo', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mis compañeros de trabajo me ayudan cuando tengo dificultades',
'Likert', 46, 4, TRUE, 'Relaciones en el trabajo', 'Relaciones sociales en el trabajo', 
'Liderazgo y relaciones en el trabajo', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me informan sobre lo que hago bien en mi trabajo',
'Likert', 47, 4, TRUE, 'Reconocimiento del desempeño', 'Reconocimiento del desempeño', 
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'La forma como evalúan mi trabajo en mi centro de trabajo me ayuda a mejorar mi desempeño',
'Likert', 48, 4, TRUE, 'Reconocimiento del desempeño', 'Reconocimiento del desempeño', 
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En mi centro de trabajo me pagan a tiempo mi salario',
'Likert', 49, 4, TRUE, 'Insuficiente sentido de pertenencia e inestabilidad', 'Falta de reconocimiento y recompensas',
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'El pago que recibo es el que merezco por el trabajo que realizo',
'Likert', 50, 4, TRUE, 'Insuficiente sentido de pertenencia e inestabilidad', 'Falta de reconocimiento y recompensas',
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Si obtengo los resultados esperados en mi trabajo me recompensan o reconocen',
'Likert', 51, 4, TRUE, 'Insuficiente sentido de pertenencia e inestabilidad', 'Falta de reconocimiento y recompensas',
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Las personas que hacen bien el trabajo pueden crecer laboralmente',
'Likert', 52, 4, TRUE, 'Insuficiente sentido de pertenencia e inestabilidad', 'Falta de reconocimiento y recompensas',
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Considero que mi trabajo es estable',
'Likert', 53, 4, TRUE, 'Insuficiente sentido de pertenencia e inestabilidad', 'Inestabilidad laboral',
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En mi trabajo existe continua rotación de personal',
'Likert', 54, 4, TRUE, 'Insuficiente sentido de pertenencia e inestabilidad', 'Inestabilidad laboral',
'Recompensas', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Siento orgullo de laborar en este centro de trabajo',
'Likert', 55, 4, TRUE, 'Orgullo por la empresa', 'Insuficiente sentido de pertenencia e inestabilidad',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me siento comprometido con mi trabajo',
'Likert', 56, 4, TRUE, 'Orgullo por la empresa', 'Insuficiente sentido de pertenencia e inestabilidad',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'En mi trabajo puedo expresarme libremente sin interrupciones',
'Likert', 57, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Recibo críticas constantes a mi persona y/o trabajo',
'Likert', 58, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Recibo burlas, calumnias, difamaciones, humillaciones o ridiculizaciones',
'Likert', 59, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Se ignora mi presencia o se me excluye de las reuniones de trabajo y en la toma de decisiones',
'Likert', 60, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Se manipulan las situaciones de trabajo para hacerme parecer un mal trabajador',
'Likert', 61, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Se ignoran mis éxitos laborales y se atribuyen a otros trabajadores',
'Likert', 62, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Me bloquean o impiden las oportunidades que tengo para obtener ascenso o mejora en mi trabajo',
'Likert', 63, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'He presenciado actos de violencia en mi centro de trabajo',
'Likert', 64, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Atiendo clientes o usuarios muy enojados',
'Likert', 65, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo me exige atender personas muy necesitadas de ayuda o enfermas',
'Likert', 66, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Para hacer mi trabajo debo demostrar sentimientos distintos a los míos',
'Likert', 67, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Mi trabajo me exige atender situaciones de violencia',
'Likert', 68, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Comunican tarde los asuntos de trabajo',
'Likert', 69, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Dificultan el logro de los resultados del trabajo',
'Likert', 70, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Cooperan poco cuando se necesita',
'Likert', 71, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna'),

(@id_cuestionario, 'Ignoran las sugerencias para mejorar su trabajo',
'Likert', 72, 4, TRUE, 'Relaciones sociales en el trabajo', 'Violencia laboral',
'Entorno organizacional', 'Trabajador', NULL, 'Ninguna');

--  ASIGNAR OPCIONES A TODAS LAS PREGUNTAS
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

-- =======================================
-- RANGOS DE INTERPRETACIÓN NOM-035
-- =======================================
SET @id_cuestionario := (SELECT id_cuestionario FROM Cuestionario ORDER BY id_cuestionario DESC LIMIT 1);

-- === CONDICIONES EN EL AMBIENTE DE TRABAJO ===
INSERT INTO Regla_Calificacion (id_cuestionario, dimension, rango_inferior, rango_superior, nivel_riesgo, descripcion) VALUES
(@id_cuestionario, 'Condiciones del ambiente de trabajo', 0, 5, 'Nulo', 'No existen condiciones adversas en el ambiente.'),
(@id_cuestionario, 'Condiciones del ambiente de trabajo', 6, 9, 'Bajo', 'Se deben mejorar pequeñas condiciones ambientales.'),
(@id_cuestionario, 'Condiciones del ambiente de trabajo', 10, 13, 'Medio', 'Se deben tomar acciones preventivas.'),
(@id_cuestionario, 'Condiciones del ambiente de trabajo', 14, 17, 'Alto', 'Requiere acciones correctivas inmediatas.'),
(@id_cuestionario, 'Condiciones del ambiente de trabajo', 18, 20, 'Muy alto', 'Existe riesgo importante para la salud.');

-- === CARGA DE TRABAJO ===
INSERT INTO Regla_Calificacion VALUES
(NULL, @id_cuestionario, 'Carga de trabajo', 0, 9, 'Nulo', 'No representa riesgo.'),
(NULL, @id_cuestionario, 'Carga de trabajo', 10, 19, 'Bajo', 'Nivel bajo de riesgo.'),
(NULL, @id_cuestionario, 'Carga de trabajo', 20, 29, 'Medio', 'Requiere atención preventiva.'),
(NULL, @id_cuestionario, 'Carga de trabajo', 30, 39, 'Alto', 'Se deben tomar acciones correctivas.'),
(NULL, @id_cuestionario, 'Carga de trabajo', 40, 80, 'Muy alto', 'Riesgo grave para la salud.');

-- === FALTA DE CONTROL SOBRE EL TRABAJO ===
INSERT INTO Regla_Calificacion VALUES
(NULL, @id_cuestionario, 'Falta de control sobre el trabajo', 0, 14, 'Nulo', 'No hay falta de control.'),
(NULL, @id_cuestionario, 'Falta de control sobre el trabajo', 15, 29, 'Bajo', 'Ligero nivel de falta de control.'),
(NULL, @id_cuestionario, 'Falta de control sobre el trabajo', 30, 44, 'Medio', 'Se requiere atención preventiva.'),
(NULL, @id_cuestionario, 'Falta de control sobre el trabajo', 45, 59, 'Alto', 'Debe atenderse con prioridad.'),
(NULL, @id_cuestionario, 'Falta de control sobre el trabajo', 60, 80, 'Muy alto', 'Riesgo grave por falta de control.');

-- === INTERFERENCIA TRABAJO-FAMILIA ===
INSERT INTO Regla_Calificacion VALUES
(NULL, @id_cuestionario, 'Interferencia en la relación trabajo–familia', 0, 4, 'Nulo', 'Sin interferencia.'),
(NULL, @id_cuestionario, 'Interferencia en la relación trabajo–familia', 5, 8, 'Bajo', 'Poca interferencia.'),
(NULL, @id_cuestionario, 'Interferencia en la relación trabajo–familia', 9, 12, 'Medio', 'Moderada interferencia.'),
(NULL, @id_cuestionario, 'Interferencia en la relación trabajo–familia', 13, 16, 'Alto', 'Alta interferencia.'),
(NULL, @id_cuestionario, 'Interferencia en la relación trabajo–familia', 17, 20, 'Muy alto', 'Interferencia grave.');

-- === LIDERAZGO Y RELACIONES EN EL TRABAJO ===
INSERT INTO Regla_Calificacion VALUES
(NULL, @id_cuestionario, 'Liderazgo', 0, 9, 'Nulo', 'No representa riesgo.'),
(NULL, @id_cuestionario, 'Liderazgo', 10, 18, 'Bajo', 'Riesgo bajo en liderazgo.'),
(NULL, @id_cuestionario, 'Liderazgo', 19, 27, 'Medio', 'Riesgo medio en liderazgo.'),
(NULL, @id_cuestionario, 'Liderazgo', 28, 36, 'Alto', 'Riesgo alto en liderazgo.'),
(NULL, @id_cuestionario, 'Liderazgo', 37, 48, 'Muy alto', 'Riesgo muy alto en liderazgo y relaciones.');

-- === RELACIONES EN EL TRABAJO ===
INSERT INTO Regla_Calificacion VALUES
(NULL, @id_cuestionario, 'Relaciones sociales en el trabajo', 0, 10, 'Nulo', 'No hay conflicto laboral.'),
(NULL, @id_cuestionario, 'Relaciones sociales en el trabajo', 11, 20, 'Bajo', 'Bajo nivel de conflicto.'),
(NULL, @id_cuestionario, 'Relaciones sociales en el trabajo', 21, 30, 'Medio', 'Moderado nivel de conflicto.'),
(NULL, @id_cuestionario, 'Relaciones sociales en el trabajo', 31, 40, 'Alto', 'Conflicto alto.'),
(NULL, @id_cuestionario, 'Relaciones sociales en el trabajo', 41, 72, 'Muy alto', 'Conflicto grave entre compañeros.');

-- === RECOMPENSAS ===
INSERT INTO Regla_Calificacion VALUES
(NULL, @id_cuestionario, 'Reconocimiento del desempeño', 0, 6, 'Nulo', 'Reconocimiento adecuado.'),
(NULL, @id_cuestionario, 'Reconocimiento del desempeño', 7, 12, 'Bajo', 'Leve deficiencia en el reconocimiento.'),
(NULL, @id_cuestionario, 'Reconocimiento del desempeño', 13, 18, 'Medio', 'Debe mejorarse el reconocimiento.'),
(NULL, @id_cuestionario, 'Reconocimiento del desempeño', 19, 24, 'Alto', 'Deficiencia importante en el reconocimiento.'),
(NULL, @id_cuestionario, 'Reconocimiento del desempeño', 25, 32, 'Muy alto', 'Grave falta de reconocimiento.');

-- === VIOLENCIA LABORAL ===
INSERT INTO Regla_Calificacion VALUES
(NULL, @id_cuestionario, 'Violencia laboral', 0, 10, 'Nulo', 'No hay evidencia de violencia.'),
(NULL, @id_cuestionario, 'Violencia laboral', 11, 20, 'Bajo', 'Casos aislados de violencia.'),
(NULL, @id_cuestionario, 'Violencia laboral', 21, 30, 'Medio', 'Requiere atención preventiva.'),
(NULL, @id_cuestionario, 'Violencia laboral', 31, 40, 'Alto', 'Requiere atención inmediata.'),
(NULL, @id_cuestionario, 'Violencia laboral', 41, 72, 'Muy alto', 'Grave violencia laboral.');

