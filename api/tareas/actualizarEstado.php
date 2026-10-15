<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

// Recibe {"id_tarea": 3, "id_estado": 2} al mover una tarea de columna
$datos = json_decode(file_get_contents('php://input'), true);

$id_user = $_SESSION["user_id"] ?? null;
$id_proyecto = $_SESSION["id_proyecto"] ?? null;
$id_tarea = isset($datos['id_tarea']) ? (int) $datos['id_tarea'] : 0;
$id_estado = isset($datos['id_estado']) ? (int) $datos['id_estado'] : 0;

if (!$id_user || !$id_proyecto || $id_tarea <= 0 || $id_estado <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit();
}

$conexion = openBd();

// Solo se actualiza si la tarea es del proyecto abierto y el usuario es miembro de ese proyecto
$sentenciaText = "update tarea set id_estado = :id_estado
where id_tarea = :id_tarea
  and id_proyecto = :id_proyecto
  and exists (select 1 from tener where tener.id_proyecto = :id_proyecto2 and tener.id_user = :id_user)
  and exists (select 1 from estado where estado.id_estado = :id_estado2)";

$sentencia = $conexion->prepare($sentenciaText);
$sentencia->bindParam(':id_estado', $id_estado);
$sentencia->bindParam(':id_estado2', $id_estado);
$sentencia->bindParam(':id_tarea', $id_tarea);
$sentencia->bindParam(':id_proyecto', $id_proyecto);
$sentencia->bindParam(':id_proyecto2', $id_proyecto);
$sentencia->bindParam(':id_user', $id_user);
$sentencia->execute();

// rowCount es 0 tanto si no hay permiso como si el estado ya era ese: comprobamos cuál es
$comprobar = $conexion->prepare("select id_estado from tarea where id_tarea = :id_tarea and id_proyecto = :id_proyecto");
$comprobar->bindParam(':id_tarea', $id_tarea);
$comprobar->bindParam(':id_proyecto', $id_proyecto);
$comprobar->execute();
$fila = $comprobar->fetch(PDO::FETCH_ASSOC);

// (int) en ambos lados: bindParam enlaza por referencia y PDO deja $id_estado convertido en string
if (!$fila || (int) $fila['id_estado'] !== (int) $id_estado) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No se pudo mover la tarea']);
    exit();
}

echo json_encode(['ok' => true]);
