<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_pregunta, id_cuestionario, texto_pregunta, tipo_calificacion, orden, puntaje_maximo, obligatoria, dimension, dominio, categoria, grupo_aplicacion, id_pregunta_dependeDe, condicion FROM Pregunta");
$stmt->execute();
$stmt->bind_result($id_pregunta, $id_cuestionario, $texto_pregunta, $tipo_calificacion, $orden, $puntaje_maximo, $obligatoria, $dimension, $dominio, $categoria, $grupo_aplicacion, $id_pregunta_dependeDe, $condicion);

$arr = array();
while ($stmt->fetch()) {
  $arr[] = array(
    'id_pregunta' => $id_pregunta,
    'id_cuestionario' => $id_cuestionario,
    'texto_pregunta' => $texto_pregunta,
    'tipo_calificacion' => $tipo_calificacion,
    'orden' => $orden,
    'puntaje_maximo' => $puntaje_maximo,
    'obligatoria' => $obligatoria,
    'dimension' => $dimension,
    'dominio' => $dominio,
    'categoria' => $categoria,
    'grupo_aplicacion' => $grupo_aplicacion,
    'id_pregunta_dependeDe' => $id_pregunta_dependeDe,
    'condicion' => $condicion
  );
}

$stmt->close();
echo json_encode($arr);
