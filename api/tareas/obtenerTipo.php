<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

$conexion = openBd();

$sentenciaText = "select * from tipo";

$sentencia = $conexion->prepare($sentenciaText);

$sentencia->execute();
$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultado);

?>