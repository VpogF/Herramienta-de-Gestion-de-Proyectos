<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

$id_proyecto = $_SESSION["id_proyecto"];


$conexion = openBd();

// Columnas explícitas (antes select * enviaba el hash de la contraseña del encargado)
// left join con usuario: id_user admite NULL y las tareas sin encargado también deben salir
$sentenciaText = "select tarea.id_tarea, tarea.nom_tarea, tarea.descripcion, tarea.fecha_ini, tarea.fecha_fin,
    tarea.id_estado, estado.nom_estado, tarea.id_tipo, tipo.nom_tipo, tarea.id_user, usuario.nom_user
from tarea
inner join estado on tarea.id_estado = estado.id_estado
inner join tipo on tarea.id_tipo = tipo.id_tipo
left join usuario on tarea.id_user = usuario.id_user
where id_proyecto = :id_proyecto
order by tarea.fecha_fin is null, tarea.fecha_fin, tarea.id_tarea";

$sentencia = $conexion->prepare($sentenciaText);
$sentencia->bindParam(':id_proyecto', $id_proyecto);
$sentencia->execute();
$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultado);

?>