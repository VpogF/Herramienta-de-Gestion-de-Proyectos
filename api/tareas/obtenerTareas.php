<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

$id_proyecto = $_SESSION["id_proyecto"];


$conexion = openBd();

$sentenciaText = "select * from tarea where id_proyecto = :id_proyecto";

$sentencia = $conexion->prepare($sentenciaText);
$sentencia->bindParam(':id_proyecto', $id_proyecto);
$sentencia->execute();
$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultado);

?>