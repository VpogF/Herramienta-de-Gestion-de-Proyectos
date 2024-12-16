<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

$id_proyecto = $_SESSION["id_proyecto"];


$conexion = openBd();

$sentenciaText = "select * from tarea
inner join estado on tarea.id_estado = estado.id_estado
inner join tipo on tarea.id_tipo = tipo.id_tipo
inner join usuario on tarea.id_user = usuario.id_user
where id_proyecto = :id_proyecto";

$sentencia = $conexion->prepare($sentenciaText);
$sentencia->bindParam(':id_proyecto', $id_proyecto);
$sentencia->execute();
$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultado);

?>