<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

$id_proyecto = $_GET['id_proyecto'];

$conexion = openBd();

$sentenciaText = "select id_user, nom_user from usuario
where id_user not in (
select id_user from tener where id_proyecto=:id_proyecto
)";

$sentencia = $conexion->prepare($sentenciaText);
$sentencia->bindParam(':id_proyecto', $id_proyecto);
$sentencia->execute();
$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultado);

?>