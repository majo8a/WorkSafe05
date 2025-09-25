<?php
// conexion.php

$host = "localhost";     // Servidor de base de datos
$usuario = "root";       // Usuario de la BD
$password = "";          // Contraseña (ajústala si tienes)
$base_datos = "NOM035DB"; // Nombre de la base de datos

// Crear conexión MySQLi
$db = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($db->connect_errno) {
  die(json_encode([
    "status" => "error",
    "message" => "Error de conexión a la base de datos: " . $db->connect_error
  ]));
}

// Para que acepte caracteres especiales (UTF-8)
$db->set_charset("utf8");
